<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;

    protected $fillable = [
        'checkout_date',
        'due_date',
        'checkin_date'
        ];

    public function users()
    {
        return $this->belongsToMany(User::class,'user_book_checkouts','book_id')->distinct();
    }
    
    public function books()
    {
        return $this->belongsToMany(Books::class,'user_book_checkouts','book_id','user_id')->distinct();
    }
}
