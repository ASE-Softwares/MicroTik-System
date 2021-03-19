<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \RouterOS;
use App\Models\MicroTik;
use App\Models\MpesaTransaction;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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
        if($request->session()->exists('router_session')){
            $logged_in_to_router =true;
        }else{
            $logged_in_to_router =false;
        }
        //if you logged in to router 
        if ($logged_in_to_router) {
            $todayEarnings = $this->calculateDailyTotal(); 
            $thisMonthEarnings = $this->calculateThisMonthTotal();

            return view('home', compact('todayEarnings','thisMonthEarnings'));
        }else{
            return redirect(route('router_login'));
            // return view('home');

        }

    }

    public function routerLogin(Request $request){
        if($request->session()->exists('router_session')){
            return redirect(route('home'));
        }
        return view('microtik.login');
    }

    public function init(Request $request){
        //verify data
        $data = $this->validate($request, [
            'ip'=>'required|ip',
            'username'=>'required|alpha_dash',
            'password'=>'required|min:6',
            'port'=>'required|numeric',
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
            return redirect()->back()->with('error', 'Hello '.auth()->user()->name.', For Some reason, We Could not login you  to the router');
        }        
        session(['router_session' => $data]);
        return redirect(route('home'));

    }
    public function router_auto_login(Request $request, MicroTik $microtik){
        if($request->session()->exists('router_session')){
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
            return redirect()->back()->with('error', 'Hello '.auth()->user()->name.', For Some reason, We Could not login you  to the'. $router->name.' router');
        }        
        session(['router_session' => $router]);
        return redirect(route('home'));
    }

    public function add_router(Request $request) {
        // dd($request->all());
        $data = $this->validate($request, [
            'ip'=>'required|string|unique:micro_tiks',
            'username'=>'required|string',
            'password'=>'required|string',
            'port'=>'required|numeric',
            'name'=>'required|string',
            'location'=>'required|string'
        ]);

        $newResponse = MicroTik::create($data);
        return redirect(route('router_login'))->with('success','Router added successfully');
    }

    public function calculateDailyTotal(){
        return MpesaTransaction::whereDate('created_at', Carbon::today())->sum('amount');
    }

    public function calculateThisMonthTotal(){
        return MpesaTransaction::whereDate('created_at', Carbon::now()->month)->sum('amount');
    }
}
