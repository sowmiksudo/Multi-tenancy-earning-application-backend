<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->reference_id = 'TRX_' . strtoupper(uniqid());
        });
    }

    protected $fillable = [
        'tenant_id',
        'user_id',
        'applet_id',
        'reference_id',
        'type',
        'category',
        'amount',
        'balance_before',
        'balance_after',
        'status',
        'description',
        'metadata',
        'processed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applet()
    {
        return $this->belongsTo(Applet::class);
    }

    public function withdrawalRequest()
    {
        return $this->hasOne(WithdrawalRequest::class);
    }
}
