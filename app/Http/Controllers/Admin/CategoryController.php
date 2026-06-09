<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Liste des catégories
     */
    public function index(Request $request)
    {
        $query = Category::with('parent');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            if ($request->type === 'main') {
                $query->whereNull('parent_id');
            } elseif ($request->type === 'sub') {
                $query->whereNotNull('parent_id');
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->orderBy('position')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Category::count(),
            'main' => Category::whereNull('parent_id')->count(),
            'sub' => Category::whereNotNull('parent_id')->count(),
            'active' => Category::where('is_active', true)->count(),
        ];

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Enregistrer une catégorie
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        // Vérifier qu'une catégorie ne peut pas être son propre parent
        if ($validated['parent_id'] ?? null) {
            $parent = Category::find($validated['parent_id']);
            if ($parent && $parent->parent_id === null) {
                // Parent valide
            }
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['position'] = $validated['position'] ?? 0;

        // Vérifier l'unicité du slug
        $slug = $validated['slug'];
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $validated['slug'] . '-' . $counter;
            $counter++;
        }
        $validated['slug'] = $slug;

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Afficher une catégorie
     */
    public function show(Category $category)
    {
        $products = $category->allProducts()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $subcategories = $category->children()
            ->where('is_active', true)
            ->orderBy('position')
            ->get();

        return view('admin.categories.show', compact('category', 'products', 'subcategories'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Mettre à jour une catégorie
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id),
            ],
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        // Empêcher une catégorie d'être son propre parent
        if (isset($validated['parent_id']) && $validated['parent_id'] == $category->id) {
            return back()->withErrors(['parent_id' => 'Une catégorie ne peut pas être son propre parent.']);
        }

        // Empêcher de créer une boucle infinie (parent qui devient enfant de son enfant)
        if (isset($validated['parent_id'])) {
            $parent = Category::find($validated['parent_id']);
            if ($parent && $parent->parent_id == $category->id) {
                return back()->withErrors(['parent_id' => 'Cette opération créerait une boucle hiérarchique.']);
            }
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        
        // Vérifier l'unicité du slug
        if ($category->name !== $validated['name']) {
            $slug = $validated['slug'];
            $counter = 1;
            while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $validated['slug'] . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprimer une catégorie
     */
    public function destroy(Category $category)
    {
        // Vérifier les produits rattachés
        $productCount = $category->allProducts()->count();

        if ($productCount > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', "Impossible de supprimer cette catégorie. {$productCount} produit(s) y sont rattachés.");
        }

        // Vérifier les sous-catégories
        $childCount = $category->children()->count();

        if ($childCount > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', "Impossible de supprimer cette catégorie. {$childCount} sous-catégorie(s) lui sont rattachées.");
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }

    /**
     * Activer/Désactiver une catégorie
     */
    public function toggleActive(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'activée' : 'désactivée';

        return redirect()
            ->back()
            ->with('success', "Catégorie {$status} avec succès.");
    }

    /**
     * Réordonner les catégories (AJAX)
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'positions' => 'required|array',
            'positions.*.id' => 'required|exists:categories,id',
            'positions.*.position' => 'required|integer|min:0',
        ]);

        foreach ($request->positions as $item) {
            Category::where('id', $item['id'])->update(['position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Dupliquer une catégorie
     */
    public function duplicate(Category $category)
    {
        $newCategory = $category->duplicate();

        return redirect()
            ->route('admin.categories.edit', $newCategory)
            ->with('success', 'Catégorie dupliquée avec succès.');
    }

    /**
     * Export des catégories (CSV)
     */
    public function export()
    {
        $categories = Category::with('parent')
            ->orderBy('position')
            ->get();

        $filename = 'categories_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');

        // En-têtes CSV
        fputcsv($handle, ['ID', 'Nom', 'Slug', 'Parent', 'Description', 'Position', 'Active', 'Créé le']);

        foreach ($categories as $category) {
            fputcsv($handle, [
                $category->id,
                $category->name,
                $category->slug,
                $category->parent ? $category->parent->name : '',
                $category->description,
                $category->position,
                $category->is_active ? 'Oui' : 'Non',
                $category->created_at->format('d/m/Y H:i'),
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent)
            ->withHeaders([
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
    }

    /**
     * Import des catégories depuis CSV (formulaire)
     */
    public function showImportForm()
    {
        return view('admin.categories.import');
    }

    /**
     * Import des catégories depuis CSV (traitement)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        
        // Lire les en-têtes
        $headers = fgetcsv($handle);
        
        $imported = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);
            
            try {
                Category::create([
                    'name' => $data['nom'] ?? $data['name'] ?? null,
                    'description' => $data['description'] ?? null,
                    'position' => $data['position'] ?? 0,
                    'is_active' => ($data['active'] ?? 'Oui') === 'Oui',
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Erreur ligne " . ($imported + 2) . ": " . $e->getMessage();
            }
        }

        fclose($handle);

        $message = "{$imported} catégorie(s) importée(s) avec succès.";
        
        if (!empty($errors)) {
            return back()->with('warning', $message)->with('errors', $errors);
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', $message);
    }

    /**
     * Obtenir l'arbre des catégories pour AJAX
     */
    public function tree()
    {
        $tree = Category::getTree();
        
        return response()->json($tree);
    }
}