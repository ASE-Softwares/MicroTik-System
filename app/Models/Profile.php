<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = [
    'name',
	'shared-users',
	'rate-limit',
	'price',
	'description',
	'keepalive-timeout',
	'on_offer'

];
 protected $casts = [
        'on_offer' => 'boolean',
    ];

	public function micro_tik(){
		return $this->belongsTo(MicroTik::class);
	}
}
