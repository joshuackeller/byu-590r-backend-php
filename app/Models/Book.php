<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'book_cover_picture', 
        'inventory_total_qty',
        'checked_qty'
    ];

    public $timestamps = true;
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public function checkouts(): BelongsToMany
    {
        return $this->belongsToMany(Checkout::class,'user_book_checkouts','book_id')->withPivot('user_id');
    }
}
