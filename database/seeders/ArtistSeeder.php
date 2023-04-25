<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Artist;
use App\Models\ArtistGenre;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class ArtistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $artists = [
            [
                'id' => 1,
                'name' => 'Taylor Swift',
                'picture_url' => 'images/taylorswift.jpeg'
            ]
         ];
        Artist::insert($artists);


        $albums = [
            [
            'name' => 'Taylor Swift',
            'artist_id' => 1
            ]
        ];
        Album::insert($albums);


        $genres = [
            [
                'id' => 1,
                'name' => 'Pop'
            ],
            [
                'id' => 2,
                'name' => 'Rap'
            ],
            [
                'id' => 3,
                'name' => 'Country'
            ],
            [
                'id' => 4,
                'name' => 'Alternative'
            ],
            [
                'id' => 5,
                'name' => 'Rock'
            ],
            [
                'id' => 6,
                'name' => 'Jazz'
            ],
            [
                'id' => 7,
                'name' => 'Classical'
            ],
            [
                'id' => 8,
                'name' => 'Electronic'
            ],
        ];
        Genre::insert($genres);


        $artistGenres = [
            [
                'artist_id' => 1,
                'genre_id' => 1
            ],
            [
                'artist_id' => 1,
                'genre_id' => 3
            ],
            [
                'artist_id' => 1,
                'genre_id' => 4
            ],
        ];
        ArtistGenre::insert($artistGenres);
    }
}
