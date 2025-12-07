<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $intervalFrom = Setting::firstOrCreate(
            ['name' => 'dynadot_next_account_creation_interval_from'],
            ['value' => 5]
        );

        $intervalTo = Setting::firstOrCreate(
            ['name' => 'dynadot_next_account_creation_interval_to'],
            ['value' => 10]
        );

        $lastRun = Setting::firstOrCreate(
            ['name' => 'dynadot_next_account_creation_at'],
            ['value' => now()->toDateTimeString()]
        );

        $enabled = Setting::firstOrCreate(
            ['name' => 'dynadot_next_account_creation_enabled'],
            ['value' => 1]
        );

        $readyFrom = Setting::firstOrCreate(
            ['name' => 'dynadot_account_ready_interval_from'],
            ['value' => 1]
        );

        $readyTo = Setting::firstOrCreate(
            ['name' => 'dynadot_account_ready_interval_to'],
            ['value' => 2]
        );

        $checkFrom = Setting::firstOrCreate(
            ['name' => 'dynadot_account_check_interval_from'],
            ['value' => 1]
        );

        $checkTo = Setting::firstOrCreate(
            ['name' => 'dynadot_account_check_interval_to'],
            ['value' => 3]
        );

        $emailDomains = Setting::firstOrCreate(
            ['name' => 'dynadot_email_domains'],
            ['value' => '']
        );

        $emailDomainsValue = collect(explode(',', $emailDomains->value))
            ->map(fn ($line) => trim($line))
            ->filter(fn ($line) => $line !== '')
            ->implode("\n");
        $cloudflareNote = Setting::firstOrCreate(
            ['name' => 'cloudflare_note'],
            ['value' => '']
        );

        return view('settings.edit', [
            'interval_from' => (int) $intervalFrom->value,
            'interval_to'   => (int) $intervalTo->value,
            'last_run'      => $lastRun->value,
            'enabled'       => (bool) $enabled->value,
            'ready_from'    => (int) $readyFrom->value,
            'ready_to'      => (int) $readyTo->value,
            'check_from'    => (int) $checkFrom->value,
            'check_to'      => (int) $checkTo->value,
            'email_domains' => $emailDomainsValue,
            'cloudflare_note' => $cloudflareNote->value,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'interval_from' => 'required|integer|min:1|max:1440',
            'interval_to'   => 'required|integer|min:1|max:1440|gte:interval_from',
            'ready_from'    => 'required|integer|min:1|max:168',
            'ready_to'      => 'required|integer|min:1|max:168|gte:ready_from',
            'check_from'    => 'required|integer|min:1|max:168',
            'check_to'      => 'required|integer|min:1|max:168|gte:check_from',
            'email_domains' => 'nullable|string',
        ]);

        Setting::updateOrCreate(
            ['name' => 'dynadot_next_account_creation_interval_from'],
            ['value' => $data['interval_from']]
        );

        Setting::updateOrCreate(
            ['name' => 'dynadot_next_account_creation_interval_to'],
            ['value' => $data['interval_to']]
        );

        Setting::updateOrCreate(
            ['name' => 'dynadot_next_account_creation_enabled'],
            ['value' => $request->boolean('enable_schedule') ? 1 : 0]
        );

        Setting::updateOrCreate(
            ['name' => 'dynadot_account_ready_interval_from'],
            ['value' => $data['ready_from']]
        );

        Setting::updateOrCreate(
            ['name' => 'dynadot_account_ready_interval_to'],
            ['value' => $data['ready_to']]
        );

        Setting::updateOrCreate(
            ['name' => 'dynadot_account_check_interval_from'],
            ['value' => $data['check_from']]
        );

        Setting::updateOrCreate(
            ['name' => 'dynadot_account_check_interval_to'],
            ['value' => $data['check_to']]
        );

        $domains = collect(preg_split('/\r\n|\r|\n/', $request->input('email_domains', '')))
            ->map(fn ($line) => trim($line))
            ->filter(fn ($line) => $line !== '')
            ->values()
            ->implode(',');

        Setting::updateOrCreate(
            ['name' => 'dynadot_email_domains'],
            ['value' => $domains]
        );

        return redirect('/settings')->with('success', 'Settings updated');
    }

    public function updateCloudflare(Request $request)
    {
        $data = $request->validate([
            'cloudflare_note' => 'nullable|string',
        ]);

        Setting::updateOrCreate(
            ['name' => 'cloudflare_note'],
            ['value' => $data['cloudflare_note'] ?? '']
        );

        return redirect('/settings#tab-pane-cloudflare')->with('success', 'Cloudflare settings updated');
    }
}
