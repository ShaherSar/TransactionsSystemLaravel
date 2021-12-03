<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model{
    use HasFactory;
    protected $guarded = [];
    public function currencies(){
        return $this->belongsToMany(Currency::class, 'payment_methods_currencies', 'payment_method_id', 'currency_id');
    }
}
