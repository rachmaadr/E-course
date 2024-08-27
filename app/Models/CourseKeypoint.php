<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseKeypoint extends Model
{
    use HasFactory, SoftDeletes;
    Protected $fillable = [
        'course_id',
        'name',
    ];

    public function course(){
        return $this->belongsTo(course::class);
    }
}
