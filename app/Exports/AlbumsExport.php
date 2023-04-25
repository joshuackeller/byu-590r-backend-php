<?php

namespace App\Exports;

use App\Models\Album;
use Maatwebsite\Excel\Concerns\FromCollection;

class AlbumsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $albums = Album::all();
        foreach($albums as $album) {
            $album['artist_name'] = $album->artist->name;
        }

        return $albums;
    }
}
