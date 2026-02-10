<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Http\Resources\BorrowingResource;
use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Borrowing::with(['book', 'member']);

        // filtrar por status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // filtrar por membros
        if ($request->has('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        $borrowings = $query->latest()->paginate(15);

        return BorrowingResource::collection($borrowings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowingRequest $request)
    {
        // checando se o livro existe
        $book = Book::findOrFail($request->book_id);

        // checando se o livro está disponível
        if (!$book->isAvailable()) {
            return response()->json([
                'message' => 'O livro não está disponível para o empréstimo!'
            ],422);
        }

        //cria um livro com request válido
        $borrowing = Borrowing::create($request->validated());

        // atualiza disponibilidade do livro
        $book->borrow();

        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
    }

   public function returnBook(Borrowing $borrowing) {
        if ($borrowing->status !== 'borrowed') {
            return response()->json([
                'message' =>  'O livro já foi devolvido'
            ], 422);
        }

        // atualiza o registro do empréstimo
        $borrowing->update([
            'returned_date' => now(),
            'status' => 'returned'
        ]);

        // atualiza a disponibilidade do livro
        $borrowing->book->returnBook();

        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
   }

    public function overdue() {
        $overdueBorrowings = Borrowing::with(['book', 'member'])
        ->where('status', 'borrowed')
        ->where('due_date', '<', now())
        ->get();

        Borrowing::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        return BorrowingResource::collection($overdueBorrowings);
    }
}
