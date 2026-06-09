<?php
namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Services\RecommendationService;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Tag;
use App\Models\SelectedOutfit;

class RecommendationController extends Controller
{
    public function index(WeatherService $weatherService, RecommendationService $recService, GeocodingService $GeocodingService, Request $request)
    {
        $hasPendingOutfitToday = SelectedOutfit::where('user_id', auth()->id())
            ->where('has_been_reviewed', false)
            ->whereDate('created_at', today())
            ->exists();
            
        if ($hasPendingOutfitToday) {
            return redirect()->route('outfit.review', [
                'date' => now()->toDateString()
            ]);
        }

        if ($request->filled('latitude') && $request->filled('longitude')) {
            $latitude = $request->latitude;
            $longitude = $request->longitude;
        } else {
            $latitude = 48.2;
            $longitude = 16.37;
        }

        $location = $GeocodingService->reverse($latitude, $longitude);
        if (!$location) {
            $location = ['display_name' => "Standort unbekannt"];
        } else if(isset($location['error']) && $location['error'] === 'Unable to geocode') {
            $location['display_name'] = "Standort unbekannt";
        }
        
        $weather = $weatherService->getWeather($latitude, $longitude);
        //$weather=false;   Test für Fehlerseite
        if (!$weather) {
            return view('error', [
                'error_message' => 'Die Wetterdaten konnten derzeit nicht geladen werden. Bitte versuche es später erneut.'
            ]);
        }
        
        $currentTime = $this->getCurrentHourIndex($weather['time']);
        if ($currentTime === -1) {
            return view('error', [
                'error_message' => 'Die Wetterdaten konnten derzeit nicht verarbeitet werden. Bitte versuche es später erneut.'
            ]);
        }
        if (Auth::guest()) {
            $kleidungsstuecke = collect([]); 
            $tags = collect([]);
        } else {
            $kleidungsstuecke = Item::with(['category', 'tags'])
                ->where('user_id', Auth::id())
                ->get();
            $tags = Tag::where(function ($query) {
                $query
                    ->where('user_id', Auth::id())
                    ->orWhereNull('user_id');
                })
                ->get();
        }
        
        $recommendations = [];
        foreach ($kleidungsstuecke as $item) {
            if ($recService->isRecommended($item, $currentTime, $weather)) {
                $recommendations[] = $item;
            }
        }

        $neededCategories = $this->getNeededCategories($weather, $currentTime);
        
        $recommendationsForFrontend = $this->formatForFrontend($recommendations, $neededCategories);
        
        $categoryMap = Category::all()
            ->map(function ($category) {
                return $this->mapCategoryName($category->name);
            })
            ->filter()
            ->values()
            ->toArray();

        return view('home', [
            'recommendations' => $recommendationsForFrontend,
            'tags' => $tags,
            'categories' => $categoryMap,
            'weather' => $weather,
            'current_time' => $currentTime,
            'location' => $location['display_name']
        ]);
    }

    private function getCurrentHourIndex($times)
    {
        $currentHour = now()->format('Y-m-d\TH');

        foreach ($times as $index => $time) {
            if (str_starts_with($time, $currentHour)) {
                return $index;
            }
        }

        return -1;
    }

    //hab ne kelien logit für die platzhalter dazugebaut...
    private function getNeededCategories($weather, $currentTime)
    {
        $temp = $weather['apparentTemperature'][$currentTime] ?? 15;
        $rain = $weather['precipitation'][$currentTime] ?? 0;
        $uv = $weather['uvIndex'][$currentTime] ?? 0;

        $needed = ['upper_shirt', 'lower_pants', 'feet_shoes'];

        if ($temp < 20) {
            $needed[] = 'upper_pulli';
            $needed[] = 'feet_socks';
        }
        if ($temp < 15 || $rain > 0) {
            $needed[] = 'upper_jacke';
        }
        if ($temp < 5) {
            $needed[] = 'head'; 
        }
        if ($uv > 3) {
            $needed[] = 'sunglasses';
        }

        return $needed;
    }

    private function formatForFrontend($recommendations, $neededCategories) {
        $categoryMap = [
            'Kopfbedeckung'   => 'head',
            'T-Shirt'         => 'upper_shirt',
            'Pullover'        => 'upper_pulli',
            'Jacke'           => 'upper_jacke',
            'Hose'            => 'lower_pants',
            'Strumpfhose'     => 'lower_tights',
            'Socken'          => 'feet_socks',
            'Schuhe'          => 'feet_shoes',
            'Accessoires'     => 'hand',
            'Sonnenbrille'    => 'sunglasses',
            'Sonnencreme'     => 'sunscreen',
        ];

        $result = [];

        foreach ($neededCategories as $cat) {
            $result[$cat] = [];
        }

        foreach ($recommendations as $item) {
            if (!$item->category) continue;

            $key = $categoryMap[$item->category->name] ?? null;
            if (!$key) continue;

            if (!isset($result[$key])) {
                $result[$key] = [];
            }

            $result[$key][] = [
                'id' => $item->id,
                'img' => $item->filepath,
                'name' => $item->name,
                'waterproof' => $item->is_waterproof,
                'cloudcoverthreshold' => $item->cloud_cover_threshold,
                'maxuv' => $item->max_uv_index,
                'minuv' => $item->min_uv_index,
                'maxtemp' => $item->max_temperature,
                'mintemp' => $item->min_temperature,
                'creationdate' => $item->created_at,
                'tags' => $item->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ];
                })->toArray(),
            ];
        }
        return $result;
    }

    private function mapCategoryName(string $name): ?string
    {
        return match ($name) {
            'Kopfbedeckung' => 'head',
            'T-Shirt' => 'upper_shirt',
            'Pullover' => 'upper_pulli',
            'Jacke' => 'upper_jacke',
            'Hose' => 'lower_pants',
            'Strumpfhose' => 'lower_tights',
            'Socken' => 'feet_socks',
            'Schuhe' => 'feet_shoes',
            'Accessoires' => 'hand',
            'Sonnenbrille' => 'sunglasses',
            'Sonnencreme' => 'sunscreen',
            default => null,
        };
    }
}