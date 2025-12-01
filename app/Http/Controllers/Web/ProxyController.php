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
}
