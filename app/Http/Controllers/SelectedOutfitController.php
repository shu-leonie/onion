<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\SelectedOutfit;
use Carbon\Carbon;
use Exception;

class SelectedOutfitController extends Controller
{
    public function addItem(Item $item)
    {
        try {
            SelectedOutfit::create([
                'user_id' => auth()->id(),
                'item_id' => $item->id,
            ]);

            $status = 'success';
            $message = 'Das Kleidungsstück wurde erfolgreich zum Outfit hinzugefügt.';
        } catch (Exception $e) {
            $status = 'error';
            $message = 'Beim Hinzufügen des Kleidungsstücks zum Outfit ist ein Fehler aufgetreten.';
        }

        return redirect()->back()->with($status, $message);
    }

    /**
     * Set has_been_reviewed of the given SelectedOutfit to the given boolean value
     */
    public function saveReview(SelectedOutfit $selectedOutfit, boolean $is_reviewed)
    {
        try {
            $selectedOutfit->has_been_reviewed = $is_reviewed;
            $selectedOutfit->save();

            $status = 'success';
            $message = 'Review wurde erfolgreich gespeichert.';
        } catch (Exception $e) {
            $status = 'error';
            $message = 'Beim Speichern des Reviews ist ein Fehler aufgetreten.';
        }

        return redirect()->back()->with($status, $message);
    }

    /*
        public function saveReview(Carbon $date)
    {
        $user_id = auth()->id();

        try {
            // set has_been_reviewed to true for all selected outfit entries of the user for the given date
            SelectedOutfit::where('user_id', $user_id)
                ->where('date', $date)
                ->update(['has_been_reviewed' => true]);

            $status = 'success';
            $message = 'Review wurde erfolgreich gespeichert.';
        } catch (Exception $e) {
            $status = 'error';
            $message = 'Beim Speichern des Reviews ist ein Fehler aufgetreten.';
        }

        return redirect()->back()->with($status, $message);
    }
        */
}
