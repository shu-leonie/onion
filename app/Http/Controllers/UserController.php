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

            $status = 'success';
            $message = 'Die Änderungen wurden erfolgreich gespeichert.';
        } catch (Exception $e) {
            $status = 'error';
            $message = 'Beim Speichern der Änderungen ist ein Fehler aufgetreten.';
        }

        return redirect()->back()->with($status, $message);
    }
}


