<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model{
    use HasFactory;
    protected $guarded = ['status'];

    public function wallet(){
        return $this->belongsTo(Wallet::class);
    }
    public function currency(){
        return $this->belongsTo(Currency::class);
    }
}
