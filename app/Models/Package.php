<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "amount",
        "download",
        "upload"
    ];
    protected $appends = ['rate'];

    public function getRateAttribute()
    {
        return $this->download . '/' . $this->upload;
    }
}
