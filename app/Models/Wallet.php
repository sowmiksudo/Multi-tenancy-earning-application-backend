<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'balance',
        'total_earned',
        'total_withdrawn',
        'status',
        'last_transaction_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'last_transaction_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id')
            ->where('tenant_id', $this->tenant_id);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'user_id', 'user_id') 
            ->where('tenant_id', $this->tenant_id);
    }
}
