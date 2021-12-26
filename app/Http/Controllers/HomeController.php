<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \RouterOS;
use App\Models\MicroTik;
use App\Models\MpesaTransaction;
use Carbon\Carbon;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\Admin;
use App\Models\Package;
use App\Models\WiredClient;
use RouterOS\Config;
use RouterOS\Query;

class HomeController extends Controller
{

	public $client;
	public $current_microtik_id;
	public $session_data;
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function connection()
	{
		$config = new Config();
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

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */
	public function index(Request $request)
	{
		// $data  = session('router_session');
		// dd($data->config);
		if ($request->session()->exists('router_session')) {
			$logged_in_to_router = true;
		} else {
			$logged_in_to_router = false;
		}
		//if you logged in to router 
		if ($logged_in_to_router) {
			$todayEarnings = $this->calculateDailyTotal();
			$thisMonthEarnings = $this->calculateThisMonthTotal();
			$thisYearEarnings = $this->calculateThisYearTotal();
			$allInterfaces = new AdminController;
			$interfaces = $allInterfaces->interfaces();
			$wired_client = $this->getwired_client();
			$active_clients = $this->getactive_clients();
			$inactive_clients = $this->getinactive_clients();

			return view('home', compact(
				'todayEarnings',
				'thisMonthEarnings',
				'thisYearEarnings',
				'interfaces',
				'wired_client',
				'active_clients',
				'inactive_clients'
			));
		} else {
			return redirect(route('router_login'));
		}
	}

	public function routerLogin(Request $request)
	{
		if ($request->session()->exists('router_session')) {
			return redirect(route('home'));
		}
		return view('microtik.login');
	}

	public function init(Request $request)
	{
		//verify data
		$data = $this->validate($request, [
			'ip' => 'required|ip',
			'username' => 'required|alpha_dash',
			'password' => 'required|min:6',
			'port' => 'required|numeric',
		]);

		//try to login to router
		$config = new \RouterOS\Config([
			'host' => $data['ip'],
			'user' => $data['username'],
			'pass' => $data['password'],
			'port' => intval($data['port']),
		]);

		try {
			$client = new RouterOS\Client($config);
		} catch (\Exception $e) {
			return redirect()->back()->with('error', $e);
		}
		session(['router_session' => $data]);
		return redirect(route('home'));
	}
	public function router_auto_login(Request $request, MicroTik $microtik)
	{
		if ($request->session()->exists('router_session')) {
			$request->session()->forget('router_session');
		}
		$router = $microtik;
		$config = new \RouterOS\Config([
			'host' => $router->ip,
			'user' => $router->username,
			'pass' => $router->password,
			'port' => intval($router->port),
		]);

		try {
			$client = new RouterOS\Client($config);
		} catch (\Exception $e) {
			return redirect()->back()->with('error', 'Hello ' . auth()->user()->name . ', For Some reason, We Could not login you  to the' . $router->name . ' router with error' . $e);
		}
		session(['router_session' => $router]);
		return redirect(route('home'));
	}

	public function add_router(Request $request)
	{
		// dd($request->all());
		$data = $this->validate($request, [
			'ip' => 'required|string|unique:micro_tiks',
			'username' => 'required|string',
			'password' => 'required|string',
			'port' => 'required|numeric',
			'name' => 'required|string',
			'location' => 'required|string'
		]);

		$newResponse = MicroTik::create($data);
		return redirect(route('router_login'))->with('success', 'Router added successfully');
	}

	public function calculateDailyTotal()
	{
		return MpesaTransaction::whereDay('created_at', '=', date('d'))->where('status', 'Conected To Network SuccessFully')->sum('amount');
	}

	public function calculateThisMonthTotal()
	{
		return MpesaTransaction::whereMonth('created_at', '=', date('m'))->where('status', 'Conected To Network SuccessFully')->sum('amount');
	}

	public function calculateThisYearTotal()
	{
		return MpesaTransaction::whereYear('created_at', '=', date('Y'))->where('status', 'Conected To Network SuccessFully')->sum('amount');
	}

	public function subscriptions(Request $request)
	{
		if ($request->ajax()) {
			return Package::all();
		} else {
			abort(500);
		}
	}

	public function getwired_client()
	{
		$c = WiredClient::count();
		return $c;
	}
	public function getactive_clients()
	{
		return	$this->queryQueue('false');
	}
	public function getinactive_clients()
	{
		return $this->queryQueue('true');
	}

	private function queryQueue($status)
	{
		$query = new Query('/ip/address/print');
		$query->where('disabled', $status);
		$this->connection();
		$users = collect($this->client->query($query)->read());
		return $users->count();
	}
}
