<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionApprovalAdmin extends Model
{
    protected $fillable = [
        'election_id',
        'user_id',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
