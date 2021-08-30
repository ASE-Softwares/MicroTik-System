<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WiredClient extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'location',
        'package_id',
        'ip_address'
    ];
    protected $casts = [
        'active' => 'boolean'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function package()
    {
        return $this->belongsTo(Package::class)->withDefault();
    }
}
