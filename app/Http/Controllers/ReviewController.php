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
        $date = $request->input('date', now()->toDateString());
        $timestamp = $request->input('timestamp');

        if (!$timestamp) {

            $latestRealOutfit = SelectedOutfit::where('user_id', auth()->id())
                ->where('has_been_reviewed', 0)
                ->latest('created_at')
                ->first();

            if ($latestRealOutfit) {
                $timestamp = $latestRealOutfit->created_at->toDateTimeString();
                $date = $latestRealOutfit->created_at->toDateString();
            } else {
                $unreviewedPlaceholders = session('unreviewed_placeholder_outfits', []);
                if (!empty($unreviewedPlaceholders)) {
                    $timestamp = end($unreviewedPlaceholders); 
                }
            }
        }


        if (!$timestamp) {
            return view('error', ['error_message' => "Anscheinend gibt es hier nichts zu reviewen :)"]);
        }

        $dbItems = [];

        if (!str_starts_with($timestamp, 'placeholder_outfit_')) {
            $dbItems = SelectedOutfit::with(['item.category', 'item.tags'])
                ->where('user_id', auth()->id())
                ->where('created_at', $timestamp) 
                ->where(function($query) {
                    $query->where('has_been_reviewed', false)
                          ->orWhere('has_been_reviewed', 0)
                          ->orWhereNull('has_been_reviewed');
                })
                ->get()
                ->map(function ($entry) {
                    $itemData = $entry->item ? $entry->item->toArray() : [];
                    return array_merge([
                        'outfit_entry_id' => $entry->id,
                        'outfit_date' => $entry->created_at->toDateString(),
                    ], $itemData);
                })
                ->toArray();
        }


        $sessionPlaceholders = session("outfit_placeholders.{$timestamp}", []);

        $layerToCategoryMap = [
            'head'         => 'Kopfbedeckung',
            'sunglasses'   => 'Sonnenbrille',
            'sunscreen'    => 'Sonnencreme',
            'upper_shirt'  => 'T-Shirt',
            'upper_pulli'  => 'Pullover',
            'upper_jacke'  => 'Jacke',
            'lower_tights' => 'Strumpfhose',
            'lower_pants'  => 'Hose',
            'feet_socks'   => 'Socken',
            'feet_shoes'   => 'Schuhe',
            'hand'         => 'Accessoires'
        ];

        $items = $dbItems;

        foreach ($sessionPlaceholders as $layer) {
            if (isset($layerToCategoryMap[$layer])) {
                $catName = $layerToCategoryMap[$layer];
                
                $alreadyHasRealItem = collect($dbItems)->contains(function($item) use ($catName) {
                    return ($item['category']['name'] ?? '') === $catName;
                });

                if (!$alreadyHasRealItem) {
                    $items[] = [
                        'outfit_entry_id' => null,
                        'outfit_date' => $date,
                        'category' => ['name' => $catName],
                        'filepath' => null,
                        'is_placeholder' => true
                    ];
                }
            }
        }

        if (empty($items)) {
            return view('error', ['error_message' => "Anscheinend gibt es hier nichts zu reviewen :)"]);
        }

        return view('review', [
            'items' => $items,
            'outfit_timestamp' => $timestamp // Übergeben wir an die View fürs Formular!
        ]);
    }
}