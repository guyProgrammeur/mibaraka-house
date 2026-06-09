<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Ajouter un avis sur un produit
     */
    public function store(Request $request, Product $product)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'customer_name' => 'required_if:guest,true|nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Vérifier si l'utilisateur a déjà laissé un avis
        $existingReview = ProductReview::where('product_id', $product->id)
            ->when(Auth::check(), function($q) {
                return $q->where('user_id', Auth::id());
            })
            ->when(!Auth::check() && $request->filled('customer_name'), function($q) use ($request) {
                return $q->where('customer_name', $request->customer_name);
            })
            ->exists();
        
        if ($existingReview) {
            return redirect()->back()
                ->with('error', 'Vous avez déjà laissé un avis sur ce produit.');
        }
        
        // Créer l'avis
        $review = new ProductReview();
        $review->product_id = $product->id;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->is_approved = false; // À approuver par admin
        
        if (Auth::check()) {
            $review->user_id = Auth::id();
            $review->customer_name = Auth::user()->name;
        } else {
            $review->customer_name = $request->customer_name;
        }
        
        $review->save();
        
        // Mettre à jour la note moyenne du produit
        $this->updateProductRating($product);
        
        return redirect()->back()
            ->with('success', 'Merci pour votre avis ! Il sera publié après validation.');
    }
    
    /**
     * Supprimer un avis
     */
    public function destroy(ProductReview $review)
    {
        // Vérifier les permissions
        if (Auth::check() && (Auth::id() === $review->user_id || Auth::user()->isAdmin())) {
            $product = $review->product;
            $review->delete();
            
            // Mettre à jour la note moyenne
            $this->updateProductRating($product);
            
            return redirect()->back()
                ->with('success', 'Votre avis a été supprimé.');
        }
        
        return redirect()->back()
            ->with('error', 'Vous n\'êtes pas autorisé à supprimer cet avis.');
    }
    
    /**
     * Récupérer la liste des avis (AJAX)
     */
    public function list(Product $product)
    {
        $reviews = $product->approvedReviews()
            ->with('user')
            ->latest()
            ->paginate(10);
        
        return response()->json([
            'success' => true,
            'html' => view('client.components.reviews-list', compact('reviews'))->render(),
            'total' => $reviews->total(),
        ]);
    }
    
    /**
     * Mettre à jour la note moyenne du produit
     */
    private function updateProductRating(Product $product)
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
}