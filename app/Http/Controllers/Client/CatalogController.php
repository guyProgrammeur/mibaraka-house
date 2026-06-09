<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Currency;
use App\Models\Announcement;
use App\Models\Company;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Detection\MobileDetect;

class CatalogController extends Controller
{
    /**
     * Déterminer le nombre de produits par page selon l'appareil
     */
    protected function getPerPage(Request $request)
    {
        $detect = new MobileDetect();
        
        if ($detect->isMobile()) {
            return 12;
        }
        
        return 24;
    }
    
    /**
     * Vérifier si l'appareil est mobile
     */
    protected function isMobile(): bool
    {
        $detect = new MobileDetect();
        return $detect->isMobile();
    }
    
    /**
     * Page d'accueil du catalogue
     */
    public function index(Request $request)
    {
        $company = Company::instance();
        $currency = $this->getCurrency($request);
        
        // Annonces
        $announcementsByPosition = [
            'top' => Announcement::active('top')->get(),
            'middle' => Announcement::active('middle')->get(),
            'bottom' => Announcement::active('bottom')->get(),
        ];
        
        $isMobile = $this->isMobile();
        
        // Produits phares
        $featuredLimit = $isMobile ? 8 : 12;
        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit($featuredLimit)
            ->get();
        
        // Nouveautés
        $newLimit = $isMobile ? 6 : 8;
        $newProducts = Product::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit($newLimit)
            ->get();
        
        // Meilleurs ventes
        $bestSellers = Product::where('is_active', true)
            ->withCount(['orderItems as total_sold' => function($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->orderBy('total_sold', 'desc')
            ->limit($newLimit)
            ->get();
        
        // Catégories principales
        $categories = Category::with(['children' => function($q) {
                $q->where('is_active', true)->orderBy('position');
            }])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('position')
            ->get();
        
        // Statistiques
        $productCount = Product::where('is_active', true)->count();
        $averageRating = Product::where('is_active', true)->avg('avg_rating') ?? 4.8;
        
        return view('client.catalog.index', compact(
            'company',
            'featuredProducts',
            'newProducts',
            'bestSellers',
            'categories',
            'productCount',
            'averageRating',
            'currency',
            'announcementsByPosition'
        ));
    }
    
    /**
     * Page d'une catégorie
     */
    public function category(Request $request, string $slug)
    {
        $company = Company::instance();
        $currency = $this->getCurrency($request);
        $perPage = $this->getPerPage($request);
        
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        
        $announcementsByPosition = [
            'top' => Announcement::active('top')->get(),
            'middle' => Announcement::active('middle')->get(),
            'bottom' => Announcement::active('bottom')->get(),
        ];
        
        $products = $category->getAllProducts()
            ->where('is_active', true)
            ->when($request->filled('sort'), function($q) use ($request) {
                switch($request->sort) {
                    case 'price_asc':
                        return $q->orderBy('price', 'asc');
                    case 'price_desc':
                        return $q->orderBy('price', 'desc');
                    case 'name_asc':
                        return $q->orderBy('name', 'asc');
                    case 'name_desc':
                        return $q->orderBy('name', 'desc');
                    case 'rating_desc':
                        return $q->orderBy('avg_rating', 'desc');
                    case 'newest':
                        return $q->orderBy('created_at', 'desc');
                    default:
                        return $q->orderBy('created_at', 'desc');
                }
            })
            ->when($request->filled('min_price'), function($q) use ($request) {
                return $q->where('price', '>=', $request->min_price);
            })
            ->when($request->filled('max_price'), function($q) use ($request) {
                return $q->where('price', '<=', $request->max_price);
            })
            ->paginate($perPage)
            ->withQueryString();
        
        if ($request->ajax()) {
            return view('client.components.product-grid', compact('products', 'currency'))->render();
        }
        
        $subcategories = $category->children()
            ->where('is_active', true)
            ->orderBy('position')
            ->get();
        
        $priceRange = [
            'min' => Product::where('category_id', $category->id)
                ->orWhereIn('category_id', $subcategories->pluck('id'))
                ->min('price') ?? 0,
            'max' => Product::where('category_id', $category->id)
                ->orWhereIn('category_id', $subcategories->pluck('id'))
                ->max('price') ?? 1000,
        ];
        
        return view('client.catalog.category', compact(
            'company',
            'category',
            'products',
            'subcategories',
            'priceRange',
            'currency',
            'announcementsByPosition',
            'perPage'
        ));
    }
    
    /**
     * Page d'un produit
     */
    public function product(Request $request, string $slug)
    {
        $company = Company::instance();
        $currency = $this->getCurrency($request);
        
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        
        // Incrémenter les vues
        $product->incrementViews();
        
        $announcementsByPosition = [
            'top' => Announcement::active('top')->get(),
            'middle' => Announcement::active('middle')->get(),
            'bottom' => Announcement::active('bottom')->get(),
        ];
        
        $perPage = $this->isMobile() ? 5 : 10;
        $reviews = $product->approvedReviews()
            ->with('user')
            ->latest()
            ->paginate($perPage);
        
        $reviewsStats = [
            'total' => $product->reviews_count,
            'average' => $product->avg_rating,
            'percentage' => $product->rating_percentage,
            'distribution' => $this->getRatingDistribution($product),
        ];
        
        $relatedLimit = $this->isMobile() ? 4 : 6;
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit($relatedLimit)
            ->get();
        
        $recentProducts = $this->getRecentProducts($product->id);
        
        return view('client.catalog.product', compact(
            'company',
            'product',
            'relatedProducts',
            'recentProducts',
            'reviews',
            'reviewsStats',
            'currency',
            'announcementsByPosition'
        ));
    }
    
    /**
     * Recherche de produits
     */
   /**
 * Recherche de produits
 */
public function search(Request $request)
{
    $company = Company::instance();
    $currency = $this->getCurrency($request);
    $query = trim($request->get('q', ''));
    $perPage = $this->getPerPage($request);
    
    // Échapper et nettoyer la recherche pour éviter les injections
    $query = strip_tags($query);
    $query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');
    
    $announcementsByPosition = [
        'top' => Announcement::active('top')->get(),
        'middle' => Announcement::active('middle')->get(),
        'bottom' => Announcement::active('bottom')->get(),
    ];
    
    // Construction de la requête de recherche sécurisée
    $products = Product::where('is_active', true);
    
    if (!empty($query)) {
        $products->where(function($q) use ($query) {
            $q->where('name', 'like', '%' . addcslashes($query, '%_') . '%')
              ->orWhere('description', 'like', '%' . addcslashes($query, '%_') . '%');
            // Suppression de la recherche par SKU car la colonne n'existe pas
            // Si vous ajoutez la colonne SKU plus tard, décommentez la ligne ci-dessous
            // ->orWhere('sku', 'like', '%' . addcslashes($query, '%_') . '%');
        });
    }
    
    // Application du tri
    if ($request->filled('sort')) {
        switch($request->sort) {
            case 'price_asc':
                $products->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $products->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $products->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $products->orderBy('name', 'desc');
                break;
            case 'newest':
                $products->orderBy('created_at', 'desc');
                break;
            case 'rating_desc':
                $products->orderBy('avg_rating', 'desc');
                break;
            default:
                $products->orderBy('created_at', 'desc');
        }
    } else {
        $products->orderBy('created_at', 'desc');
    }
    
    $products = $products->paginate($perPage)->withQueryString();
    
    // Redirection si recherche vide
    if (empty($query)) {
        return redirect()->route('client.catalog');
    }
    
    // Réponse AJAX pour infinite scroll
    if ($request->ajax()) {
        return view('client.components.product-grid', compact('products', 'currency'))->render();
    }
    
    return view('client.catalog.search', compact(
        'company',
        'products',
        'query',
        'currency',
        'announcementsByPosition'
    ));
}
    
    /**
     * Récupérer la devise sélectionnée
     */
    private function getCurrency(Request $request)
    {
        $currencyCode = $request->session()->get('currency', 'CDF');
        
        $currency = Currency::where('code', $currencyCode)
            ->where('is_active', true)
            ->first();
        
        if (!$currency) {
            $currency = Currency::where('is_default', true)->first();
        }
        
        return $currency;
    }
    
    /**
     * Changer de devise
     */
    public function changeCurrency(Request $request, $code)
    {
        $currency = Currency::where('code', $code)->where('is_active', true)->first();
        
        if ($currency) {
            $request->session()->put('currency', $code);
        }
        
        return redirect()->back();
    }
    
    /**
     * Ajouter un avis sur un produit
     */
    public function storeReview(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        
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
        
        $existingReview = ProductReview::where('product_id', $product->id)
            ->when(auth()->check(), function($q) {
                return $q->where('user_id', auth()->id());
            })
            ->when(!auth()->check() && $request->filled('customer_name'), function($q) use ($request) {
                return $q->where('customer_name', $request->customer_name);
            })
            ->exists();
        
        if ($existingReview) {
            return redirect()->back()
                ->with('error', 'Vous avez déjà laissé un avis sur ce produit.');
        }
        
        $review = new ProductReview();
        $review->product_id = $product->id;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->is_approved = false;
        
        if (auth()->check()) {
            $review->user_id = auth()->id();
            $review->customer_name = auth()->user()->name;
        } else {
            $review->customer_name = $request->customer_name;
        }
        
        $review->save();
        
        $this->updateProductRating($product);
        
        return redirect()->back()
            ->with('success', 'Merci pour votre avis ! Il sera publié après validation.');
    }
    
    /**
     * Mettre à jour la note moyenne du produit
     */
    private function updateProductRating($product)
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
     * Gérer l'historique des produits récents
     */
    private function getRecentProducts($currentProductId)
    {
        $recentIds = session()->get('recent_products', []);
        
        $recentIds = array_diff($recentIds, [$currentProductId]);
        array_unshift($recentIds, $currentProductId);
        $recentIds = array_slice($recentIds, 0, 4);
        session()->put('recent_products', $recentIds);
        
        if (empty($recentIds)) {
            return collect();
        }
        
        return Product::whereIn('id', $recentIds)
            ->where('is_active', true)
            ->orderByRaw('FIELD(id, ' . implode(',', $recentIds) . ')')
            ->get();
    }
    
    /**
     * Obtenir la distribution des notes pour un produit
     */
    private function getRatingDistribution($product)
    {
        $distribution = [];
        
        for ($i = 5; $i >= 1; $i--) {
            $count = $product->approvedReviews()
                ->where('rating', $i)
                ->count();
            
            $percentage = $product->reviews_count > 0 
                ? round(($count / $product->reviews_count) * 100) 
                : 0;
            
            $distribution[$i] = [
                'count' => $count,
                'percentage' => $percentage,
            ];
        }
        
        return $distribution;
    }
    
    /**
     * Filtrer les produits par prix (AJAX)
     */
    public function filterByPrice(Request $request)
    {
        $request->validate([
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);
        
        $perPage = $this->getPerPage($request);
        
        $products = Product::where('is_active', true)
            ->when($request->filled('category_id'), function($q) use ($request) {
                return $q->where('category_id', $request->category_id);
            })
            ->when($request->filled('min_price'), function($q) use ($request) {
                return $q->where('price', '>=', $request->min_price);
            })
            ->when($request->filled('max_price'), function($q) use ($request) {
                return $q->where('price', '<=', $request->max_price);
            })
            ->paginate($perPage);
        
        $currency = $this->getCurrency($request);
        
        if ($request->ajax()) {
            return view('client.components.product-grid', compact('products', 'currency'))->render();
        }
        
        return response()->json([
            'success' => true,
            'html' => view('client.components.product-grid', compact('products', 'currency'))->render(),
            'count' => $products->total(),
        ]);
    }
    
    /**
     * Trier les produits (AJAX)
     */
    public function sort(Request $request)
    {
        $request->validate([
            'sort' => 'required|in:price_asc,price_desc,name_asc,name_desc,newest,rating_desc',
            'category_id' => 'nullable|exists:categories,id',
        ]);
        
        $perPage = $this->getPerPage($request);
        
        $products = Product::where('is_active', true)
            ->when($request->filled('category_id'), function($q) use ($request) {
                return $q->where('category_id', $request->category_id);
            })
            ->when($request->sort == 'price_asc', function($q) {
                return $q->orderBy('price', 'asc');
            })
            ->when($request->sort == 'price_desc', function($q) {
                return $q->orderBy('price', 'desc');
            })
            ->when($request->sort == 'name_asc', function($q) {
                return $q->orderBy('name', 'asc');
            })
            ->when($request->sort == 'name_desc', function($q) {
                return $q->orderBy('name', 'desc');
            })
            ->when($request->sort == 'newest', function($q) {
                return $q->orderBy('created_at', 'desc');
            })
            ->when($request->sort == 'rating_desc', function($q) {
                return $q->orderBy('avg_rating', 'desc');
            })
            ->paginate($perPage);
        
        $currency = $this->getCurrency($request);
        
        return response()->json([
            'success' => true,
            'html' => view('client.components.product-grid', compact('products', 'currency'))->render(),
        ]);
    }
}