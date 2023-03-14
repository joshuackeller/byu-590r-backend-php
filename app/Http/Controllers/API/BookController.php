<?php

namespace App\Http\Controllers\API;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BookController extends BaseController
{
    public function index()
    {
        $books = Book::all();
        foreach($books as $book) {
            $book->book_cover_picture_url = $this->getS3Url($book->book_cover_picture);
        }

        return $this->sendResponse($books, 'User');
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
            'description' => 'required|min:1',
            'inventory_total_qty' => 'required|integer',
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
                return $this->sendError($path, "book image failed to upload");
            }

            $input['book_cover_picture'] = $path;
        }
        $book = Book::create($input);
        if(isset($book->book_cover_picture)) {
            $book->book_cover_picture_url = $this->getS3Url($path);
        }
        $book->inventory_total_qty = intval($book->inventory_total_qty);
        $book->checked_qty = intval($book->checked_qty);
        return $this->sendResponse($book, "Book created");
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
            'description' => 'required|min:1',
            'inventory_total_qty' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());     
        }
        $input = $request->all();
        $book = Book::findorFail($id);
        
        $book->name = $input['name'];
        $book->description = $input['description'];
        $book->inventory_total_qty = $input['inventory_total_qty'];
        

        $book->save();
      
        $book->book_cover_picture_url = $this->getS3Url($book->book_cover_picture);
        
        
        return $this->sendResponse($book, "Book updated");
    }


    public function uploadBookImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
         ]);
         $book = Book::findOrFail($id);

        if ($request->hasFile('image')) { 

            Storage::disk('s3')->delete($book->book_cover_picture);

            $extension = request()->file('image')->getClientOriginalExtension();
            $image_name = time() . "_" . mt_rand(1000000, 9999999) . "." . $extension;
            $path = $request->file('image')->storeAs(
                'images',
                $image_name,
                's3'
            );
            Storage::disk('s3')->setVisibility($path, "public");
            if(!$path) {
                return $this->sendError($path, "could not update book image");
            }

            $book->book_cover_picture = $path;
            $book->save();
           
            $book->book_cover_picture_url = $this->getS3Url($book->book_cover_picture);

            return $this->sendResponse($book, "User avatar uploaded");

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $success['name'] = $book->name;

        if($book->checked_qty == 0) {
            $book->delete();
        } else {
            return $this->sendError('Cannot delete book until all checked out books are returned');     
        }

        return $this->sendResponse($success, "Book deleted");
    }

    public function checkoutBook(Request $request, $id) {
        $book = Book::findOrFail($id);
        if($book->checked_qty >= $book->inventory_total_qty) {
            return $this->sendError($book, "all books are currently checked out");
        }
        $book->checked_qty = $book->checked_qty + 1;
        $book->save();
        $success['book'] = $book;

        return $this->sendResponse($success, "Book checked out");
    } 

    public function returnBook(Request $request, $id) {
        $book = Book::findOrFail($id);
        if($book->checked_qty <= 0) {
            return $this->sendError($book, "no more books to return");
        }
        $book->checked_qty = $book->checked_qty - 1;
        $book->save();
        $success['book'] = $book;

        return $this->sendResponse($success, "Book returned");
    }

}
