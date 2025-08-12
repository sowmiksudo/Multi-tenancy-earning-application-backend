<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'project_id',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
