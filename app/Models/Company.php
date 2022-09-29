<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Laravel\Scout\Searchable;

class Company extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'name', 'email', 'location','user_id'
    ];

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'location' => $this->location,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}