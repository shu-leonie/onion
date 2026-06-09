<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Models\Tag;
use App\Models\SelectedOutfit;

class ProfileController extends Controller
{
    public function index()
    {
        $kleidungsstuecke = Item::with(['category', 'tags'])
            ->where('user_id', Auth::id())
            ->get();
        $tags = Tag::where('user_id', Auth::id())
            ->get();
        $categories = Category::all();
        $unreviewedOutfitsByDay = SelectedOutfit::where('user_id', auth()->id())
            ->where('has_been_reviewed', false)
            ->get()
            ->groupBy(fn($outfit) => $outfit->created_at->toDateString())
            ->map(function ($group) {
                return $group->pluck('item_id');
            });

        return view('profile', [
            'items' => $kleidungsstuecke,
            'tags' => $tags,
            'categories' => $categories,
            'unreviewedOutfitsByDay' => $unreviewedOutfitsByDay
        ]);
    }

}