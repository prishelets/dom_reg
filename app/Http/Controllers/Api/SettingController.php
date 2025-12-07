<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getEmailDomain(Request $request)
    {
        $registrar = $request->query('registrar');

        if ($registrar !== 'dynadot.com') {
            return response()->json([
                'success' => false,
                'error'   => 'Unsupported registrar',
            ], 422);
        }

        $setting = Setting::where('name', 'dynadot_email_domains')->first();
        $raw = $setting?->value;

        $entries = collect(explode(',', (string) $raw))
            ->map(fn ($line) => trim($line))
            ->filter(fn ($line) => $line !== '')
            ->values();

        if ($entries->isEmpty()) {
            return response()->json([
                'success' => false,
                'error'   => 'No email domains configured',
            ], 404);
        }

        $selected = $entries->random();
        [$identifier, $domain] = array_pad(explode('|', $selected, 2), 2, null);

        return response()->json([
            'success'    => true,
            'entry'      => $selected,
            'identifier' => $identifier ? trim($identifier) : null,
            'domain'     => $domain ? trim($domain) : null,
        ]);
    }
}
