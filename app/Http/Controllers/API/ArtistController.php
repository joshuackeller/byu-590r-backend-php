<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Artist;
use App\Models\ArtistGenre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ArtistSummaryEmail;


class ArtistController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $artists = Artist::all();
        foreach($artists as $artist) {
            if (isset($artist->picture_url)){
                $artist->picture_url = $this->getS3Url($artist->picture_url);
            } else {
                $artist->picture_url = null;
            }
        }

        return $this->sendResponse($artists, "Artists");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:1',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());     
        }
        $input = $request->all();

        if ($request->hasFile('image')) { 
            $extension = request()->file('image')->getClientOriginalExtension();
            $image_name = time() . "_" . mt_rand(1000000, 9999999) . "." . $extension;
            $path = $request->file('image')->storeAs(
                'images',
                $image_name,
                's3'
            );
            Storage::disk('s3')->setVisibility($path, "public");
            if(!$path) {
                return $this->sendError($path, "artist image failed to upload");
            }

            $input['picture_url'] = $path;
        }
    
        $artist = Artist::create($input);
        if(isset($artist->picture_url)) {
            $artist->picture_url = $this->getS3Url($path);
        }

        return $this->sendResponse($artist, "Artist created");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $artist = Artist::findOrFail($id);
        $artist->picture_url = $this->getS3Url($artist->picture_url);
        return $this->sendResponse($artist, 'Artist');
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:1',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());     
        }
        $input = $request->all();
        $artist = Artist::findOrFail($id);
        
        $artist->name = $input['name'];

        $artist->save();
      
        if (isset($artist->picture_url)) {
            $artist->picture_url = $this->getS3Url($artist->picture_url);
        } else {
            $artist->picture_url = null;
        }
        
        return $this->sendResponse($artist, "Artist updated");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $artist = Artist::findOrFail($id);
        $success['name'] = $artist->name;
        $artist->delete();

        return $this->sendResponse($success, "Artist deleted");
    }

    public function uploadArtistImage(Request $request, $id)
    {   
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
         ]);

        if ($request->hasFile('image')) { 
            $artist = Artist::findOrFail($id);
            $extension = request()->file('image')->getClientOriginalExtension();
            $image_name = time() . "_" . $artist->id . "." . $extension;
            $path = $request->file('image')->storeAs(
                'images',
                $image_name,
                's3'
            );
            Storage::disk('s3')->setVisibility($path, "public");
            if(!$path) {
                return $this->sendError($path, "artist image failed to upload");
            }

            $artist->picture_url = $path;
            $artist->save();
            $success['picture_url'] = null;
            if(isset($artist->picture_url)) {
                $success['picture_url'] = $this->getS3Url($path);
            }
            return $this->sendResponse($success, "Artist image uploaded");

        }

    }

    public function removeArtistImage($id)
    {
        $artist = Artist::findOrFail($id);
        Storage::disk('s3')->delete($artist->picture_url);
        $artist->picture_url = null;
        $artist->save();
        $success['picture_url'] = null;
        return $this->sendResponse($success, "Artist picture_url removed");
    }


    public function getArtistGenres($id) {
        $artist = Artist::findOrFail($id);
        $genres = $artist->genres;
        return $this->sendResponse($genres, "Artist genres");
    }

    public function addArtistGenre(Request $request, $id, $genre_id) {
        
        $artistGenre = ArtistGenre::create([
            'artist_id' => $id,
            'genre_id' => $genre_id
        ]);

        $newArtistGenre['artist'] = $artistGenre->artist;
        $newArtistGenre['genre'] = $artistGenre->genre;

        return $this->sendResponse($newArtistGenre, "Artist genre added");
    }

    public function removeArtistGenre(Request $request, $id, $genre_id) {
        
        ArtistGenre::where('artist_id', $id)->where('genre_id', $genre_id)->delete();

        $deletedItem = [
            'artist_id' => $id,
            'genre_id' => $genre_id
        ];

        return $this->sendResponse($deletedItem, "Artist genre removed");
    }

    public function getArtistAlbums($id) {
        $artist = Artist::findOrFail($id);
        $albums = $artist->albums;
        foreach($albums as $album) {
            $album->picture_url = $this->getS3Url($album->picture_url);
        }
        return $this->sendResponse($albums, "Artist albums");
    }

    public function sendSummaryEmail(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());     
        }
        $input = $request->all();

        $artists = Artist::with('albums')->get();

        Mail::to($input['email'])->send(new ArtistSummaryEmail($artists));

    }

    public function artistsWithAlbums($id) {
        $artists = Artist::with('albums')->get();
        foreach($artists as $artist) {
            $artist['picture_url'] = $this->getS3Url($artist->picture_url);
            foreach($artist->albums as $album) {
                $album->picture_url = $this->getS3Url($album->picture_url);
            }
        }
        return $this->sendResponse($artists, "Artists with albums");   
    }

    

}
