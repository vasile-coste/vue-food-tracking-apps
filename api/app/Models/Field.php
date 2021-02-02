<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'user_id',
        'field_name',
        'seed_id',
        'seed_name',
        'seed_company_id',
        'seed_company_name',
        'seeding_status',
        'fertilizer_id',
        'fertilizer_name',
        'fertilizer_company_id',
        'fertilizer_company_name',
        'fertilizing_status',
        'harvesting_company_id',
        'harvesting_company_name',
        'harvesting_status',
        'products_sold'
    ];
}
