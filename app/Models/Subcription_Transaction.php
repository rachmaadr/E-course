<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcription_Transaction extends Model
{
    use HasFactory, SoftDeletes;
    Protected $fillable = [
        'user_id',
        'total_amount',
        'is_paid',
        'proof',
        'subcription_start_date'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
