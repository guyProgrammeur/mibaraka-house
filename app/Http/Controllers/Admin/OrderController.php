<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes avec filtres
     */
    public function index(Request $request)
    {
        $query = Order::with('customer', 'items');

        // Filtre par numéro de commande ou téléphone client
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $request->search . '%');
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par statut de paiement
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filtre par méthode de paiement
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filtre par période
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filtre par montant total
        if ($request->filled('min_total')) {
            $query->where('total_amount', '>=', (float) $request->min_total);
        }

        if ($request->filled('max_total')) {
            $query->where('total_amount', '<=', (float) $request->max_total);
        }

        // Tri
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['order_number', 'total_amount', 'created_at', 'delivered_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        $orders = $query->paginate(20)->withQueryString();

        // Statistiques pour les filtres
        $statusCounts = [
            'pending' => Order::where('status', Order::STATUS_PENDING)->count(),
            'confirmed' => Order::where('status', Order::STATUS_CONFIRMED)->count(),
            'preparing' => Order::where('status', Order::STATUS_PREPARING)->count(),
            'ready' => Order::where('status', Order::STATUS_READY)->count(),
            'delivered' => Order::where('status', Order::STATUS_DELIVERED)->count(),
            'cancelled' => Order::where('status', Order::STATUS_CANCELLED)->count(),
        ];

        $totalRevenue = Order::where('status', Order::STATUS_DELIVERED)->sum('total_amount');
        $todayRevenue = Order::where('status', Order::STATUS_DELIVERED)
            ->whereDate('created_at', today())
            ->sum('total_amount');

        return view('admin.orders.index', compact(
            'orders', 
            'statusCounts', 
            'totalRevenue', 
            'todayRevenue'
        ));
    }

    /**
     * Affiche les détails d'une commande
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'customer', 'currency']);
        
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'image_path', 'stock_quantity', 'track_stock']);
        
        $currencies = Currency::where('is_active', true)->get();
        
        $statusTransitions = [
            'current' => $order->status,
            'allowed' => $this->getAllowedTransitions($order->status),
        ];
        
        $paymentMethods = [
            'cash' => 'Espèces',
            'mobile_money' => 'Mobile Money',
            'bank_transfer' => 'Virement bancaire',
        ];

        return view('admin.orders.show', compact(
            'order', 
            'products', 
            'currencies', 
            'statusTransitions',
            'paymentMethods'
        ));
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled',
            'note' => 'nullable|string|max:500',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        $allowedTransitions = $this->getAllowedTransitions($oldStatus);

        if (!in_array($newStatus, $allowedTransitions)) {
            return back()->withErrors([
                'status' => "Transition non autorisée de {$oldStatus} vers {$newStatus}."
            ]);
        }

        $success = $order->changeStatus($newStatus, $request->note);

        if (!$success) {
            return back()->withErrors(['status' => 'Impossible de changer le statut.']);
        }

        $message = "Statut de la commande #{$order->order_number} mis à jour : {$order->status_label}";

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', $message);
    }

    /**
     * Met à jour le statut de paiement
     */
    public function updatePayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed',
            'payment_method' => 'nullable|in:cash,mobile_money,bank_transfer',
        ]);

        $order->payment_status = $request->payment_status;

        if ($request->payment_method) {
            $order->payment_method = $request->payment_method;
        }

        if ($request->payment_status === Order::PAYMENT_PAID) {
            $order->markAsPaid($request->payment_method);
        } else {
            $order->save();
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Statut de paiement mis à jour.');
    }

    /**
     * Ajoute un produit à la commande
     */
    public function addItem(Request $request, Order $order)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Vérifier si le produit est actif
        if (!$product->is_active) {
            return back()->withErrors(['product_id' => 'Ce produit n\'est pas actif.']);
        }

        // Vérifier le stock si suivi
        if ($product->track_stock && $product->stock_quantity < $request->quantity) {
            return back()->withErrors(['quantity' => "Stock insuffisant. Disponible: {$product->stock_quantity}"]);
        }

        // Vérifier si le produit existe déjà dans la commande
        $existingItem = $order->items()->where('product_id', $product->id)->first();

        DB::transaction(function () use ($order, $product, $request, $existingItem) {
            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $request->quantity;
                $existingItem->updateQuantity($newQuantity);
            } else {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->image_path,
                    'unit_price' => $product->price,
                    'quantity' => $request->quantity,
                    'subtotal' => $product->price * $request->quantity,
                ]);
            }

            $order->recalculateTotal();
        });

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Produit ajouté à la commande.');
    }

    /**
     * Met à jour la quantité d'un article
     */
    public function updateItem(Request $request, Order $order, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = $order->items()->findOrFail($itemId);

        // Vérifier le stock si le produit est suivi
        if ($item->product && $item->product->track_stock) {
            $stockAvailable = $item->product->stock_quantity + $item->quantity;
            if ($stockAvailable < $request->quantity) {
                return back()->withErrors([
                    'quantity' => "Stock insuffisant. Maximum disponible: {$stockAvailable}"
                ]);
            }
        }

        $item->updateQuantity($request->quantity);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Quantité mise à jour.');
    }

    /**
     * Supprime un article de la commande
     */
    public function removeItem(Order $order, $itemId)
    {
        $item = $order->items()->findOrFail($itemId);
        
        $item->delete();
        $order->recalculateTotal();

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Produit retiré de la commande.');
    }

    /**
     * Envoie la commande par WhatsApp au commerçant
     */
    public function sendWhatsappMerchant(Order $order)
    {
        $link = $order->whatsapp_link_for_merchant;
        $order->markWhatsappAsSent();

        return redirect()->away($link);
    }

    /**
     * Envoie la confirmation au client par WhatsApp
     */
    public function sendWhatsappCustomer(Order $order)
    {
        $link = $order->whatsapp_link_for_customer;
        
        return redirect()->away($link);
    }

    /**
     * Génère la facture PDF
     */
    public function invoice(Order $order)
    {
        $order->load(['items.product', 'customer', 'currency']);
        
        $pdf = View::make('admin.orders.invoice-pdf', compact('order'))->render();
        
        // Retourner la vue pour impression (ou utiliser un package PDF)
        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Annule une commande
     */
    public function cancel(Order $order, Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        if (!$order->can_be_cancelled) {
            return back()->withErrors(['error' => 'Cette commande ne peut pas être annulée.']);
        }

        $note = $request->reason ? "Annulation: {$request->reason}" : "Annulation par l'administrateur";
        $order->changeStatus(Order::STATUS_CANCELLED, $note);

        // Restaurer les stocks si nécessaire
        foreach ($order->items as $item) {
            if ($item->product && $item->product->track_stock) {
                $item->product->increaseStock($item->quantity);
            }
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Commande annulée avec succès.');
    }

    /**
     * Duplique une commande (pour réutilisation)
     */
    public function duplicate(Order $order)
    {
        $newOrder = $order->replicate();
        $newOrder->order_number = $order->generateOrderNumber();
        $newOrder->status = Order::STATUS_PENDING;
        $newOrder->payment_status = Order::PAYMENT_PENDING;
        $newOrder->whatsapp_sent = false;
        $newOrder->whatsapp_sent_at = null;
        $newOrder->delivered_at = null;
        $newOrder->admin_notes = null;
        $newOrder->created_at = now();
        $newOrder->save();

        // Dupliquer les articles
        foreach ($order->items as $item) {
            $newItem = $item->replicate();
            $newItem->order_id = $newOrder->id;
            $newItem->save();
        }

        $newOrder->recalculateTotal();

        return redirect()
            ->route('admin.orders.show', $newOrder)
            ->with('success', 'Commande dupliquée avec succès.');
    }

    /**
     * Supprime une commande (soft delete ou hard delete)
     */
    public function destroy(Order $order)
    {
        // Seules les commandes livrées ou annulées peuvent être supprimées
        if (!in_array($order->status, [Order::STATUS_DELIVERED, Order::STATUS_CANCELLED])) {
            return back()->withErrors([
                'error' => 'Seules les commandes livrées ou annulées peuvent être supprimées.'
            ]);
        }

        DB::transaction(function () use ($order) {
            $order->items()->delete();
            $order->delete();
        });

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Commande supprimée avec succès.');
    }

    /**
     * Exporte les commandes en CSV
     */
    public function export(Request $request)
    {
        $query = Order::with('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $filename = 'commandes_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');

        // En-têtes CSV
        fputcsv($handle, [
            'N° commande', 'Date', 'Client', 'Téléphone', 'Adresse',
            'Total produits', 'Sous-total', 'Livraison', 'Total',
            'Devise', 'Statut', 'Paiement', 'Notes'
        ]);

        foreach ($orders as $order) {
            fputcsv($handle, [
                $order->order_number,
                $order->created_at->format('d/m/Y H:i'),
                $order->customer_name,
                $order->customer_phone,
                $order->delivery_address,
                $order->items->sum('quantity'),
                $order->subtotal,
                $order->delivery_fee,
                $order->total_amount,
                $order->currency_code,
                $order->status_label,
                $order->payment_status === 'paid' ? 'Payé' : 'En attente',
                $order->admin_notes,
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
     * Statistiques des commandes (AJAX)
     */
    public function stats(Request $request)
    {
        $period = $request->get('period', 'month');
        
        $query = Order::where('status', Order::STATUS_DELIVERED);
        
        switch ($period) {
            case 'week':
                $query->where('created_at', '>=', now()->subDays(7));
                $groupBy = 'DATE(created_at)';
                break;
            case 'month':
                $query->where('created_at', '>=', now()->subDays(30));
                $groupBy = 'DATE(created_at)';
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                $groupBy = 'DATE_FORMAT(created_at, "%Y-%m")';
                break;
            default:
                $groupBy = 'DATE(created_at)';
        }
        
        $stats = $query->select(
            DB::raw("{$groupBy} as date"),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as revenue')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json($stats);
    }

    /**
     * Met à jour les frais de livraison
     */
    public function updateDeliveryFee(Request $request, Order $order)
    {
        $request->validate([
            'delivery_fee' => 'required|numeric|min:0',
        ]);

        $order->delivery_fee = $request->delivery_fee;
        $order->recalculateTotal();

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Frais de livraison mis à jour.');
    }

    /**
     * Met à jour les notes administratives
     */
    public function updateNotes(Request $request, Order $order)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $order->admin_notes = $request->admin_notes;
        $order->save();

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Notes mises à jour.');
    }

    /**
     * Crée une nouvelle commande manuelle
     */
    public function create()
    {
        $customers = Customer::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'email', 'default_address', 'city', 'neighborhood']);
        
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'image_path', 'stock_quantity', 'track_stock']);
        
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.orders.create', compact('customers', 'products', 'currencies'));
    }

    /**
     * Enregistre une nouvelle commande manuelle
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required_if:customer_id,null|string|max:255',
            'customer_phone' => 'required_if:customer_id,null|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'delivery_address' => 'required|string|max:500',
            'delivery_city' => 'nullable|string|max:100',
            'delivery_neighborhood' => 'nullable|string|max:100',
            'delivery_fee' => 'numeric|min:0',
            'currency_code' => 'required|exists:currencies,code',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $currency = Currency::where('code', $request->currency_code)->first();

        DB::transaction(function () use ($request, $currency) {
            // Récupérer ou créer le client
            if ($request->filled('customer_id')) {
                $customer = Customer::find($request->customer_id);
                $customerName = $customer->name;
                $customerPhone = $customer->phone;
                $customerEmail = $customer->email;
            } else {
                $customer = null;
                $customerName = $request->customer_name;
                $customerPhone = $request->customer_phone;
                $customerEmail = $request->customer_email;
            }

            // Créer la commande
            $order = Order::create([
                'order_number' => (new Order())->generateOrderNumber(),
                'customer_id' => $customer?->id,
                'status' => Order::STATUS_PENDING,
                'delivery_fee' => $request->delivery_fee ?? 0,
                'currency_code' => $request->currency_code,
                'currency_id' => $currency->id,
                'exchange_rate' => $currency->rate,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'customer_email' => $customerEmail,
                'delivery_address' => $request->delivery_address,
                'delivery_city' => $request->delivery_city,
                'delivery_neighborhood' => $request->delivery_neighborhood,
                'payment_status' => Order::PAYMENT_PENDING,
                'whatsapp_sent' => false,
            ]);

            // Ajouter les articles
            foreach ($request->items as $itemData) {
                $product = Product::find($itemData['product_id']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->image_path,
                    'unit_price' => $product->price,
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $product->price * $itemData['quantity'],
                ]);
            }

            $order->recalculateTotal();
        });

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Commande créée avec succès.');
    }

    /**
     * Retourne les transitions autorisées pour un statut donné
     */
    private function getAllowedTransitions(string $status): array
    {
        $transitions = [
            Order::STATUS_PENDING => [Order::STATUS_CONFIRMED, Order::STATUS_CANCELLED],
            Order::STATUS_CONFIRMED => [Order::STATUS_PREPARING, Order::STATUS_CANCELLED],
            Order::STATUS_PREPARING => [Order::STATUS_READY, Order::STATUS_CANCELLED],
            Order::STATUS_READY => [Order::STATUS_DELIVERED, Order::STATUS_CANCELLED],
            Order::STATUS_DELIVERED => [],
            Order::STATUS_CANCELLED => [],
        ];

        return $transitions[$status] ?? [];
    }
}