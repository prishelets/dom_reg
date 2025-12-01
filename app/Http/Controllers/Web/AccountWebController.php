<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Account;

class AccountWebController extends Controller
{
    public function list()
    {
        $accounts = Account::orderBy('id', 'desc')->get();

        return view('accounts.list', [
            'accounts' => $accounts
        ]);
    }

    public function delete($id)
    {
        $account = Account::find($id);

        if (!$account) {
            return redirect('/accounts')->with('error', 'Account not found');
        }

        $account->delete();

        return redirect('/accounts')->with('success', 'Account deleted');
    }
}
