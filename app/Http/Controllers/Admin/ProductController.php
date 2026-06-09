<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Affiche la liste des produits avec filtres
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filtre de recherche
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        // Filtre par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'featured':
                    $query->where('is_featured', true);
                    break;
                case 'low_stock':
                    $query->where('track_stock', true)
                        ->whereRaw('stock_quantity <= stock_alert_threshold')
                        ->where('stock_quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('track_stock', true)
                        ->where('stock_quantity', 0);
                    break;
            }
        }

        // Filtre par prix
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        // Tri
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['name', 'price', 'stock_quantity', 'created_at', 'updated_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $categories = Category::with('parent')
            ->orderBy('name')
            ->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Enregistre un nouveau produit
     */
    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $validated['slug'] = $this->generateUniqueSlug($validated['name']);
        $validated['track_stock'] = $request->boolean('track_stock', false);
        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;
        $validated['stock_alert_threshold'] = $validated['stock_alert_threshold'] ?? 5;

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->uploadImage($request->file('image'));
        }

        if ($request->hasFile('image_secondary')) {
            $validated['image_secondary'] = $this->uploadImage($request->file('image_secondary'));
        }

        $product = Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produit créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Product $product)
    {
        $categories = Category::with('parent')
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Met à jour un produit
     */
    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product->id);

        $validated['slug'] = Str::slug($validated['name']);
        
        if (Product::where('slug', $validated['slug'])->where('id', '!=', $product->id)->exists()) {
            $validated['slug'] = $validated['slug'] . '-' . uniqid();
        }

        $validated['track_stock'] = $request->boolean('track_stock', $product->track_stock);
        $validated['is_featured'] = $request->boolean('is_featured', $product->is_featured);
        $validated['is_active'] = $request->boolean('is_active', $product->is_active);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                $this->deleteImage($product->image_path);
            }
            $validated['image_path'] = $this->uploadImage($request->file('image'));
        }

        if ($request->hasFile('image_secondary')) {
            if ($product->image_secondary) {
                $this->deleteImage($product->image_secondary);
            }
            $validated['image_secondary'] = $this->uploadImage($request->file('image_secondary'));
        }

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Supprime un produit
     */
    public function destroy(Product $product)
    {
        $hasOrders = $product->orderItems()->exists();

        if ($hasOrders) {
            return back()->withErrors([
                'error' => 'Impossible de supprimer ce produit car il a déjà été commandé.'
            ]);
        }

        if ($product->image_path) {
            $this->deleteImage($product->image_path);
        }

        if ($product->image_secondary) {
            $this->deleteImage($product->image_secondary);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produit supprimé avec succès.');
    }

    /**
     * Active/Désactive un produit
     */
    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'activé' : 'désactivé';

        return redirect()
            ->back()
            ->with('success', "Produit {$status} avec succès.");
    }

    /**
     * Active/Désactive la mise en avant d'un produit
     */
    public function toggleFeatured(Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);

        $status = $product->is_featured ? 'mis en avant' : 'retiré des avant-premières';

        return redirect()
            ->back()
            ->with('success', "Produit {$status} avec succès.");
    }

    /**
     * Met à jour le stock en masse
     */
    public function bulkUpdateStock(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.stock_quantity' => 'required|integer|min:0',
        ]);

        $updated = 0;
        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            if ($product && $product->track_stock) {
                $product->update(['stock_quantity' => $productData['stock_quantity']]);
                $updated++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Stock mis à jour pour {$updated} produit(s)."
        ]);
    }

    /**
     * Exporte les produits en CSV
     */
    public function export(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->get();

        $filename = 'produits_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');

        // En-têtes CSV
        fputcsv($handle, [
            'ID', 'Nom', 'Slug', 'Catégorie', 'Prix (USD)', 
            'Stock', 'Seuil alerte', 'Suivi stock', 'Mis en avant', 
            'Actif', 'Créé le', 'Mis à jour le'
        ]);

        foreach ($products as $product) {
            fputcsv($handle, [
                $product->id,
                $product->name,
                $product->slug,
                $product->category?->name,
                $product->price,
                $product->stock_quantity,
                $product->stock_alert_threshold,
                $product->track_stock ? 'Oui' : 'Non',
                $product->is_featured ? 'Oui' : 'Non',
                $product->is_active ? 'Oui' : 'Non',
                $product->created_at,
                $product->updated_at,
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->withHeaders([
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
    }

    /**
     * Duplique un produit
     */
    public function duplicate(Product $product)
    {
        $newProduct = $product->duplicate();

        return redirect()
            ->route('admin.products.edit', $newProduct)
            ->with('success', 'Produit dupliqué avec succès. Veuillez ajuster le nom et le slug.');
    }

    /**
     * Récupère les produits en stock bas (pour notifications)
     */
    public function lowStock()
    {
        $products = Product::where('track_stock', true)
            ->whereRaw('stock_quantity <= stock_alert_threshold')
            ->where('stock_quantity', '>', 0)
            ->orderByRaw('stock_quantity / stock_alert_threshold ASC')
            ->limit(10)
            ->get();

        return response()->json([
            'count' => $products->count(),
            'products' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock' => $product->stock_quantity,
                    'threshold' => $product->stock_alert_threshold,
                    'url' => route('admin.products.edit', $product),
                ];
            })
        ]);
    }

    /**
     * Validation des données produit
     */
    private function validateProduct(Request $request, ?int $ignoreId = null): array
    {
        $uniqueRule = Rule::unique('products', 'name');
        if ($ignoreId) {
            $uniqueRule->ignore($ignoreId);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255', $uniqueRule],
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0|max:999999.99',
            'description' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_secondary' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'stock_quantity' => 'nullable|integer|min:0',
            'stock_alert_threshold' => 'nullable|integer|min:0',
            'track_stock' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'unit' => 'nullable|string|max:50',
            'weight' => 'nullable|numeric|min:0|max:9999.99',
        ]);
    }

    /**
     * Génère un slug unique
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Upload une image
     */
    private function uploadImage($file): string
    {
        return $file->store('products', 'public');
    }

    /**
     * Supprime une image
     */
    private function deleteImage(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}