<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaTransaction extends Model
{
	use HasFactory;
	protected $fillable = [
		'amount',
		'MpesaReceiptNumber',
		'TransactionDate',
		'PhoneNumber',
		'status',
		'profile_id',
		'CheckoutRequestID'
	];

	public function profile()
	{
		return $this->belongsTo(Profile::class);
	}
}
