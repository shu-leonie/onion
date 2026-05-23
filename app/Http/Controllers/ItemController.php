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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('items.index', [
            'items' => Item::with('category', 'tags')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('items.create', [
            'categories' => Category::all(),
            'tags' => Tag::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validate($request);

        if (! empty($validated['tags'])) {
            $tags = $validated['tags'];
            unset($validated['tags']);
        } else {
            $tags = [];
        }

        // set user_id
        $validated['user_id'] = $request->user()->id;

        // get image
        $image = $request->file('filepath');

        try {

            if (! empty($image)) {
                // save image
                $filepath = $this->saveImage($image);
                $validated['filepath'] = $filepath;
            }

            $item = Item::create($validated);
            $item->tags()->attach($tags);

            $status = 'success';
            $message = 'Das Kleidungsstück wurde erfolgreich gespeichert.';
        } catch (Exception $e) {
            $status = 'error';
            $message = 'Beim Speichern des Kleidungsstücks ist ein Fehler aufgetreten.';
        }

        return redirect()->route('items.index')->with($status, $message)
            ->with('item_id', $item->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        return view('items.edit', [
            'item' => $item,
            'categories' => Category::all(),
            'tags' => Tag::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $this->validate($request);

        if (! empty($validated['tags'])) {
            $tags = $validated['tags'];
            unset($validated['tags']);
        } else {
            $tags = [];
        }

        // set user_id
        $validated['user_id'] = $request->user()->id;

        // get image
        $image = $request->file('filepath');

        try {

            if (! empty($image)) {
                if (! empty($item->filepath)) {
                    // delete old image
                    Storage::delete($item->filepath);
                }
                // save image
                $filepath = $this->saveImage($image);
                $validated['filepath'] = $filepath;
            }

            $item->update($validated);
            $item->tags()->sync($tags);

            $status = 'success';
            $message = 'Die Änderungen wurden erfolgreich gespeichert.';
        } catch (Exception $e) {
            $status = 'error';
            $message = 'Beim Speichern der Änderungen ist ein Fehler aufgetreten.';
        }

        return redirect()->route('items.index')->with($status, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        try {
            if (! empty($item->filepath)) {
                // delete image
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

    /**
     * Validation
     */
    private function validate(Request $request)
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
            ? 'required|file|image|max:2048'
            : 'sometimes|file|image|max:2048',
        ]);
    }

    /**
     * save image
     */
    private function saveImage($image)
    {
        $filename = Carbon::now()->format('Y-m-d-H_i_s').'_'.$image->getClientOriginalName();
        // clean image name
        $filename = preg_replace('~[^\w\d\-_\(\)\[\]\.]~', '', $filename);
        // save image
        $filepath = $image->storeAs('images/items', $filename, 'public');

        return $filepath;
    }
}
