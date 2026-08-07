<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Http\Requests\AssetRequest;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $assets = Asset::query()
            ->when($search, function ($query, $search) {
                $query->where('asset_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('condition', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('assets.index', compact('assets', 'search'));
    }

    public function create()
    {
        // Helper to suggest next asset code
        $nextCode = 'AST-' . str_pad((Asset::max('id') + 1), 4, '0', STR_PAD_LEFT);
        return view('assets.create', compact('nextCode'));
    }

    public function store(AssetRequest $request)
    {
        Asset::create($request->validated());

        return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit', compact('asset'));
    }

    public function update(AssetRequest $request, Asset $asset)
    {
        $asset->update($request->validated());

        return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }

    public function updateCondition(Request $request, Asset $asset)
    {
        $user = $request->user();
        if (!$user->isAdmin() && !$user->isStaff()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'condition' => 'required|in:Good,Needs Repair,Damaged',
        ]);

        $asset->update(['condition' => $request->condition]);

        return redirect()->route('assets.index')->with('success', 'Asset condition updated to ' . $request->condition . '.');
    }
}
