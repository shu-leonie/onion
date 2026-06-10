<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        try {
            $tag = Tag::create([
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
                'message' => 'Beim Speichern des Tags ist ein Fehler aufgetreten.',
                'error' => $e->getMessage(),
            ], 500);
        }

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

            return response()->json([
                'status' => 'success',
                'tag' => $tag,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Beim Speichern der Änderungen ist ein Fehler aufgetreten.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        try {
            $tag->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Der Tag wurde erfolgreich gelöscht.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Beim Löschen des Tags ist ein Fehler aufgetreten.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
