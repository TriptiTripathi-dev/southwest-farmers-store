<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Recipe;
use App\Models\RecipeStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Cookbook Builder, moved to the Store side (item 6) as a genuinely working
 * feature — the Warehouse version's create form never actually submitted
 * (action="#", step inputs had no name attributes at all).
 */
class StoreCookbookController extends Controller
{
    public function index()
    {
        $storeId = Auth::user()->store_id;
        $recipes = Recipe::whereHas('menuItem', fn ($q) => $q->where('store_id', $storeId))
            ->with('menuItem')
            ->latest()
            ->get();

        return view('store.cookbook.index', compact('recipes'));
    }

    public function create()
    {
        $storeId = Auth::user()->store_id;
        $menuItems = MenuItem::where('store_id', $storeId)->orderBy('name')->get();

        return view('store.cookbook.create', compact('menuItems'));
    }

    public function store(Request $request)
    {
        $storeId = Auth::user()->store_id;

        $validated = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prep_time_minutes' => 'nullable|integer|min:0',
            'cook_time_minutes' => 'nullable|integer|min:0',
            'yield_quantity' => 'nullable|numeric|min:0',
            'yield_unit' => 'nullable|string|max:50',
            'steps' => 'nullable|array',
            'steps.*.instruction' => 'required_with:steps|string',
            'steps.*.timer_minutes' => 'nullable|integer|min:0',
        ]);

        $menuItem = MenuItem::where('id', $validated['menu_item_id'])->where('store_id', $storeId)->firstOrFail();

        $recipe = DB::transaction(function () use ($validated, $menuItem) {
            $recipe = Recipe::create([
                'menu_item_id' => $menuItem->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'prep_time_minutes' => $validated['prep_time_minutes'] ?? null,
                'cook_time_minutes' => $validated['cook_time_minutes'] ?? null,
                'yield_quantity' => $validated['yield_quantity'] ?? null,
                'yield_unit' => $validated['yield_unit'] ?? null,
            ]);

            foreach (($validated['steps'] ?? []) as $i => $step) {
                RecipeStep::create([
                    'recipe_id' => $recipe->id,
                    'step_number' => $i + 1,
                    'instruction' => $step['instruction'],
                    'timer_minutes' => $step['timer_minutes'] ?? null,
                ]);
            }

            return $recipe;
        });

        return redirect()->route('store.cookbook.show', $recipe->id)->with('success', 'Recipe created successfully.');
    }

    public function show($id)
    {
        $recipe = $this->scopedRecipe($id);
        return view('store.cookbook.show', compact('recipe'));
    }

    public function edit($id)
    {
        $recipe = $this->scopedRecipe($id);
        $storeId = Auth::user()->store_id;
        $menuItems = MenuItem::where('store_id', $storeId)->orderBy('name')->get();

        return view('store.cookbook.edit', compact('recipe', 'menuItems'));
    }

    public function update(Request $request, $id)
    {
        $recipe = $this->scopedRecipe($id);
        $storeId = Auth::user()->store_id;

        $validated = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prep_time_minutes' => 'nullable|integer|min:0',
            'cook_time_minutes' => 'nullable|integer|min:0',
            'yield_quantity' => 'nullable|numeric|min:0',
            'yield_unit' => 'nullable|string|max:50',
            'steps' => 'nullable|array',
            'steps.*.instruction' => 'required_with:steps|string',
            'steps.*.timer_minutes' => 'nullable|integer|min:0',
        ]);

        MenuItem::where('id', $validated['menu_item_id'])->where('store_id', $storeId)->firstOrFail();

        DB::transaction(function () use ($recipe, $validated) {
            $recipe->update([
                'menu_item_id' => $validated['menu_item_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'prep_time_minutes' => $validated['prep_time_minutes'] ?? null,
                'cook_time_minutes' => $validated['cook_time_minutes'] ?? null,
                'yield_quantity' => $validated['yield_quantity'] ?? null,
                'yield_unit' => $validated['yield_unit'] ?? null,
            ]);

            $recipe->steps()->delete();
            foreach (($validated['steps'] ?? []) as $i => $step) {
                RecipeStep::create([
                    'recipe_id' => $recipe->id,
                    'step_number' => $i + 1,
                    'instruction' => $step['instruction'],
                    'timer_minutes' => $step['timer_minutes'] ?? null,
                ]);
            }
        });

        return redirect()->route('store.cookbook.show', $recipe->id)->with('success', 'Recipe updated successfully.');
    }

    public function destroy($id)
    {
        $recipe = $this->scopedRecipe($id);
        $recipe->steps()->delete();
        $recipe->delete();

        return redirect()->route('store.cookbook.index')->with('success', 'Recipe deleted successfully.');
    }

    protected function scopedRecipe($id): Recipe
    {
        $storeId = Auth::user()->store_id;
        return Recipe::whereHas('menuItem', fn ($q) => $q->where('store_id', $storeId))
            ->with(['menuItem', 'steps'])
            ->findOrFail($id);
    }
}
