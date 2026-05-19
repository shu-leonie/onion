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
            return response()->json(['error' => 'Standort konnte nicht bestimmt werden'], 500);
        } else if(isset($location['error']) && $location['error'] === 'Unable to geocode') {
            $location['display_name'] = "Es konnte kein Ortsname gefunden werden.";
        }
        $weather = $weatherService->getWeather($latitude, $longitude);
        if (!$weather) {
            return response()->json(['error' => 'Keine Wetterdaten'], 500);
        }
        $currentTime = $this->getCurrentHourIndex($weather['time']);
        if ($currentTime === -1) {
            return response()->json(['error' => 'Zeit nicht gefunden'], 500);
        }

        /* Sollte theoretisch sowas liefern
        [
            {
                "id": 1,
                "name": "Winterjacke",
                "filepath": "/img/jacket.jpg",
                "is_waterproof": true,
                "cloudcoverthreshold": null,
                "maxuv": null,
                "maxtemp": 10,
                "mintemp": -5,

                "category": {
                "id": 2,
                "categoryname": "Jacken",
                "impactedbyrain": true
                },

                "tags": [
                { "id": 1, "name": "warm" },
                { "id": 2, "name": "winter" }
                ]
            }
        ]
        */
        if (Auth::guest()) {
            $kleidungsstuecke = Item::with(['category', 'tags'])
                ->WhereNull('user_id')
                ->get();
            $tags = Tag::whereNull('user_id')->get();
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
        $recommendationsForFrontend = $this->formatForFrontend($recommendations);
        /* Sollte sowas liefern
        {
            "upper_jacke": [
                {
                "id": 10,
                "img": "/storage/jacke1.png",
                "name": "Supa Jacke",
                "waterproof": true,

                "tags": [
                    { "id": 3, "name": "rain" },
                    { "id": 5, "name": "wind" }
                ],

                "cloudcoverthreshold": 60,
                "maxuv": null,
                "minuv": null,
                "maxtemp": 18,
                "mintemp": 5,

                "creationdate": "2026-05-05"
                }
            ]
        }
        */
        $categoryMap = Category::all() //Wenn kein gemapptes array gebraucht wird, ab hier löschen
            ->map(function ($category) {
                return $this->mapCategoryName($category->name);
            })
            ->filter()   // entfernt nulls
            ->values()   // reindexiert 0..n
            ->toArray();



        //ZUM TESTEN
        //$categories = ['head', 'upper', 'lower', 'feet']; 
        /*$tags = [
            'sun',
            'rain',
            'cold',
            'winter',
            'summer',
            'light',
            'sport',
            'casual',
            'wind'
        ];*/
        /*$testRec = [
            'head' => [
                [
                    'id' => 1,
                    'img' => '/storage/head1_cosi.png',
                    'tags' => ['sun', 'summer']
                ],
                [
                    'id' => 2,
                    'img' => '/storage/head2_cosi.png',
                    'tags' => ['rain', 'cold']
                ],
                [
                    'id' => 3,
                    'img' => '/storage/head3_cosi.png',
                    'tags' => []
                ],
            ],

            'upper_shirt' => [
                [
                    'id' => 4,
                    'img' => '/storage/shirt1_cosi.png',
                    'tags' => ['sun', 'light']
                ],
                [
                    'id' => 5,
                    'img' => '/storage/shirt2_cosi.png',
                    'tags' => ['cold', 'winter']
                ],
                [
                    'id' => 6,
                    'img' => '/storage/shirt3_cosi.png',
                    'tags' => ['casual']
                ],
                [
                    'id' => 19,
                    'img' => '/storage/shirt4_cosi.png',
                    'tags' => ['sport']
                ],
            ],

            'upper_pulli' => [
                [
                    'id' => 7,
                    'img' => '/storage/pulli1_cosi.png',
                    'tags' => ['cold', 'winter']
                ],
                [
                    'id' => 8,
                    'img' => '/storage/pulli2_cosi.png',
                    'tags' => ['casual']
                ],
                [
                    'id' => 9,
                    'img' => '/storage/pulli3_cosi.png',
                    'tags' => ['sport']
                ],
            ],

            'upper_jacke' => [
                [
                    'id' => 10,
                    'img' => '/storage/jacke1_cosi.png',
                    'tags' => ['rain', 'wind']
                ],
                [
                    'id' => 11,
                    'img' => '/storage/jacke2_cosi.png',
                    'tags' => ['cold', 'winter']
                ],
                [
                    'id' => 12,
                    'img' => '/storage/jacke3_cosi.png',
                    'tags' => ['light']
                ],
            ],

            'lower' => [
                [
                    'id' => 13,
                    'img' => '/storage/lower1_cosi.png',
                    'tags' => ['summer', 'light']
                ],
                [
                    'id' => 14,
                    'img' => '/storage/lower2_cosi.png',
                    'tags' => ['casual']
                ],
                [
                    'id' => 15,
                    'img' => '/storage/lower3_cosi.png',
                    'tags' => ['winter', 'cold']
                ],
            ],

            'feet' => [
                [
                    'id' => 16,
                    'img' => '/storage/feet1_cosi.png',
                    'tags' => ['sport']
                ],
                [
                    'id' => 17,
                    'img' => '/storage/feet2_cosi.png',
                    'tags' => ['rain']
                ],
                [
                    'id' => 18,
                    'img' => '/storage/feet3_cosi.png',
                    'tags' => ['casual', 'summer']
                ],
            ],
        ];*/

        return view('home', [
            'recommendations' => $recommendationsForFrontend/* NACH DEN TESTS AUF DAS ÄNDERN$recommendations*/,
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

    private function formatForFrontend($recommendations) {
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

        $result = [
            'head' => [],

            'upper_shirt' => [],
            'upper_pulli' => [],
            'upper_jacke' => [],

            'lower_pants' => [],
            'lower_tights' => [],

            'feet_socks' => [],
            'feet_shoes' => [],

            'hand' => [],
            'sunglasses' => [],
            'sunscreen' => [],
        ];

        foreach ($recommendations as $item) {
            if (!$item->category) continue;

            $key = $categoryMap[$item->category->name] ?? null;
            if (!$key) continue;

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
                // TAGS (id + name)
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