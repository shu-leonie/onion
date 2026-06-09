<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\SelectedOutfit;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class SelectedOutfitController extends Controller
{
    /*public function storeOutfit(Request $request) //baut array von item ids zusammen uzm komplettes outfit zu speichern
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:items,id',
        ]);

        foreach ($request->input('item_ids') as $itemId) {
            SelectedOutfit::create([
                'user_id' => auth()->id(),
                'item_id' => $itemId,
                'has_been_reviewed' => false,
            ]);
        }

        return redirect()->route('outfit.review', [
            'date' => now()->toDateString()
        ]);
    }*/

    public function storeOutfit(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:items,id',
        ]);
        SelectedOutfit::where('user_id', auth()->id())
            ->whereDate('created_at', now()->toDateString())
            ->where(function($query) {
                $query->where('has_been_reviewed', false)
                      ->orWhere('has_been_reviewed', 0)
                      ->orWhereNull('has_been_reviewed');
            })
            ->delete();

        foreach ($request->input('item_ids') as $itemId) {
            $outfit = new SelectedOutfit();
            $outfit->user_id = auth()->id();
            $outfit->item_id = $itemId;
            $outfit->has_been_reviewed = 0;
            $outfit->save();
        }

        return redirect()->route('outfit.review', [
            'date' => now()->toDateString()
        ]);
    }

    public function addItem(Item $item)
    {
        try {
            SelectedOutfit::create([
                'user_id' => auth()->id(),
                'item_id' => $item->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Das Kleidungsstück wurde erfolgreich zum Outfit hinzugefügt.',
                'item' => $item,
                'item_id' => $item->id,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Beim Hinzufügen des Kleidungsstücks zum Outfit ist ein Fehler aufgetreten.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveReview(SelectedOutfit $selectedOutfit, bool $is_reviewed) //bool nicht boolean - php wirft sonst nen fehler...
    {
        try {
            $selectedOutfit->has_been_reviewed = $is_reviewed;
            $selectedOutfit->save();

            return response()->json([
                'success' => true,
                'message' => 'Review wurde erfolgreich gespeichert.',
                'selectedOutfit' => $selectedOutfit,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Beim Speichern des Reviews ist ein Fehler aufgetreten.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return redirect()->back()->with($status, $message);
    }
}