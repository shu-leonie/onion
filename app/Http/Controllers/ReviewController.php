<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\SelectedOutfit;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $allItems = SelectedOutfit::with(['item.category', 'item.tags'])
            ->where('user_id', auth()->id())
            ->latest() 
            ->get();

        $unreviewedItems = $allItems->filter(function($entry) {
            return empty($entry->has_been_reviewed);
        });

        if ($unreviewedItems->isEmpty()) {
            return view('error', ['error_message' => "Anscheinend gibt es hier nichts zu reviewen :)"]);
        }

        $latestTime = $unreviewedItems->first()->created_at;

        $items = $unreviewedItems->filter(function($entry) use ($latestTime) {
            return $entry->created_at->diffInSeconds($latestTime) <= 5 && $entry->item !== null;
        })->map(function ($entry) {
            return array_merge([
                'outfit_entry_id' => $entry->id,
                'outfit_date' => $entry->created_at->toDateString(),
            ], $entry->item->toArray());
        });

        return view('review', ['items' => $items]);
    }
}