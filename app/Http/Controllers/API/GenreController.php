<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class GenreController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $genres = Genre::all();

        return $this->sendResponse($genres, "Genres");
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
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());     
        }
        $input = $request->all();

        $genre = Genre::create($input);

        return $this->sendResponse($genre, "Genre created");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $genre = Genre::findOrFail($id);
        return $this->sendResponse($genre, 'Genre');
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
        $genre = Genre::findOrFail($id);
        
        $genre->name = $input['name'];

        $genre->save();
      
        
        return $this->sendResponse($genre, "Genre updated");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $genre = Genre::findOrFail($id);
        $success['name'] = $genre->name;

        return $this->sendResponse($success, "Genre deleted");
    }

    public function getGenreArtists($id)
    {
        $genre = Genre::findOrFail($id);
        $artists = $genre->artists;

        return $this->sendResponse($artists, "Artists");
    }

}
