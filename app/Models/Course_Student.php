<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class course_student extends Model
{
    use HasFactory, SoftDeletes;
    Protected $fillable = [
        'user_id',
        'course_id',
    ];
}
