<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model{
    use HasFactory;

    protected $guarded = ['allowed_currencies'];

    public function currencies(){
        return $this->belongsToMany(Currency::class, 'currency_payment_method', 'payment_method_id', 'currency_id');
    }
}
