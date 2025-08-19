<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Tenant;
use App\Models\UserProfile;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

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
        'otp_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function tenant(){
        return $this->belongsTo(Tenant::class);
    }
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function userApplets()
    {
        return $this->hasMany(UserApplet::class);
    }
    public function user_profiles()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }

    public function invalidActivities()
    {
        return $this->hasMany(InvalidActivity::class);
    }

}
