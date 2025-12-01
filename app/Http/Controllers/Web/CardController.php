<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index()
    {
        $cards = Card::orderBy('id', 'desc')->get();
        return view('cards.list', compact('cards'));
    }

    public function create()
    {
        return view('cards.create');
    }

    public function store(Request $request)
    {
        Card::create([
            'holder'     => $request->input('holder'),
            'number'     => $request->input('number'),
            'exp_month'  => $request->input('exp_month'),
            'exp_year'   => $request->input('exp_year'),
            'cvv'        => $request->input('cvv'),
            'bank'       => $request->input('bank'),
            'active'     => true,
        ]);

        return redirect('/cards')->with('success', 'Card added');
    }

    public function delete($id)
    {
        Card::where('id', $id)->delete();
        return redirect('/cards')->with('success', 'Card deleted');
    }
}
