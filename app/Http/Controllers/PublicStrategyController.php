<?php

namespace App\Http\Controllers;

use App\Models\PublicStrategy;
use App\Services\ImgBBService;
use Illuminate\Http\Request;

class PublicStrategyController extends Controller
{
    public function index()
    {
        $strategies = PublicStrategy::all();
        return view('admin.public_strategies.index', compact('strategies'));
    }

    public function create()
    {
        return view('admin.public_strategies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'description'=> 'nullable|string',
            'images.*'   => 'nullable|image|max:4096',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            $imgbb = new ImgBBService();
            foreach ($request->file('images') as $image) {
                $url = $imgbb->upload($image);
                if ($url) {
                    $imagePaths[] = $url;
                } else {
                    // Fallback to local storage
                    $imagePaths[] = asset('storage/' . $image->store('strategies', 'public'));
                }
            }
        }

        PublicStrategy::create([
            'title'       => $request->title,
            'description' => $request->description,
            'images'      => $imagePaths,
        ]);

        return redirect()->route('admin.public-strategies.index')->with('success', 'Strategy created successfully.');
    }

    public function edit(PublicStrategy $publicStrategy)
    {
        return view('admin.public_strategies.edit', compact('publicStrategy'));
    }

    public function update(Request $request, PublicStrategy $publicStrategy)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'description'=> 'nullable|string',
            'images.*'   => 'nullable|image|max:4096',
        ]);

        $imagePaths = $publicStrategy->images ?? [];

        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $removePath) {
                // Only delete local files (not ImgBB URLs)
                if (!str_starts_with($removePath, 'http')) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($removePath);
                }
                $imagePaths = array_values(array_diff($imagePaths, [$removePath]));
            }
        }

        if ($request->hasFile('images')) {
            $imgbb = new ImgBBService();
            foreach ($request->file('images') as $image) {
                $url = $imgbb->upload($image);
                if ($url) {
                    $imagePaths[] = $url;
                } else {
                    $imagePaths[] = asset('storage/' . $image->store('strategies', 'public'));
                }
            }
        }

        $publicStrategy->update([
            'title'       => $request->title,
            'description' => $request->description,
            'images'      => $imagePaths,
        ]);

        return redirect()->route('admin.public-strategies.index')->with('success', 'Strategy updated successfully.');
    }

    public function destroy(PublicStrategy $publicStrategy)
    {
        // ImgBB images are managed externally, no local deletion needed
        $publicStrategy->delete();
        return redirect()->route('admin.public-strategies.index')->with('success', 'Strategy deleted successfully.');
    }
}

