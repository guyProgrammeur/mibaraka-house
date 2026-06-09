<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Affiche la liste des clients avec filtres
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Filtre de recherche
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
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
                case 'frequent':
                    $query->where('total_orders', '>=', 5);
                    break;
                case 'inactive_6months':
                    $query->where('last_order_at', '<', now()->subMonths(6))
                          ->orWhereNull('last_order_at');
                    break;
            }
        }

        // Filtre par période d'inscription
        if ($request->filled('registered_from')) {
            $query->whereDate('created_at', '>=', $request->registered_from);
        }

        if ($request->filled('registered_to')) {
            $query->whereDate('created_at', '<=', $request->registered_to);
        }

        // Filtre par montant dépensé
        if ($request->filled('min_spent')) {
            $query->where('total_spent', '>=', (float) $request->min_spent);
        }

        if ($request->filled('max_spent')) {
            $query->where('total_spent', '<=', (float) $request->max_spent);
        }

        // Tri
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['name', 'total_orders', 'total_spent', 'created_at', 'last_order_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        $customers = $query->paginate(20)->withQueryString();

        // Statistiques pour les filtres
        $stats = [
            'total' => Customer::count(),
            'active' => Customer::where('is_active', true)->count(),
            'frequent' => Customer::where('total_orders', '>=', 5)->count(),
            'inactive' => Customer::where('is_active', false)->count(),
            'new_this_month' => Customer::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    /**
     * Affiche les détails d'un client
     */
    public function show(Customer $customer)
    {
        $customer->load(['orders' => function($query) {
            $query->latest()->limit(10);
        }]);

        $ordersCount = $customer->orders()->count();
        $totalSpent = $customer->orders()->where('status', 'delivered')->sum('total_amount');
        $lastOrder = $customer->orders()->latest()->first();
        $favoriteProducts = $customer->getFavoriteProducts(5);
        
        // Statistiques des commandes par statut
        $orderStats = [
            'pending' => $customer->orders()->where('status', 'pending')->count(),
            'confirmed' => $customer->orders()->where('status', 'confirmed')->count(),
            'preparing' => $customer->orders()->where('status', 'preparing')->count(),
            'ready' => $customer->orders()->where('status', 'ready')->count(),
            'delivered' => $customer->orders()->where('status', 'delivered')->count(),
            'cancelled' => $customer->orders()->where('status', 'cancelled')->count(),
        ];

        // Commandes par mois (pour graphique)
        $monthlyOrders = $customer->orders()
            ->where('status', 'delivered')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        $currencies = Currency::where('is_active', true)->get();

        return view('admin.customers.show', compact(
            'customer',
            'ordersCount',
            'totalSpent',
            'lastOrder',
            'favoriteProducts',
            'orderStats',
            'monthlyOrders',
            'currencies'
        ));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Customer $customer)
    {
        $currencies = Currency::where('is_active', true)->get();
        
        return view('admin.customers.edit', compact('customer', 'currencies'));
    }

    /**
     * Met à jour un client
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('customers')->ignore($customer->id),
            ],
            'email' => 'nullable|email|max:255',
            'default_address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'preferred_currency' => 'required|exists:currencies,code',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $customer->update($validated);

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Supprime un client
     */
    public function destroy(Customer $customer)
    {
        $hasOrders = $customer->orders()->exists();

        if ($hasOrders) {
            return back()->withErrors([
                'error' => 'Impossible de supprimer ce client car il a déjà passé des commandes.'
            ]);
        }

        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Client supprimé avec succès.');
    }

    /**
     * Active/Désactive un client
     */
    public function toggleActive(Customer $customer)
    {
        $customer->update(['is_active' => !$customer->is_active]);

        $status = $customer->is_active ? 'activé' : 'désactivé';

        return redirect()
            ->back()
            ->with('success', "Client {$status} avec succès.");
    }

    /**
     * Exporte les clients en CSV
     */
    public function export(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'frequent') {
                $query->where('total_orders', '>=', 5);
            }
        }

        $customers = $query->get();

        $filename = 'clients_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');

        // En-têtes CSV
        fputcsv($handle, [
            'ID', 'Nom', 'Téléphone', 'Email', 'Adresse', 'Ville', 'Quartier',
            'Total commandes', 'Total dépensé (USD)', 'Dernière commande', 
            'Inscrit le', 'Statut', 'Notes'
        ]);

        foreach ($customers as $customer) {
            fputcsv($handle, [
                $customer->id,
                $customer->name,
                $customer->formatted_phone,
                $customer->email,
                $customer->default_address,
                $customer->city,
                $customer->neighborhood,
                $customer->total_orders,
                $customer->total_spent,
                $customer->last_order_at?->format('d/m/Y'),
                $customer->created_at->format('d/m/Y'),
                $customer->is_active ? 'Actif' : 'Inactif',
                $customer->notes,
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
     * Envoie un message WhatsApp au client
     */
    public function sendWhatsapp(Customer $customer, Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $link = $customer->sendWhatsappMessage($request->message);

        // Marquer le client comme actif
        $customer->markAsActive();

        return redirect()->away($link);
    }

    /**
     * Formulaire pour envoyer un message groupé
     */
    public function broadcastForm()
    {
        $customersCount = Customer::where('is_active', true)->count();
        
        return view('admin.customers.broadcast', compact('customersCount'));
    }

    /**
     * Envoie un message groupé (ouvre WhatsApp Web avec plusieurs contacts)
     * Note: WhatsApp ne permet pas de broadcast natif, on ouvre des onglets individuels
     */
    public function broadcast(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        if ($request->filled('customer_ids')) {
            $customers = Customer::whereIn('id', $request->customer_ids)->get();
        } else {
            $customers = Customer::where('is_active', true)->get();
        }

        if ($customers->isEmpty()) {
            return back()->withErrors(['error' => 'Aucun client sélectionné.']);
        }

        // Stocker les liens en session pour les ouvrir un par un
        $links = [];
        foreach ($customers as $customer) {
            $links[] = $customer->sendWhatsappMessage($request->message);
        }

        session()->flash('whatsapp_links', $links);
        session()->flash('whatsapp_count', count($links));

        return redirect()
            ->route('admin.customers.index')
            ->with('success', "Message préparé pour " . count($links) . " client(s). Cliquez sur les liens WhatsApp pour envoyer.");
    }

    /**
     * Récupère les clients pour l'auto-complétion (AJAX)
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $customers = Customer::where('is_active', true)
            ->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('phone', 'like', '%' . $request->q . '%');
            })
            ->limit(10)
            ->get(['id', 'name', 'phone', 'email']);

        return response()->json($customers);
    }

    /**
     * Met à jour les statistiques de tous les clients (maintenance)
     */
    public function refreshStats()
    {
        $customers = Customer::all();
        $updated = 0;

        foreach ($customers as $customer) {
            $customer->updateStats();
            $updated++;
        }

        return redirect()
            ->route('admin.customers.index')
            ->with('success', "Statistiques mises à jour pour {$updated} client(s).");
    }

    /**
     * Affiche les clients inactifs (plus de 6 mois sans commande)
     */
    public function inactive()
    {
        $customers = Customer::where('is_active', true)
            ->where(function($q) {
                $q->where('last_order_at', '<', now()->subMonths(6))
                  ->orWhereNull('last_order_at');
            })
            ->orderBy('last_order_at', 'asc')
            ->paginate(20);

        return view('admin.customers.inactive', compact('customers'));
    }

    /**
     * Affiche le top des clients (fidèles)
     */
    public function topCustomers()
    {
        $customers = Customer::where('is_active', true)
            ->where('total_orders', '>', 0)
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        return view('admin.customers.top', compact('customers'));
    }
}