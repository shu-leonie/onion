<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Tag;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        return view('items.index', [
            'items' => Item::with('category', 'tags')->get(),
        ]);
    }

    public function create()
    {
        return view('items.create', [
            'categories' => Category::all(),
            'tags' => Tag::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request); 
        //validateRequest statt vallidate... (laravel hat bereits eune validate funktion // 
        // nicht das wir desshalb irgendwelche random bugs bekommen)

        if (! empty($validated['tags'])) {
            $tags = $validated['tags'];
            unset($validated['tags']);
        } else {
            $tags = [];
        }

        $validated['user_id'] = $request->user()->id;
        $image = $request->file('filepath');

        try {
            if (! empty($image)) {
                $filepath = $this->saveImage($image);
                $validated['filepath'] = $filepath;
            }

            $item = Item::create($validated);
            $item->tags()->attach($tags);

            return response()->json([
                'success' => true,
                'message' => 'Das Kleidungsstück wurde erfolgreich gespeichert.',
                'item' => $item,
                'item_id' => $item->id,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Beim Speichern des Kleidungsstücks ist ein Fehler aufgetreten.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Item $item)
    {
        
    }

    public function edit(Item $item)
    {
        return view('items.edit', [
            'item' => $item,
            'categories' => Category::all(),
            'tags' => Tag::all(),
        ]);
    }

    public function update(Request $request, Item $item)
    {
        $validated = $this->validateRequest($request);

        if (! empty($validated['tags'])) {
            $tags = $validated['tags'];
            unset($validated['tags']);
        } else {
            $tags = [];
        }

        $validated['user_id'] = $request->user()->id;
        $image = $request->file('filepath');

        try {
            if (! empty($image)) {
                if (! empty($item->filepath)) {
                    Storage::delete($item->filepath);
                }
                $filepath = $this->saveImage($image);
                $validated['filepath'] = $filepath;
            }

            $item->update($validated);
            $item->tags()->sync($tags);
            
            $status = 'success';
            $message = 'Die Änderungen wurden erfolgreich gespeichert.';

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => $status,
                    'item' => $item,
                    'message' => $message,
                ], 200);
            }

            return redirect()->back()->with($status, $message);

        } catch (Exception $e) {
            $status = 'error';
            $message = 'Beim Speichern der Änderungen ist ein Fehler aufgetreten.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => $status,
                    'message' => $message,
                ], 500);
            }

            return redirect()->back()->with($status, $message);
        }
    }

    public function destroy(Item $item)
    {
        try {
            if (! empty($item->filepath)) {
                Storage::disk('public')->delete($item->filepath);
            }
            $item->delete();
            $status = 'success';
            $message = 'Das Kleidungsstück wurde gelöscht.';
        } catch (Exception $e) {
            $status = 'error';
            $message = 'Beim Löschen des Kleidungsstücks ist ein Fehler aufgetreten.';
        }

        return redirect(route('items.index'))->with($status, $message);
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'is_waterproof' => 'nullable|boolean',
            'min_temperature' => 'nullable|integer',
            'max_temperature' => 'nullable|integer',
            'min_uv_index' => 'nullable|integer',
            'max_uv_index' => 'nullable|integer',
            'cloud_cover_threshold' => 'nullable|integer|between:0,100',
            'filepath' => $request->isMethod('post')
            ? 'required|file|image|max:10240' //auf 10mb angehoben 
            : 'sometimes|file|image|max:10240',
        ]);
    }

    private function saveImage($image)
    {
        $filename = Carbon::now()->format('Y-m-d-H_i_s').'_'.$image->getClientOriginalName();
        $filename = preg_replace('~[^\w\d\-_\(\)\[\]\.]~', '', $filename);
        $filepath = $image->storeAs('images/items', $filename, 'public');

        return $filepath;
    }
}