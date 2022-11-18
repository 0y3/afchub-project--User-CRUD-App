<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class CompanyDepartment extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    protected $fillable = [
        'name','company_id'
    ];

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}