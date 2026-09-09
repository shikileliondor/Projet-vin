<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
            'role' => ['required', Rule::enum(UserRole::class)],
            'pin' => [Rule::requiredIf($this->route('user') === null), 'nullable', 'digits:4'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->has('pin') || ! is_string($this->input('pin'))) {
                return;
            }

            $user = $this->route('user');
            $users = User::query();

            if ($user instanceof User) {
                $users->whereKeyNot($user->id);
            }

            $pinAlreadyUsed = $users
                ->get(['pin'])
                ->contains(fn (User $existingUser) => $existingUser->pin
                    && Hash::check($this->string('pin')->toString(), $existingUser->pin));

            if ($pinAlreadyUsed) {
                $validator->errors()->add('pin', 'Ce code PIN est déjà utilisé par un autre utilisateur.');
            }
        }];
    }
}
