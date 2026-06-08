<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateOffset(Request $request, User $user)
    {
        $request->validate([
            'temperature_offset' => 'required|integer',
        ]);

        try {
            $user->update([
                'temperature_offset' => $user->temperature_offset + $request->input('temperature_offset'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Die Änderungen wurden erfolgreich gespeichert.',
                'temperature_offset' => $user->temperature_offset,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Beim Speichern der Änderungen ist ein Fehler aufgetreten.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
