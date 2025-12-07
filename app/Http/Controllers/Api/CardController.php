<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
    public function get()
    {
        $card = null;

        DB::transaction(function () use (&$card) {
            $card = Card::orderByRaw("COALESCE(card_last_used_at, '1970-01-01 00:00:00') asc")
                ->lockForUpdate()
                ->first();

            if ($card) {
                $card->card_last_used_at = now();
                $card->save();
            }
        }, 3);

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'No cards available',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'card'    => $card,
        ]);
    }
}
