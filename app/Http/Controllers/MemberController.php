<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::with('activeBorrowings');

        if ($request->has('search')){
            $search = $request->search;

            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{search}%")
                ->orwhere('email', 'like', "%{search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->paginate(10);

        return MemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
