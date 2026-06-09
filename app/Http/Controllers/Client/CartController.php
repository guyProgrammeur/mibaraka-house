<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Currency;
use App\Models\Company;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Afficher la page du panier
     */
    public function index(Request $request)
    {
        $cart = $this->getCart();
        $cartItems = $this->getCartItems($cart);
        $currency = $this->getCurrency($request);
        $company = Company::instance();
        
        $subtotal = $this->calculateSubtotal($cart);
        $deliveryFee = $company->calculateDeliveryFee($subtotal);
        $total = $subtotal + $deliveryFee;
        $cartCount = $this->getCartCount();
        
        // Produits recommandés
        $recommendedProducts = Product::where('is_active', true)
            ->whereNotIn('id', array_keys($cart))
            ->inRandomOrder()
            ->limit(5)
            ->get();
        
        // Annonces
        $announcementsByPosition = [
            'top' => Announcement::active('top')->get(),
            'bottom' => Announcement::active('bottom')->get(),
        ];
        
        return view('client.cart.index', compact(
            'cartItems', 
            'currency', 
            'subtotal', 
            'deliveryFee', 
            'total', 
            'cartCount',
            'recommendedProducts',
            'announcementsByPosition'
        ));
    }
    
    /**
     * Ajouter un produit au panier (AJAX)
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $product = Product::findOrFail($request->product_id);
        
        if (!$product->isAvailable($request->quantity)) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuffisant pour ce produit.'
            ], 400);
        }
        
        $cart = $this->getCart();
        
        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] += $request->quantity;
            
            if (!$product->isAvailable($cart[$request->product_id]['quantity'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuffisant pour ce produit.'
                ], 400);
            }
        } else {
            $cart[$request->product_id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $request->quantity,
                'image' => $product->image_url,
                'slug' => $product->slug,
                'max_quantity' => $product->track_stock ? $product->stock_quantity : 99,
            ];
        }
        
        $this->saveCart($cart);
        $cartCount = $this->getCartCount();
        
        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté au panier',
            'cart_count' => $cartCount,
        ]);
    }
    
    /**
     * Mettre à jour la quantité d'un produit
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0|max:99',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $cart = $this->getCart();
        
        if (!isset($cart[$request->product_id])) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé dans le panier'
            ], 404);
        }
        
        $product = Product::find($request->product_id);
        
        if ($request->quantity <= 0) {
            unset($cart[$request->product_id]);
            $message = 'Produit retiré du panier';
        } else {
            if ($product && !$product->isAvailable($request->quantity)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuffisant pour ce produit.'
                ], 400);
            }
            
            $cart[$request->product_id]['quantity'] = $request->quantity;
            $message = 'Quantité mise à jour';
        }
        
        $this->saveCart($cart);
        
        $subtotal = $this->calculateSubtotal($cart);
        $company = Company::instance();
        $deliveryFee = $company->calculateDeliveryFee($subtotal);
        $total = $subtotal + $deliveryFee;
        $cartCount = $this->getCartCount();
        $currency = $this->getCurrency($request);
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'cart_count' => $cartCount,
            'subtotal' => $this->formatPrice($subtotal, $currency),
            'delivery_fee' => $deliveryFee == 0 ? 'Offerte' : $this->formatPrice($deliveryFee, $currency),
            'total' => $this->formatPrice($total, $currency),
        ]);
    }
    
    /**
     * Supprimer un produit du panier
     */
    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $cart = $this->getCart();
        
        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            $this->saveCart($cart);
        }
        
        $subtotal = $this->calculateSubtotal($cart);
        $company = Company::instance();
        $deliveryFee = $company->calculateDeliveryFee($subtotal);
        $total = $subtotal + $deliveryFee;
        $currency = $this->getCurrency($request);
        
        return response()->json([
            'success' => true,
            'message' => 'Produit retiré du panier',
            'cart_count' => $this->getCartCount(),
            'subtotal' => $this->formatPrice($subtotal, $currency),
            'delivery_fee' => $deliveryFee == 0 ? 'Offerte' : $this->formatPrice($deliveryFee, $currency),
            'total' => $this->formatPrice($total, $currency),
        ]);
    }
    
    /**
     * Vider le panier
     */
    public function clear(Request $request)
    {
        $this->clearCart();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Panier vidé avec succès',
                'cart_count' => 0,
            ]);
        }
        
        return redirect()->route('client.cart.index')->with('success', 'Panier vidé avec succès');
    }
    
    /**
     * Récupérer le contenu du panier (AJAX)
     */
    public function content(Request $request)
    {
        $cart = $this->getCart();
        $currency = $this->getCurrency($request);
        
        $items = [];
        foreach ($cart as $item) {
            $items[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'image' => $item['image'],
                'slug' => $item['slug'],
                'total' => $item['price'] * $item['quantity'],
                'formatted_price' => $this->formatPrice($item['price'], $currency),
                'formatted_total' => $this->formatPrice($item['price'] * $item['quantity'], $currency),
            ];
        }
        
        $subtotal = $this->calculateSubtotal($cart);
        $company = Company::instance();
        $deliveryFee = $company->calculateDeliveryFee($subtotal);
        
        return response()->json([
            'success' => true,
            'items' => $items,
            'count' => $this->getCartCount(),
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $subtotal + $deliveryFee,
            'formatted_subtotal' => $this->formatPrice($subtotal, $currency),
            'formatted_delivery' => $deliveryFee > 0 ? $this->formatPrice($deliveryFee, $currency) : 'Offerte',
            'formatted_total' => $this->formatPrice($subtotal + $deliveryFee, $currency),
        ]);
    }
    
    /**
     * Mini panier pour le header (AJAX)
     */
    public function miniCart(Request $request)
    {
        $cart = $this->getCart();
        $currency = $this->getCurrency($request);
        
        $items = [];
        foreach (array_slice($cart, 0, 3) as $item) {
            $items[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'image' => $item['image'],
                'total' => $item['price'] * $item['quantity'],
                'formatted_price' => $this->formatPrice($item['price'], $currency),
            ];
        }
        
        $subtotal = $this->calculateSubtotal($cart);
        
        return response()->json([
            'success' => true,
            'items' => $items,
            'count' => $this->getCartCount(),
            'subtotal' => $subtotal,
            'has_more' => count($cart) > 3,
            'formatted_subtotal' => $this->formatPrice($subtotal, $currency),
        ]);
    }
    
    /**
     * Vérifier si les produits sont toujours disponibles
     */
    public function validateCart()
    {
        $cart = $this->getCart();
        $invalidItems = [];
        
        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            
            if (!$product || !$product->is_active) {
                $invalidItems[] = $item['name'];
                unset($cart[$id]);
            } elseif ($product->track_stock && $product->stock_quantity < $item['quantity']) {
                $invalidItems[] = $item['name'] . ' (stock: ' . $product->stock_quantity . ')';
                $cart[$id]['quantity'] = $product->stock_quantity;
                if ($cart[$id]['quantity'] <= 0) {
                    unset($cart[$id]);
                }
            }
        }
        
        $this->saveCart($cart);
        
        if (!empty($invalidItems)) {
            return redirect()->route('client.cart.index')
                ->with('warning', 'Certains produits ont été modifiés ou supprimés: ' . implode(', ', $invalidItems));
        }
        
        return redirect()->route('client.checkout.index');
    }
    
    // ========== METHODES PRIVÉES ==========
    
    /**
     * Récupérer le panier depuis la session
     */
    private function getCart(): array
    {
        return session()->get('cart', []);
    }
    
    /**
     * Sauvegarder le panier en session
     */
    private function saveCart(array $cart): void
    {
        session()->put('cart', $cart);
    }
    
    /**
     * Vider le panier
     */
    private function clearCart(): void
    {
        session()->forget('cart');
    }
    
    /**
     * Compter le nombre d'articles dans le panier
     */
    private function getCartCount(): int
    {
        $cart = $this->getCart();
        return array_sum(array_column($cart, 'quantity'));
    }
    
    /**
     * Calculer le sous-total du panier
     */
    private function calculateSubtotal(array $cart): float
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
    
    /**
     * Récupérer les détails des produits du panier
     */
    private function getCartItems(array $cart): \Illuminate\Support\Collection
    {
        $productIds = array_keys($cart);
        if (empty($productIds)) {
            return collect();
        }
        
        $products = Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');
        
        $items = [];
        foreach ($cart as $id => $data) {
            if (isset($products[$id])) {
                $product = $products[$id];
                $product->quantity = $data['quantity'];
                $items[] = $product;
            }
        }
        
        return collect($items);
    }
    
    /**
     * Récupérer la devise sélectionnée
     */
    private function getCurrency(Request $request): Currency
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
     * Formater un prix selon la devise
     */
    private function formatPrice(float $price, Currency $currency): string
    {
        if ($currency->code === 'CDF') {
            return $currency->symbol . ' ' . number_format($price * $currency->rate, 0, ',', ' ');
        }
        return $currency->symbol . ' ' . number_format($price, 2, ',', ' ');
    }
    public function getCartContent(Request $request)
{
    $cart = $request->session()->get('cart', []);
    $cartItems = $this->getCartItems($cart);
    $currency = $this->getCurrency($request);
    
    $items = [];
    foreach ($cartItems as $item) {
        $items[] = [
            'id' => $item->id,
            'name' => $item->name,
            'price' => $item->price,
            'quantity' => $item->quantity,
            'image' => $item->image_url,
            'price_formatted' => $this->formatPrice($item->price, $currency),
        ];
    }
    
    return response()->json([
        'items' => $items,
        'count' => array_sum(array_column($cart, 'quantity')),
        'total' => $this->calculateSubtotal($cart),
        'total_formatted' => $this->formatPrice($this->calculateSubtotal($cart), $currency),
    ]);
}
}