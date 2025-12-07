<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index()
    {
        $cards = Card::orderByRaw('card_last_used_at DESC NULLS LAST')
            ->orderByDesc('id')
            ->get();
        return view('cards.list', compact('cards'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'holder'    => 'nullable|string|max:255',
            'number'    => 'required|string|max:32',
            'exp_month' => ['required', 'regex:/^(0?[1-9]|1[0-2])$/'],
            'exp_year'  => ['required', 'regex:/^(\d{2}|\d{4})$/'],
            'cvv'       => 'required|string|max:4',
            'label'     => 'nullable|string|max:255',
        ]);

        $month = str_pad((string) $data['exp_month'], 2, '0', STR_PAD_LEFT);
        $yearInput = (string) $data['exp_year'];
        $year = strlen($yearInput) === 4
            ? substr($yearInput, -2)
            : str_pad($yearInput, 2, '0', STR_PAD_LEFT);

        Card::create([
            'holder'    => $data['holder'] ?? '',
            'number'    => $data['number'],
            'exp_month' => $month,
            'exp_year'  => $year,
            'cvv'       => $data['cvv'],
            'label'     => $data['label'] ?? null,
        ]);

        return redirect('/cards')->with('success', 'Card added');
    }

    public function delete($id)
    {
        Card::where('id', $id)->delete();
        return redirect('/cards')->with('success', 'Card deleted');
    }
}
