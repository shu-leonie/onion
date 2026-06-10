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
        ]);



        $now = now(); 
        $timestampString = $now->toDateTimeString(); 
        
        $hasRealItems = false;
        $placeholders = [];

        foreach ($request->input('item_ids') as $itemId) {
            if (is_numeric($itemId)) {
                $outfit = new SelectedOutfit();
                $outfit->user_id = auth()->id();
                $outfit->item_id = $itemId;
                $outfit->has_been_reviewed = 0;
                

                $outfit->created_at = $now;
                $outfit->updated_at = $now;
                $outfit->save();
                $hasRealItems = true;
            } else if (is_string($itemId) && str_starts_with($itemId, 'placeholder:')) {
                $placeholders[] = str_replace('placeholder:', '', $itemId);
            }
        }

        if (!$hasRealItems) {
            $timestampString = "placeholder_outfit_" . uniqid();
            $unreviewedPlaceholders = session('unreviewed_placeholder_outfits', []);
            $unreviewedPlaceholders[] = $timestampString;
            session(['unreviewed_placeholder_outfits' => $unreviewedPlaceholders]);
        }


        session(["outfit_placeholders.{$timestampString}" => $placeholders]);

        return redirect()->route('outfit.review', [
            'date' => $now->toDateString(),
            'timestamp' => $timestampString 
        ]);
    }

}