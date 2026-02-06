<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::with('author')->paginate(10);

        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(storeBookRequest $request)
    {
        $book = Book::create($request->validated());

        $book->load('author');

        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        try {
            $book->load('author');

            return new BookResource($book);
        } catch (\Exception $th){
            return response()->json([
                'status' => false,
                'message' => 'Livro não encontrado'

            ], 404);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBookRequest $request, Book $book)
    {
        $book->update($request->validated());

        $book->load('author');

        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json([
            'status' => true,
            'message' => 'Livro excluído com sucesso!'
        ]);
    }
}
