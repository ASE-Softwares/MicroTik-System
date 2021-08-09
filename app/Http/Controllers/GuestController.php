<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RouterOS;
use Carbon\Carbon;
use App\Models\Profile;
use App\Helpers\Mpesa;
use App\Models\Mpesaresponse;
use App\Models\MpesaTransaction;
use Facade\FlareClient\Http\Response;
use Illuminate\Support\Str;

class GuestController extends Controller
{
  /*
      Router OS Client Instance
  */
  protected $client;

  //Class contructor
  public function __construct()
  {
    $this->connection();
  }

  // Show the Vending App Welcome Page
  public function welcome()
  {
    $packages = Profile::all();
    return view('welcome', compact('packages'));
  }


  // Create a connection Instance
  public function connection()
  {
    $config = new \RouterOS\Config([
      'host' => env('REMOTE_ROUTER_HOST'),
      'user' => env('REMOTE_ROUTER_USER'),
      'pass' => env('REMOTE_ROUTER_PASS'),
      'port' => intval(env('REMOTE_ROUTER_PORT')),
    ]);

    try {
      $this->client = new RouterOS\Client($config);
    } catch (\Exception $e) {
      return;
    }
  }


  /*
      Send an stk push request and return the response to client side vue framework
  */
  public function purchase(Request $request,  Mpesa $mpesa)
  {
    $data = $this->validate($request, [
      'phone_number' => ['required', 'numeric', 'digits:10', 'starts_with:07,01'],
      'id' => ['exists:profiles', 'required'],
    ]);
    $package = Profile::find($data['id']);
    $amount = $package->price;
    $pnb = ltrim($data['phone_number'], '0');
    $msisdn = '254' . $pnb;
    $TransactionDesc = $package->name;
    $response = $mpesa->sendSTKPush($amount, $msisdn, $TransactionDesc);

    $response_object = response($response)->getContent();
    $response_object_as_json = json_decode($response_object, true);
    if ($response_object_as_json['ResponseCode'] == "0") {
      MpesaTransaction::create([
        'CheckoutRequestID' => $response_object_as_json['CheckoutRequestID'],
        'amount' => $amount,
        'MpesaReceiptNumber' => Str::random(15),
        'TransactionDate' => Carbon::now(),
        'PhoneNumber' => $data['phone_number'],
        'status' => 'Push Sent',
        'profile_id' => $package->id
      ]);
    }
    return $response;
  }


  // Process the response from mpesa from our callback url
  public function responseFromMpesa(Request $request)
  {
    $this->create_subscription($request);
    return;
  }


  /*
    Process the Payment, update the transaction and create the user to hotspot
    */


  public function create_subscription(Request $req)
  {
    $response_object = $req->getContent();
    $response_object_as_json = json_decode($response_object, true);
    $body = $response_object_as_json['Body'];
    $stkCallback = $body['stkCallback'];
    $CheckoutRequestID = $stkCallback['CheckoutRequestID'];
    $ResultCode = $stkCallback['ResultCode'];
    if ($ResultCode == 0) {
      $CallbackMetadata = $stkCallback['CallbackMetadata'];
      $Items = collect($CallbackMetadata['Item']);
      $phone_number = collect($Items->firstWhere('Name', 'PhoneNumber'))->get('Value');
      $transaction_code = collect($Items->firstWhere('Name', 'MpesaReceiptNumber'))->get('Value');
      $PhoneNumber = ltrim($phone_number, '254');
      $PhoneNumber = '0' . $PhoneNumber;
      $t = MpesaTransaction::where('CheckoutRequestID', $CheckoutRequestID)->where('status', 'Push Sent')->first();
      if (!$t == null) {
        $t->update([
          'status' => 'Processing',
        ]);
        $profile = $t->profile;
        if ($profile) {
          $this->create_user($PhoneNumber, $transaction_code, $profile->name);
          $t->update([
            'status' => "Conected To Network SuccessFully"
          ]);
        } else {
          $t->update([
            'status' => "Not Conected To Network, Error! Missing Profile "
          ]);
        }
      }
      return;
    } else {
      return;
    }
  }


  // Create the User on the router
  public function create_user($name, $password, $profile)
  {
    $query = (new RouterOs\Query('/ip/hotspot/user/add'))
      ->equal('name', $name)
      ->equal('password', $password)
      ->equal('profile', $profile);
    $response = $this->client->query($query)->read();
    return;
  }
}
