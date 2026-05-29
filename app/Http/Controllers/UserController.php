<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SelectedOutfit;
class UserController extends Controller
{
    public function updateOffset(Request $request)
    {
        $request->validate([
            'temperature_offset' => 'required|integer',
            'outfit_ids' => 'array', 
        ]);

        try {
            $user = auth()->user();
            
            $user->update([
                'temperature_offset' => $user->temperature_offset + $request->input('temperature_offset'),
            ]);

            if ($request->has('outfit_ids')) {
                SelectedOutfit::whereIn('id', $request->input('outfit_ids'))
                    ->where('user_id', $user->id)
                    ->update(['has_been_reviewed' => 1]);
            }

            $status = 'success';
            $message = 'Vielen Dank für dein Feedback!';
        } catch (\Exception $e) {
            $status = 'error';
            $message = 'Beim Speichern der Änderungen ist ein Fehler aufgetreten.';
        }

        return view('review-success', [
            'offset' => $request->input('temperature_offset')
        ]);
    }
}