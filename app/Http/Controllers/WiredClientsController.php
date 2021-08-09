<?php

namespace App\Http\Controllers;

use App\Models\MicroTik;
use Illuminate\Http\Request;
use App\Models\WiredClient;
use RouterOS;
use RouterOS\Query;

class WiredClientsController extends Controller
{
    public $client;
    public $current_microtik_id;
    public $session_data;

    public function connection()
    {
        $data = session()->get('router_session');
        $this->session_data = $data;
        $config = new \RouterOS\Config([
            'host' => $data['ip'],
            'user' => $data['username'],
            'pass' => $data['password'],
            'port' => intval($data['port']),
        ]);
        try {
            $this->client = new RouterOS\Client($config);
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
        $users = WiredClient::all();
        return view('wired_clients.create', compact('users'));
    }

    public function getClients()
    {
        $query = new Query('/ip/address/print');
        $this->connection();
        $users = $this->client->query($query)->read();
        // dd($users);
        return $users;
    }
}
