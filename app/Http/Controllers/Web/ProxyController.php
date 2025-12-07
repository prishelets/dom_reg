<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proxy;
use Illuminate\Validation\ValidationException;

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
        $validated = $request->validate([
            'default_protocol' => 'required|in:default,http,socks5',
            'proxies'          => 'required|string',
            'label'            => 'nullable|string|max:255',
        ]);

        $lines = preg_split('/\r\n|\r|\n/', trim($validated['proxies']));
        $lines = array_filter($lines, fn ($line) => trim($line) !== '');

        if (empty($lines)) {
            throw ValidationException::withMessages([
                'proxies' => 'Provide at least one proxy.',
            ]);
        }

        $created = 0;
        foreach ($lines as $line) {
            $proxyData = $this->parseProxyLine($line, $validated['default_protocol']);
            $payload = $proxyData + [
                'active' => false,
                'label'  => $validated['label'] ?? null,
            ];
            Proxy::create($payload);
            $created++;
        }

        return redirect('/proxies')->with('success', "Added {$created} proxies.");
    }

    public function destroy($id)
    {
        $proxy = Proxy::find($id);

        if (!$proxy) {
            return redirect('/proxies')->with('error', 'Proxy not found.');
        }

        $proxy->delete();

        return redirect('/proxies')->with('success', 'Proxy deleted.');
    }

    public function destroyAll()
    {
        Proxy::query()->delete();

        return redirect('/proxies')->with('success', 'All proxies deleted.');
    }

    private function parseProxyLine(string $line, string $defaultProtocol): array
    {
        $pattern = '/^(?:(?P<protocol>socks5):\/\/)?(?:(?P<login>[^:@\s]+):(?P<password>[^@:\s]+)@)?(?P<ip>\d{1,3}(?:\.\d{1,3}){3}):(?P<port>\d{2,5})$/i';
        $line = trim($line);

        if (!preg_match($pattern, $line, $matches)) {
            throw ValidationException::withMessages([
                'proxies' => "Invalid proxy format: {$line}",
            ]);
        }

        $protocol = $matches['protocol'] ?? null;
        $protocol = $protocol
            ? strtolower($protocol)
            : ($defaultProtocol === 'default' ? 'http' : $defaultProtocol);

        $port = (int) $matches['port'];
        if ($port < 1 || $port > 65535) {
            throw ValidationException::withMessages([
                'proxies' => "Invalid port for proxy: {$line}",
            ]);
        }

        return [
            'protocol' => $protocol,
            'login'    => $matches['login'] ?? null,
            'password' => $matches['password'] ?? null,
            'ip'       => $matches['ip'],
            'port'     => $port,
        ];
    }
}
