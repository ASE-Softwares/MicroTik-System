<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\MpesaTransaction;
use Illuminate\Http\Request;


/**
 * 
 */
class Mpesa
{
	public $short_code;
	public $consumer_secret;
	public $consumer_key;
	public $env;
	public $access_token;
	public $passkey;
	public $url;
	public $validation_url;
	public $confirmation_url;
	public $app_env;
	public $base_url;

	public function __construct()
	{
		$this->short_code = env('TILL_NUMBER', null);
		$this->consumer_secret = env('CONSUMER_SECRET', null);
		$this->consumer_key = env('CONSUMER_KEY', null);
		$this->env = env('MPESA_ENV', 'production');
		$this->passkey = env('PASS_KEY');
		$this->app_env = env('APP_ENV');
		if ($this->app_env == 'production') {
			$this->base_url = env('APP_URL');
			$this->url = $this->base_url . '/mpesa_response';
		} else {
			$this->base_url = env('TUNNEL_URL');
			$this->url = $this->base_url . '/mpesa_response';
		}
		$this->validation_url = $this->base_url . '/validation';
		$this->confirmation_url = $this->base_url . '/confirmation';
		$this->access_token = $this->getAccessToken();
	}

	public function getAccessToken()
	{
		$consumer_key = $this->consumer_key;
		$consumer_secret = $this->consumer_secret;

		$credentials = base64_encode($consumer_key . ":" . $consumer_secret);

		$env = $this->env;
		if ($env == 'sandbox') {
			$url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
		} else {
			$url = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		$credentials = base64_encode($consumer_key . ':' . $consumer_secret);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials)); //setting a custom header
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$curl_response = curl_exec($curl);
		if ($curl_response) {
			$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$header = substr($curl_response, 0, $header_size);
			$body = substr($curl_response, $header_size);
			$r = json_decode($body, true);
			return $r['access_token'];
		} else {
			return false;
		}
	}

	public function getLipaNaMpesapassword()
	{
		$lipa_time = Carbon::rawParse('now')->format('YmdHms');
		$passkey = $this->passkey;
		$BusinessShortCode = $this->short_code;
		$timestamp = $lipa_time;
		$lipa_na_mpesa_password = base64_encode($BusinessShortCode . $passkey . $timestamp);
		//dd($lipa_na_mpesa_password);
		return $lipa_na_mpesa_password;
	}

	public function sendSTKPush($amount, $phone_number, $TransactionDesc)
	{
		if ($this->env == 'sandbox') {
			$url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
		} else {
			$url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $this->access_token));
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$curl_post_data = [
			//Fill in the request parameters with valid values
			'BusinessShortCode' => $this->short_code,
			'Password' => $this->getLipaNaMpesapassword(),
			'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
			'TransactionType' => 'CustomerPayBillOnline',
			'Amount' => intval($amount),
			'PartyA' => intval($phone_number), // replace this with your phone number
			'PartyB' => $this->short_code,
			'PhoneNumber' => $phone_number, // replace this with your phone number
			'CallBackURL' => $this->url,
			'AccountReference' => "HOTSPOT",
			'TransactionDesc' => $TransactionDesc,
		];
		$data_string = json_encode($curl_post_data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
		$curl_response = curl_exec($curl);
		return $curl_response;
	}

	public function mpesaValidation(Request $request)
	{
		$result_code = "0";
		$result_description = "Accepted validation request.";
		return $this->createValidationResponse($result_code, $result_description);
	}

	public function createValidationResponse($result_code, $result_description)
	{
		$result = json_encode(["ResultCode" => $result_code, "ResultDesc" => $result_description]);
		$response = new Response();
		$response->headers->set("Content-Type", "application/json; charset=utf-8");
		$response->setContent($result);
		return $response;
	}


	public function mpesaRegisterUrls()
	{
		if ($this->env == 'sandbox') {
			$url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';
		} else {
			$url = "https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl";
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer ' . $this->access_token));
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
			'ShortCode' => $this->short_code,
			'ResponseType' => 'Completed',
			'ConfirmationURL' => $this->confirmation_url,
			'ValidationURL' => $this->validation_url
		)));
		$curl_response = curl_exec($curl);
		echo $curl_response;
	}
}
