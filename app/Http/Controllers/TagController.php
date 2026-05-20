<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::where('user_id', auth()->id())->get();

        return view('tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        try {
            $tag=Tag::create([
                'name' => $request->name,
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'status' => 'success',
                'tag' => $tag,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Beim Speichern des Tags ist ein Fehler aufgetreten.'
            ], 500);
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        return view('tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        try {
            $tag->update([
                'name' => $request->name,
            ]);

            $status = 'success';
            $message = 'Die Änderungen wurden erfolgreich gespeichert.';
        } catch (Exception $e) {
            $status = 'error';
            $message = 'Beim Speichern der Änderungen ist ein Fehler aufgetreten.';
        }

        return redirect()->route('tags.index')->with($status, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        try {
            $tag->delete();
            $status = 'success';
            $message = 'Der Tag wurde erfolgreich gelöscht.';
        } catch (Exception $e) {
            $status = 'error';
            $message = 'Beim Löschen des Tags ist ein Fehler aufgetreten.';
        }

        return redirect()->route('tags.index')->with($status, $message);
    }
}
