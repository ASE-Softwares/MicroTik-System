<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RouterOS;
use Carbon\Carbon;
use App\Models\Profile;
use App\Models\MpesaTransaction;

class HelpController extends Controller
{
	public $client;
	public $current_microtik_id;
	public $session_data;
	public function raw_transaction(){
		return view('admin.admins.mpesa_transaction');
	}
	public function connection(){        
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
			$this->discover_microtik();      

		} catch (\Exception $e) {
			session()->forget('router_session');
			return redirect(route('router_login'))->with('error_message','Router Disconnected! Please Check if its connected');

		}      
	}

	public function discover_microtik(){
		$data = session()->get('router_session');
		$router = MicroTik::where('ip', $data['ip'])->first();
		if ($router) {
			return $this->current_microtik_id = $router->id;        
		}      
	}

	public function create_subscription(Request $request){		
		$data =$this->validate($request, [
			'amount'=>'numeric|string|min:1|max:1000',
			'MpesaReceiptNumber'=>'required|string|min:5|max:10|alpha_num',
			'TransactionDate'=>'date|string|min:5|max:10',
			'PhoneNumber'=>['required','numeric','digits:10','starts_with:07,01'],
		]);
		

	  	// Check if transaction exits
		$t = MpesaTransaction::where('MpesaReceiptNumber', $data['MpesaReceiptNumber'])->get();
		if ($t!=null && $t->count()>0) {
			return false;
		}

		$create_transaction = new MpesaTransaction;
		$create_transaction->amount= $data['amount'];
		$create_transaction->MpesaReceiptNumber=$data['MpesaReceiptNumber'];
		$create_transaction->TransactionDate=$data['TransactionDate'];
		$create_transaction->PhoneNumber=$data['PhoneNumber'];

	  //get a profile from router with price
		$profile = Profile::where('price', $data['amount'])->first();
		if ($profile) {
	    //create a user
			$res = $this->create_user($PhoneNumber, $MpesaReceiptNumber, $profile->name);
			if ($res == true) {
				$create_transaction->status = "Conected To Network SuccessFully";
			}else{
				$create_transaction->status = "Not Conected To Network, Error! Router Missing";
			}
		}
		else{
			$create_transaction->status = "Not Conected To Network, Error! Missing Profile ";

	    //alert admin of a payment that has an issue.
		}

		$create_transaction->save();
	  // Delete the raw response after successfully recorded it.		
		return redirect()->back()->with('success','Transaction Recorded and User Added SuccessFully');

		
	}

	public function create_user($name, $password, $profile){
		$query = (new RouterOs\Query('/ip/hotspot/user/add'))
		->equal('name', $name)
		->equal('password',$password)
		->equal('profile', $profile);

		if ($this->client !=null) {
			$response = $this->client->query($query)->read();
			return true;
		}else{
			return false;
		}

	}
}
