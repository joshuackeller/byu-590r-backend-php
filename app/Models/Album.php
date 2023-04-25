<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'picture_url',
        'artist_id',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }
    public $timestamps = false;
}