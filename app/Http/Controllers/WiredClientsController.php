<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddWiredClientRequest;
use App\Models\MicroTik;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\WiredClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use RouterOS;
use RouterOS\Config;
use RouterOS\Query;
use Illuminate\Support\Str;

class WiredClientsController extends Controller
{
    public $client;
    public $current_microtik_id;
    public $session_data;


    public function __construct()
    {
        return $this->middleware(['auth', 'auth_router']);
    }
    public function connection()
    {
        $config = new Config;
        $data = session()->get('router_session');
        $this->session_data = $data;
        $c = $config->set('timeout', 1)
            ->set('host', $data['ip'])
            ->set('user',  $data['username'])
            ->set('pass', $data['password'])
            ->set('port', intval($data['port']));
        try {
            $this->client = new RouterOS\Client($c);
        } catch (\Exception $e) {
            session()->forget('router_session');
            return redirect(route('router_login'))->with('error_message', 'Router Disconnected! Please Check if its connected');
        }
    }

    public function index()
    {
        $users = collect($this->getClients());
        // dd($users);
        return view('wired_clients.index', compact('users'));
    }
    public function create()
    {
        $suggested = $this->suggestIp();

        $interface = new Query('/interface/print');
        $this->connection();
        $interfaces = collect($this->client->query($interface)->read());
        $packages = Package::orderBy('created_at', 'desc')->get();
        return view('wired_clients.create', compact('suggested', 'interfaces', 'packages'));
    }

    public function getClients()
    {
        $query = new Query('/ip/address/print');
        $this->connection();
        $users = $this->client->query($query)->read();
        // dd($users);
        return $users;
    }
    public function store(AddWiredClientRequest $request)
    {
        $p = Package::findorfail($request->package_id);
        $u = User::create([
            'name' => ucfirst($request->client_name),
            'email' => $request->email == null ? strtoupper(Str::random(15)) : $request->email,
            'password' => Hash::make('asewireless_auth'),
        ]);
        $client = WiredClient::create([
            'user_id' => $u->id,
            'location' => 'Kamakamega',
            'package_id' => $p->package_id,
            'ip_address' => $request->ip
        ]);

        // create an entry in address list
        $this->addAddress($client, $request->lan);
        // create entry in firewall nat and mangle
        $this->firewallNat($client, $request->wan);
        // create an entry in queues
        $this->addQueue($client, $p);

        return redirect(route('wired_clients.index'))->with('success', 'Client Added Successfully');
    }

    public function addAddress(WiredClient $client, $interface)
    {
        $this->connection();
        $n = explode('.', $client->ip_address);
        $newIp = $n[0] . '.' . $n[1] . '.' . $n[2] . '.' . '1';
        $s = $newIp . '/30';
        // dd($s);
        $query = (new RouterOs\Query('/ip/address/add'))
            ->equal('address', $s)
            ->equal('interface', $interface)
            ->equal('comment', $client->user->name);
        $this->client->query($query)->read();
        return;
    }
    public function firewallNat(WiredClient $wired_client,  $out_intf)
    {
        // ADD NAT Rule
        $this->connection();
        $n = explode('.', $wired_client->ip_address);
        $newIp = $n[0] . '.' . $n[1] . '.' . $n[2] . '.' . '1';
        $s = $newIp . '/30';
        $query = (new RouterOs\Query('/ip/firewall/nat/add'))
            ->equal('chain', 'srcnat')
            ->equal('src-address', $s)
            ->equal('action', 'masquerade')
            ->equal('out-interface', $out_intf)
            ->equal('comment', $wired_client->user->name);
        $this->connection();
        $this->client->query($query)->read();


        // ADD MANGLE RULE
        $query = (new RouterOs\Query('/ip/firewall/mangle/add'))
            ->equal('chain', 'prerouting')
            ->equal('src-address', $s)
            ->equal('action', 'mark-routing')
            ->equal('new-routing-mark', $out_intf)
            ->equal('comment', $wired_client->user->name);
        $this->client->query($query)->read();
        return;
    }

    public function addQueue(WiredClient $wired_client, Package $p)
    {
        // $wired_client->ip_address;
        $s =  explode('.', $wired_client->ip_address);
        $newIp = $s[0] . '.' . $s[1] . '.' . $s[2] . '.' . '2';

        $this->connection();
        $query = (new RouterOs\Query('/ip/firewall/mangle/add'))
            ->equal('name', $wired_client->user->name)
            ->equal('target', $newIp)
            ->equal('max-limit', $p->rate)
            ->equal('comment', $wired_client->user->name);
        $this->client->query($query)->read();
        return;
    }
    public function suggestIp()
    {
        $ip =  mt_rand(0, 255) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255) . ".1";
        $user = WiredClient::where('ip_address', $ip)->first();
        if ($user == null) {
            return $ip;
        } else {
            $this->suggestIp();
        }
    }
}
