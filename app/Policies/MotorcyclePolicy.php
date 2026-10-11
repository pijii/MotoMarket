<?php

namespace App\Policies;

use App\Models\Motorcycle;
use App\Models\User;

class MotorcyclePolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function view(User $user, Motorcycle $motorcycle): bool
    {
        return $motorcycle->seller_id === $user->id;
    }

    public function update(User $user, Motorcycle $motorcycle): bool
    {
        return $motorcycle->seller_id === $user->id && $user->isSeller();
    }

    public function delete(User $user, Motorcycle $motorcycle): bool
    {
        return $motorcycle->seller_id === $user->id && $user->isSeller();
    }
}