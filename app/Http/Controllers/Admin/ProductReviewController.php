<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReviewController extends Controller
{
    /**
     * Liste des avis produits
     */
    public function index(Request $request)
    {
        $query = ProductReview::with(['product', 'user']);
        
        // Filtre par produit
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        
        // Filtre par statut
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }
        
        // Filtre par note
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }
        
        // Recherche par nom client
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('comment', 'like', '%' . $request->search . '%');
            });
        }
        
        $reviews = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name']);
        
        // Statistiques
        $stats = [
            'total' => ProductReview::count(),
            'approved' => ProductReview::where('is_approved', true)->count(),
            'pending' => ProductReview::where('is_approved', false)->count(),
            'avg_rating' => ProductReview::where('is_approved', true)->avg('rating') ?? 0,
        ];
        
        return view('admin.reviews.index', compact('reviews', 'products', 'stats'));
    }
    
    /**
     * Afficher un avis spécifique
     */
    public function show(ProductReview $review)
    {
        $review->load(['product', 'user']);
        
        return view('admin.reviews.show', compact('review'));
    }
    
    /**
     * Approuver un avis
     */
    public function approve(ProductReview $review)
    {
        try {
            DB::beginTransaction();
            
            $review->update(['is_approved' => true]);
            $this->updateProductRatingStats($review->product);
            
            DB::commit();
            
            return redirect()
                ->back()
                ->with('success', 'Avis approuvé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de l\'approbation de l\'avis.');
        }
    }
    
    /**
     * Rejeter un avis
     */
    public function reject(ProductReview $review)
    {
        try {
            DB::beginTransaction();
            
            $review->update(['is_approved' => false]);
            $this->updateProductRatingStats($review->product);
            
            DB::commit();
            
            return redirect()
                ->back()
                ->with('success', 'Avis rejeté avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors du rejet de l\'avis.');
        }
    }
    
    /**
     * Supprimer un avis
     */
    public function destroy(ProductReview $review)
    {
        try {
            DB::beginTransaction();
            
            $product = $review->product;
            $review->delete();
            $this->updateProductRatingStats($product);
            
            DB::commit();
            
            return redirect()
                ->route('admin.reviews.index')
                ->with('success', 'Avis supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'avis.');
        }
    }
    
    /**
     * Approuver plusieurs avis (action groupée)
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:product_reviews,id',
        ]);
        
        try {
            DB::beginTransaction();
            
            $reviews = ProductReview::whereIn('id', $request->ids)->get();
            $productIds = [];
            
            foreach ($reviews as $review) {
                $review->update(['is_approved' => true]);
                $productIds[] = $review->product_id;
            }
            
            // Mettre à jour les statistiques des produits concernés
            foreach (array_unique($productIds) as $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $this->updateProductRatingStats($product);
                }
            }
            
            DB::commit();
            
            return redirect()
                ->back()
                ->with('success', count($request->ids) . ' avis approuvés avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de l\'approbation groupée.');
        }
    }
    
    /**
     * Rejeter plusieurs avis (action groupée)
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:product_reviews,id',
        ]);
        
        try {
            DB::beginTransaction();
            
            $reviews = ProductReview::whereIn('id', $request->ids)->get();
            $productIds = [];
            
            foreach ($reviews as $review) {
                $review->update(['is_approved' => false]);
                $productIds[] = $review->product_id;
            }
            
            // Mettre à jour les statistiques des produits concernés
            foreach (array_unique($productIds) as $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $this->updateProductRatingStats($product);
                }
            }
            
            DB::commit();
            
            return redirect()
                ->back()
                ->with('success', count($request->ids) . ' avis rejetés avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors du rejet groupé.');
        }
    }
    
    /**
     * Supprimer plusieurs avis (action groupée)
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:product_reviews,id',
        ]);
        
        try {
            DB::beginTransaction();
            
            $reviews = ProductReview::whereIn('id', $request->ids)->get();
            $productIds = [];
            
            foreach ($reviews as $review) {
                $productIds[] = $review->product_id;
                $review->delete();
            }
            
            // Mettre à jour les statistiques des produits concernés
            foreach (array_unique($productIds) as $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $this->updateProductRatingStats($product);
                }
            }
            
            DB::commit();
            
            return redirect()
                ->back()
                ->with('success', count($request->ids) . ' avis supprimés avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la suppression groupée.');
        }
    }
    
    /**
     * Mettre à jour les statistiques de notation d'un produit
     */
    private function updateProductRatingStats(Product $product)
    {
        $avgRating = ProductReview::where('product_id', $product->id)
            ->where('is_approved', true)
            ->avg('rating') ?? 0;
        
        $reviewsCount = ProductReview::where('product_id', $product->id)
            ->where('is_approved', true)
            ->count();
        
        $product->avg_rating = round($avgRating, 1);
        $product->reviews_count = $reviewsCount;
        $product->rating_percentage = $reviewsCount > 0 ? ($avgRating / 5) * 100 : 0;
        $product->save();
    }
    
    /**
     * Exporter les avis en CSV
     */
    public function export(Request $request)
    {
        $query = ProductReview::with(['product', 'user']);
        
        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }
        
        $reviews = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'avis_produits_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // En-têtes CSV
        fputcsv($handle, ['ID', 'Produit', 'Client', 'Note', 'Commentaire', 'Statut', 'Date']);
        
        foreach ($reviews as $review) {
            fputcsv($handle, [
                $review->id,
                $review->product->name ?? 'N/A',
                $review->customer_name ?? $review->user->name ?? 'Anonyme',
                $review->rating . '/5',
                $review->comment,
                $review->is_approved ? 'Approuvé' : 'En attente',
                $review->created_at->format('d/m/Y H:i'),
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
     * Statistiques des avis pour le dashboard (AJAX)
     */
    public function stats()
    {
        $stats = [
            'total' => ProductReview::count(),
            'approved' => ProductReview::where('is_approved', true)->count(),
            'pending' => ProductReview::where('is_approved', false)->count(),
            'avg_rating' => round(ProductReview::where('is_approved', true)->avg('rating') ?? 0, 1),
            'ratings_distribution' => [
                5 => ProductReview::where('rating', 5)->where('is_approved', true)->count(),
                4 => ProductReview::where('rating', 4)->where('is_approved', true)->count(),
                3 => ProductReview::where('rating', 3)->where('is_approved', true)->count(),
                2 => ProductReview::where('rating', 2)->where('is_approved', true)->count(),
                1 => ProductReview::where('rating', 1)->where('is_approved', true)->count(),
            ],
            'latest_reviews' => ProductReview::with(['product', 'user'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($review) {
                    return [
                        'id' => $review->id,
                        'product_name' => $review->product->name ?? 'N/A',
                        'customer' => $review->customer_name ?? $review->user->name ?? 'Anonyme',
                        'rating' => $review->rating,
                        'status' => $review->is_approved ? 'approuvé' : 'en attente',
                        'created_at' => $review->created_at->diffForHumans(),
                    ];
                }),
        ];
        
        return response()->json($stats);
    }
}