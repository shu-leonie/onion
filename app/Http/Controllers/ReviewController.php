<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Tag;
use App\Models\SelectedOutfit;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date');
        
        $items = SelectedOutfit::with([
                'item.category',
                'item.tags'
            ])
            ->where('user_id', auth()->id())
            ->where(function($query) {
                $query->where('has_been_reviewed', false)
                      ->orWhere('has_been_reviewed', 0)
                      ->orWhereNull('has_been_reviewed');
            })
            ->whereDate('created_at', $date)
            ->get()
            ->filter(function ($entry) {
                return $entry->item !== null;
            })
            ->map(function ($entry) {
                return array_merge([
                    'outfit_entry_id' => $entry->id,
                    'outfit_date' => $entry->created_at->toDateString(),
                ], $entry->item->toArray());
            });

        if ($items->isEmpty()) {
            return view('error', ['error_message' => "Anscheinend gibt es hier nichts zu reviewen :)"]);
        }

        return view('review', ['items' => $items]);
    }
}