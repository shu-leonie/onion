<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SelectedOutfit;
use Exception;

class UserController extends Controller
{
    public function updateOffset(Request $request)
    {
        $request->validate([
            'temperature_offset' => 'required|integer',
            'outfit_ids' => 'nullable|array',
            'outfit_timestamp' => 'nullable|string',
        ]);

        try {
            $user = auth()->user();

            $user->update([
                'temperature_offset' => $user->temperature_offset + $request->input('temperature_offset'),
            ]);

            if ($request->has('outfit_ids')) {

                SelectedOutfit::where('user_id', $user->id)
                    ->where('has_been_reviewed', false)
                    ->update(['has_been_reviewed' => true]);
            }


            $timestamp = $request->input('outfit_timestamp');
            if ($timestamp) {
                session()->forget("outfit_placeholders.{$timestamp}");
                $unreviewed = session('unreviewed_placeholder_outfits', []);
                if (($key = array_search($timestamp, $unreviewed)) !== false) {
                    unset($unreviewed[$key]);
                    session(['unreviewed_placeholder_outfits' => array_values($unreviewed)]);
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Die Änderungen wurden erfolgreich gespeichert.',
                    'temperature_offset' => $user->temperature_offset,
                ]);
            }

            return view('review-success', [
                'offset' => $request->input('temperature_offset')
            ]);

        } catch (Exception $e) {
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beim Speichern der Änderungen ist ein Fehler aufgetreten.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Beim Speichern der Änderungen ist ein Fehler aufgetreten.');
        }
    }
}