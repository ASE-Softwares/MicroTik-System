<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddWiredClientRequest;
use App\Http\Requests\SyncClientRequest;
use App\Models\MicroTik;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\WiredClient;
use Carbon\Carbon;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\Hash;
use RouterOS;
use RouterOS\Config;
use RouterOS\Query;
use Illuminate\Support\Str;
use RouterOS\Client;

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

        // dd($p);
        $u = User::create([
            'name' => ucfirst($request->client_name),
            'email' => $request->email == null ? strtoupper(Str::random(15)) : $request->email,
            'password' => Hash::make('asewireless_auth'),
        ]);

        $s =  explode('.', $request->ip);
        $newIp = $s[0] . '.' . $s[1] . '.' . $s[2] . '.' . '2';
        $client = WiredClient::create([
            'user_id' => $u->id,
            'location' => 'Kakamega',
            'package_id' => $p->id,
            'ip_address' => $newIp
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
        $query = (new RouterOs\Query('/queue/simple/add'))
            ->equal('name', $wired_client->user->name)
            ->equal('target', $newIp)
            ->equal('max-limit', $p->rate);
        $this->client->query($query)->read();
        return;
    }
    public function suggestIp()
    {
        $ip =  mt_rand(0, 230) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255) . ".1";
        $user = WiredClient::where('ip_address', $ip)->first();
        if ($user == null) {
            return $ip;
        } else {
            $this->suggestIp();
        }
    }

    public function toggleClient(Request $request)
    {
        $this->connection();

        if ($request->action == 'Enable') {
            $userId = $request->client['.id'];
            $query = new Query('/ip/address/enable');
            $query->equal('.id', $userId);
            $this->client->query($query)->read();

            $query2 = (new Query('/ip/address/print'))->where('.id', $userId);
            $result2 =  $this->client->query($query2)->read();
            $res = [
                'status' => $result2[0]['disabled'] == 'false' ? 'Success' : 'Failed',
                'action' => 'Enable',
                'current_state' => $result2[0]['disabled'] == 'false' ? 'Active' : 'Disabled'
            ];
            return response()->json($res, 201);
        } else {
            $userId = $request->client['.id'];
            $query = new Query('/ip/address/disable');
            $query->equal('.id', $userId);
            $this->client->query($query)->read();

            $query2 = (new Query('/ip/address/print'))->where('.id', $userId);
            $result2 =  $this->client->query($query2)->read();
            $res = [
                'status' => $result2[0]['disabled'] == 'true' ? 'Success' : 'Failed',
                'action' => 'Disable',
                'current_state' => $result2[0]['disabled'] == 'false' ? 'Active' : 'Disabled'
            ];
            return response()->json($res, 201);
        }
    }

    public function getNumber(array $req)
    {
        // dd($req);
        $query = new Query('/ip/address/find');
        $r = $this->client->query($query)->read();
        dd($r);
    }
    public function queueInfo($ip)
    {
        $s =  explode('.', $ip);
        $newIp = $s[0] . '.' . $s[1] . '.' . $s[2] . '.' . '1';
        $alternativeIp = $s[0] . '.' . $s[1] . '.' . $s[2] . '.' . '2';
        $client = WiredClient::where('ip_address',  $newIp)->orWhere('ip_address',  $alternativeIp)->first();
        if ($client != null) {
            $client->load(['user', 'package']);
        }

        $r = [
            'client' => $client,
            'queue' => $this->queryQueue($alternativeIp),
        ];

        return response()->json($r, 201);
    }

    public function queryQueue($alternativeIp)
    {
        $query = new RouterOS\Query('/queue/simple/print');
        $this->connection();
        $data = collect($this->client->query($query)->read());
        $result = $data->where('target', $alternativeIp . '/32')->first();
        return $result;
        // return $interfaces;
    }

    public function liteQueueInfo($ip)
    {
        $s =  explode('.', $ip);
        $newIp = $s[0] . '.' . $s[1] . '.' . $s[2] . '.' . '1';
        $alternativeIp = $s[0] . '.' . $s[1] . '.' . $s[2] . '.' . '2';


        $r =  $this->queryQueue($alternativeIp);


        return response()->json($r, 201);
    }

    public function newClient(SyncClientRequest $request)
    {
        // "ip" => "52.132.97.0"
        // "client_name" => "Lenovo Base Client"
        // "email" => "test@test.com"
        // "package_id" => 2

        $u =  User::create([
            'name' => ucwords($request->client_name),
            'email' => $request->email ? strtolower($request->email) : strtolower(str_replace(' ', '', $request->client_name)) . '@raosys.com',
            'password' => Hash::make('defaultpassword'),
        ]);
        $s =  explode('.', $request->ip);
        $ip = $s[0] . '.' . $s[1] . '.' . $s[2] . '.' . '2';
        $w =  WiredClient::create([
            'user_id' => $u->id,
            'location' => 'Kakamega',
            'package_id' => $request->package_id,
            'ip_address' => $ip
        ]);
        $w->load(['user', 'package']);
        return response()->json($w, 201);
    }
}
