<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Proxy;

class ProxyController extends Controller
{
    public function index()
    {
        $proxies = Proxy::orderBy('id', 'desc')->get();

        return view('proxies.list', [
            'proxies' => $proxies
        ]);
    }

    public function create()
    {
        return view('proxies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'protocol' => 'required|string',
            'ip'       => 'required|string',
            'port'     => 'required|integer',
            'login'    => 'nullable|string',
            'password' => 'nullable|string',
        ]);

        Proxy::create([
            'protocol' => $request->protocol,
            'login'    => $request->login,
            'password' => $request->password,
            'ip'       => $request->ip,
            'port'     => $request->port,
            'active'   => true,
        ]);

        return redirect('/proxies')->with('success', 'Proxy added successfully');
    }
}
