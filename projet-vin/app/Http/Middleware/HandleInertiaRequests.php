<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => fn () => Setting::query()->where('key', 'business_name')->value('value') ?? config('app.name'),
            'currency' => fn () => Setting::query()->where('key', 'currency')->value('value') ?? 'FCFA',
            'auth' => [
                'user' => $user,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
            'permissions' => fn () => $user ? [
                'manageProducts' => $user->canManageProducts(),
                'recordEntries' => $user->canRecordEntries(),
                'recordExits' => $user->canRecordExits(),
                'manageInventories' => $user->canManageInventories(),
                'viewInventories' => $user->canViewInventories(),
                'viewMovements' => $user->canViewMovements(),
                'admin' => $user->isAdmin(),
            ] : null,
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
