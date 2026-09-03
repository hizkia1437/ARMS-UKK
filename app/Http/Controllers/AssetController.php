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
        $category = $request->input('category');
        $condition = $request->input('condition');
        $sortBy = in_array($request->input('sort_by'), ['name', 'asset_code', 'category', 'condition', 'location', 'created_at']) ? $request->input('sort_by') : 'created_at';
        $sortDir = strtolower($request->input('sort_dir')) === 'asc' ? 'asc' : 'desc';

        $query = Asset::query();

        $query->when($search, function ($q, $search) {
            $q->where('asset_code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%")
                ->orWhere('condition', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%");
        });

        $query->when($category, function ($q, $category) {
            $q->where('category', $category);
        });

        $query->when($condition, function ($q, $condition) {
            $q->where('condition', $condition);
        });

        $assets = $query->orderBy($sortBy, $sortDir)->paginate(10)->withQueryString();
        $categories = Asset::select('category')->distinct()->pluck('category');

        return view('assets.index', compact('assets', 'search', 'category', 'condition', 'sortBy', 'sortDir', 'categories'));
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
