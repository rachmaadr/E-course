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

    public function subscribe_transaction(){
        return $this->hasMany(SubscribeTransaction::class, 'user_id');
    }

    public function hasActiveSubcription(){
        // Retrieve the latest subscription transaction where the subscription is paid
        $latestSubscription = $this->subscribe_transaction()
            ->where('is_paid', true)
            ->latest('updated_at')
            ->first();

        // If there is no paid subscription transaction, return false
        if (!$latestSubscription) {
            return false;
        }

        // Calculate the subscription end date based on the start date
        $subscriptionEndDate = Carbon::parse($latestSubscription->subscription_start_date)->addMonths(1);

        // Check if the current date is less than or equal to the subscription end date
        return Carbon::now()->lessThanOrEqualTo($subscriptionEndDate);
    }
}
