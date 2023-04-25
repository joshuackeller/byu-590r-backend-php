<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ArtistGenre extends Model
{
    protected $table = 'artist_genre';

    protected $primaryKey = ['artist_id', 'genre_id'];

    public $incrementing = false;

    protected $fillable = ['artist_id', 'genre_id'];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
    public $timestamps = false;
}