<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Motorcycle;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'usersCount' => User::count(),
            'sellersCount' => User::where('role', 'seller')->count(),
            'buyersCount' => User::where('role', 'buyer')->count(),
            'listingCounts' => Motorcycle::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'pendingReports' => Report::where('status', 'pending')->count(),
            'recentListings' => Motorcycle::with('seller')->latest()->take(8)->get(),
        ]);
    }

    public function motorcycles(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:pending,approved,rejected,sold,removed'],
            'condition' => ['nullable', 'in:new,used'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $query = Motorcycle::with(['seller', 'category'])->latest();
        if (! empty($filters['status'])) $query->where('status', $filters['status']);
        if (! empty($filters['condition'])) $query->where('condition', $filters['condition']);
        if (! empty($filters['category'])) $query->where('category_id', $filters['category']);
        if (! empty($filters['q'])) {
            $term = $filters['q'];
            $query->where(fn ($builder) => $builder->where('brand', 'like', "%{$term}%")
                ->orWhere('model', 'like', "%{$term}%")
                ->orWhereHas('seller', fn ($seller) => $seller->where('name', 'like', "%{$term}%")));
        }

        return view('admin.motorcycles', [
            'motorcycles' => $query->paginate(20)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function approve(Motorcycle $motorcycle): RedirectResponse
    {
        abort_unless(in_array($motorcycle->status, ['pending', 'rejected'], true), 403);
        $motorcycle->update(['status' => 'approved', 'rejection_reason' => null]);

        return back()->with('status', 'Listing approved and published.');
    }

    public function reject(Request $request, Motorcycle $motorcycle): RedirectResponse
    {
        abort_unless(in_array($motorcycle->status, ['pending', 'approved'], true), 403);
        $data = $request->validate(['rejection_reason' => ['nullable', 'string', 'max:2000']]);
        $motorcycle->update(['status' => 'rejected', 'rejection_reason' => $data['rejection_reason'] ?? null]);

        return back()->with('status', 'Listing rejected.');
    }

    public function remove(Motorcycle $motorcycle): RedirectResponse
    {
        abort_if($motorcycle->status === 'removed', 403);
        $motorcycle->update(['status' => 'removed']);

        return back()->with('status', 'Listing removed from public view.');
    }

    public function users(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'in:buyer,seller,admin'],
        ]);
        $query = User::query()->latest();
        if (! empty($filters['role'])) $query->where('role', $filters['role']);
        if (! empty($filters['q'])) {
            $term = $filters['q'];
            $query->where(fn ($builder) => $builder->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
        }

        return view('admin.users', ['users' => $query->paginate(20)->withQueryString(), 'filters' => $filters]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', 'in:buyer,seller,admin']]);
        abort_if($user->is(auth()->user()), 403, 'You cannot change your own administrator role.');
        $user->forceFill(['role' => $data['role']])->save();

        return back()->with('status', 'Account role updated.');
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        abort_if($user->is(auth()->user()) && ! $data['is_active'], 403, 'You cannot suspend your own account.');
        $user->forceFill(['is_active' => (bool) $data['is_active']])->save();

        return back()->with('status', $data['is_active'] ? 'Account reactivated.' : 'Account suspended.');
    }

    public function reports(Request $request): View
    {
        $filters = $request->validate(['status' => ['nullable', 'in:pending,reviewed,resolved,dismissed']]);

        return view('admin.reports', [
            'reports' => $this->reportQuery($filters)->paginate(20)->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function exportReports(Request $request): StreamedResponse
    {
        $filters = $request->validate(['status' => ['nullable', 'in:pending,reviewed,resolved,dismissed']]);

        return response()->streamDownload(function () use ($filters): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'Report ID', 'Status', 'Reason', 'Description', 'Reporter ID', 'Reporter Name',
                'Reporter Email', 'Motorcycle ID', 'Motorcycle', 'Reported User ID',
                'Reported User Name', 'Reported User Email', 'Submitted At', 'Updated At',
            ]);

            $this->reportQuery($filters)->chunk(500, function ($reports) use ($output): void {
                foreach ($reports as $report) {
                    $fields = [
                        $report->id,
                        $report->status,
                        $report->reason,
                        $report->description,
                        $report->reporter?->id,
                        $report->reporter?->name,
                        $report->reporter?->email,
                        $report->motorcycle?->id,
                        $report->motorcycle?->display_name,
                        $report->reportedUser?->id,
                        $report->reportedUser?->name,
                        $report->reportedUser?->email,
                        $report->created_at?->toIso8601String(),
                        $report->updated_at?->toIso8601String(),
                    ];

                    fputcsv($output, array_map($this->spreadsheetSafeValue(...), $fields));
                }
            });

            fclose($output);
        }, 'motomarket-reports-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function updateReport(Request $request, Report $report): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:reviewed,resolved,dismissed']]);
        $report->update(['status' => $data['status']]);

        return back()->with('status', 'Report status updated.');
    }

    private function reportQuery(array $filters)
    {
        return Report::with(['reporter', 'motorcycle', 'reportedUser'])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest();
    }

    private function spreadsheetSafeValue(mixed $value): string
    {
        $value = (string) ($value ?? '');

        return preg_match('/^[\x00-\x20]*[=+\-@]/', $value) ? "'".$value : $value;
    }
}