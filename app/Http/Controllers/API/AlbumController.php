<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Album;
use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Exports\AlbumsExport;
use Maatwebsite\Excel\Facades\Excel;


class AlbumController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $albums = Album::all();
        foreach($albums as $album) {
            if (isset($album->picture_url)){
                $album->picture_url = $this->getS3Url($album->picture_url);
            } else {
                $album->picture_url = null;
            }
        }

        return $this->sendResponse($albums, "Albums");
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
            'artist_id' => 'required|integer',
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
                return $this->sendError($path, "album image failed to upload");
            }

            $input['picture_url'] = $path;
        }
        $album = Album::create($input);
        if(isset($album->picture_url)) {
            $album->picture_url = $this->getS3Url($path);
        }

        return $this->sendResponse($album, "Album created");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $album = Album::findOrFail($id);
        return $this->sendResponse($album, 'Album');
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
        $album = Album::findOrFail($id);
        
        $album->name = $input['name'];

        $album->save();
      
        if (isset($album->picture_url)) {
            $album->picture_url = $this->getS3Url($album->picture_url);
        } else {
            $album->picture_url = null;
        }
        
        return $this->sendResponse($album, "Album updated");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $album = Album::findOrFail($id);
        $success['name'] = $album->name;

        $album->delete();

        return $this->sendResponse($success, "Album deleted");
    }

    public function uploadAlbumImage(Request $request, $id)
    {   
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
         ]);

        if ($request->hasFile('image')) { 
            $album = Album::findOrFail($id);
            $extension = request()->file('image')->getClientOriginalExtension();
            $image_name = time() . "_" . $album->id . "." . $extension;
            $path = $request->file('image')->storeAs(
                'images',
                $image_name,
                's3'
            );
            Storage::disk('s3')->setVisibility($path, "public");
            if(!$path) {
                return $this->sendError($path, "album image failed to upload");
            }

            $album->picture_url = $path;
            $album->save();
            $success['picture_url'] = null;
            if(isset($album->picture_url)) {
                $success['picture_url'] = $this->getS3Url($path);
            }
            return $this->sendResponse($success, "Album image uploaded");

        }

    }

    public function removeAlbumImage($id)
    {
        $album = Album::findOrFail($id);
        Storage::disk('s3')->delete($album->picture_url);
        $album->picture_url = null;
        $album->save();
        $success['picture_url'] = null;
        return $this->sendResponse($success, "Album picture_url removed");
    }

    public function testing() {
        $artists = Artist::all();

        return $this->sendResponse($artists, "Artists");
    }

    public function export() 
    {
        return Excel::download(new AlbumsExport, 'albums.xlsx');
    }

}
