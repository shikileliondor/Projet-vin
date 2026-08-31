<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function edit(Request $request): Response
    {
        abort_unless($request->user()->isAdmin(), 403);

        return Inertia::render('settings/application', [
            'settings' => [
                'business_name' => Setting::query()->where('key', 'business_name')->value('value') ?? 'WineStock',
                'currency' => Setting::query()->where('key', 'currency')->value('value') ?? 'FCFA',
            ],
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        foreach ($request->validated() as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Paramètres enregistrés.');
    }
}
