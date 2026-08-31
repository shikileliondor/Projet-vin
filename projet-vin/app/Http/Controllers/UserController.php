<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\SaveUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->isAdmin(), 403);

        return Inertia::render('users/index', [
            'users' => User::query()->orderBy('name')->get()->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'role_label' => $user->role->label(),
                'is_active' => $user->is_active,
            ]),
            'roles' => collect(UserRole::cases())->map(fn (UserRole $role) => [
                'value' => $role->value,
                'label' => $role->label(),
            ]),
        ]);
    }

    public function store(SaveUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Str::password(32);
        $data['email_verified_at'] = now();
        User::create($data);

        return back()->with('success', 'Utilisateur ajouté.');
    }

    public function update(SaveUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if ($request->user()->is($user) && array_key_exists('is_active', $data) && ! $data['is_active']) {
            throw ValidationException::withMessages(['is_active' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        $removesLastAdmin = $user->isAdmin()
            && (($data['role'] ?? null) !== UserRole::Admin->value || ! ($data['is_active'] ?? true))
            && User::query()->where('role', UserRole::Admin)->where('is_active', true)->count() === 1;

        if ($removesLastAdmin) {
            throw ValidationException::withMessages(['role' => 'Au moins un administrateur actif est requis.']);
        }

        if (empty($data['pin'])) {
            unset($data['pin']);
        }

        $user->update($data);

        return back()->with('success', 'Utilisateur modifié.');
    }
}
