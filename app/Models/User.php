<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'occupation',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function courses(){
        return $this->belongsToMany(course::class, 'course_students');
    }

    public function subcription_transaction(){
        return $this->hasMany(Subcription_Transaction::class, 'course_students');
    }

    public function hasActiveSubcription(){
        $latestSubcription = $this->subcription_transaction()
        ->where('is_paid', true)
        ->latest('update_at')
        ->first();

        if (!$latestSubcription) {
            # code...
            return false;
        }

        $subcriptionEndDate = Carbon::parse($latestSubcription->subcription_start_date)->addMonth(1);
        return Carbon::now()->lessThanOrEqualTo($subcriptionEndDate);
    }
}
