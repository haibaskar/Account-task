<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'account_id',
        'type',
        'amount',
        'description',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * A transaction belongs to a user (account).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_id');
    }
}

