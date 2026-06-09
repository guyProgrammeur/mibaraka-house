<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
    /**
     * Affiche la liste des devises
     */
    public function index(Request $request)
    {
        $query = Currency::query();

        // Filtre par recherche
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%')
                  ->orWhere('symbol', 'like', '%' . $request->search . '%');
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $currencies = $query->orderBy('is_default', 'desc')
            ->orderBy('code')
            ->paginate(20)
            ->withQueryString();

        $defaultCurrency = Currency::where('is_default', true)->first();
        $totalCurrencies = Currency::count();
        $activeCurrencies = Currency::where('is_active', true)->count();

        return view('admin.currencies.index', compact(
            'currencies',
            'defaultCurrency',
            'totalCurrencies',
            'activeCurrencies'
        ));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('admin.currencies.create');
    }

    /**
     * Enregistre une nouvelle devise
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code|alpha|uppercase',
            'symbol' => 'required|string|max:5',
            'name' => 'nullable|string|max:100',
            'rate' => 'required|numeric|min:0.0001',
            'is_default' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active', true);

        // Gestion de la devise par défaut
        if ($request->boolean('is_default')) {
            Currency::where('is_default', true)->update(['is_default' => false]);
            $validated['is_default'] = true;
        } else {
            // Si c'est la première devise, elle devient par défaut
            if (Currency::count() === 0) {
                $validated['is_default'] = true;
            }
        }

        $currency = Currency::create($validated);

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', "Devise {$currency->code} créée avec succès.");
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    /**
     * Met à jour une devise
     */
    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:3',
                'alpha',
                'uppercase',
                Rule::unique('currencies')->ignore($currency->id),
            ],
            'symbol' => 'required|string|max:5',
            'name' => 'nullable|string|max:100',
            'rate' => 'required|numeric|min:0.0001',
            'is_default' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active', $currency->is_active);

        // Gestion de la devise par défaut
        if ($request->boolean('is_default') && !$currency->is_default) {
            Currency::where('is_default', true)->update(['is_default' => false]);
            $validated['is_default'] = true;
        } elseif (!$request->boolean('is_default') && $currency->is_default) {
            $validated['is_default'] = false;

            // Vérifier qu'il y a au moins une devise par défaut
            if (Currency::where('is_default', true)->where('id', '!=', $currency->id)->count() === 0) {
                return back()->withErrors([
                    'is_default' => 'Il doit y avoir au moins une devise par défaut.'
                ]);
            }
        } else {
            $validated['is_default'] = $currency->is_default;
        }

        $currency->update($validated);

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', "Devise {$currency->code} mise à jour avec succès.");
    }

    /**
     * Supprime une devise
     */
    public function destroy(Currency $currency)
    {
        // Vérifier si c'est la devise par défaut
        if ($currency->is_default) {
            return back()->withErrors([
                'error' => 'Impossible de supprimer la devise par défaut.'
            ]);
        }

        // Vérifier si des commandes utilisent cette devise
        $orderCount = $currency->orders()->count();
        if ($orderCount > 0) {
            return back()->withErrors([
                'error' => "Impossible de supprimer cette devise. {$orderCount} commande(s) y sont associées."
            ]);
        }

        $currencyCode = $currency->code;
        $currency->delete();

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', "Devise {$currencyCode} supprimée avec succès.");
    }

    /**
     * Définit une devise comme devise par défaut
     */
    public function setDefault(Currency $currency)
    {
        if (!$currency->is_active) {
            return back()->withErrors([
                'error' => 'Impossible de définir une devise inactive comme devise par défaut.'
            ]);
        }

        Currency::where('is_default', true)->update(['is_default' => false]);
        $currency->update(['is_default' => true]);

        return redirect()
            ->back()
            ->with('success', "{$currency->code} définie comme devise par défaut.");
    }

    /**
     * Active/Désactive une devise
     */
    public function toggleActive(Currency $currency)
    {
        if ($currency->is_default) {
            return back()->withErrors([
                'error' => 'Impossible de désactiver la devise par défaut.'
            ]);
        }

        $currency->update(['is_active' => !$currency->is_active]);

        $status = $currency->is_active ? 'activée' : 'désactivée';

        return redirect()
            ->back()
            ->with('success', "Devise {$currency->code} {$status} avec succès.");
    }

    /**
     * Met à jour le taux de change d'une devise
     */
    public function updateRate(Request $request, Currency $currency)
    {
        $request->validate([
            'rate' => 'required|numeric|min:0.0001',
        ]);

        $oldRate = $currency->rate;
        $currency->update(['rate' => $request->rate]);

        return redirect()
            ->back()
            ->with('success', "Taux de {$currency->code} mis à jour : {$oldRate} → {$currency->rate}");
    }

    /**
     * Met à jour plusieurs taux de change en une fois
     */
    public function bulkUpdateRates(Request $request)
    {
        $request->validate([
            'currencies' => 'required|array',
            'currencies.*.id' => 'required|exists:currencies,id',
            'currencies.*.rate' => 'required|numeric|min:0.0001',
        ]);

        $updated = 0;
        foreach ($request->currencies as $currencyData) {
            $currency = Currency::find($currencyData['id']);
            
            // Ne pas modifier le taux de l'USD si c'est la référence
            if ($currency->code !== 'USD') {
                $currency->update(['rate' => $currencyData['rate']]);
                $updated++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$updated} devise(s) mise(s) à jour."
        ]);
    }

    /**
     * Exporte les devises en CSV
     */
    public function export()
    {
        $currencies = Currency::orderBy('code')->get();

        $filename = 'devises_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');

        // En-têtes CSV
        fputcsv($handle, [
            'Code', 'Symbole', 'Nom', 'Taux (vs USD)', 'Par défaut', 'Actif', 'Créé le'
        ]);

        foreach ($currencies as $currency) {
            fputcsv($handle, [
                $currency->code,
                $currency->symbol,
                $currency->name,
                $currency->rate,
                $currency->is_default ? 'Oui' : 'Non',
                $currency->is_active ? 'Oui' : 'Non',
                $currency->created_at->format('d/m/Y H:i'),
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
     * Affiche un aperçu des conversions de devises
     */
    public function preview()
    {
        $currencies = Currency::where('is_active', true)->orderBy('code')->get();
        $defaultCurrency = Currency::where('is_default', true)->first();
        
        // Montants de test pour l'aperçu
        $testAmounts = [1, 5, 10, 25, 50, 100];

        return view('admin.currencies.preview', compact('currencies', 'defaultCurrency', 'testAmounts'));
    }

    /**
     * API: Récupère le taux de change pour une devise (AJAX)
     */
    public function getRate(Request $request, $code)
    {
        $request->validate([
            'amount' => 'nullable|numeric',
        ]);

        $currency = Currency::where('code', strtoupper($code))
            ->where('is_active', true)
            ->firstOrFail();

        $result = [
            'code' => $currency->code,
            'symbol' => $currency->symbol,
            'rate' => $currency->rate,
            'formatted_rate' => $currency->formatted_rate,
        ];

        if ($request->has('amount')) {
            $amountInUsd = $request->amount;
            $converted = $currency->convertFromUsd($amountInUsd);
            $result['converted_amount'] = $converted;
            $result['formatted_converted'] = $currency->formatAmount($converted);
        }

        return response()->json($result);
    }

    /**
     * API: Récupère toutes les devises actives (AJAX)
     */
    public function getActiveCurrencies()
    {
        $currencies = Currency::where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('code')
            ->get(['id', 'code', 'symbol', 'name', 'rate', 'is_default']);

        return response()->json($currencies);
    }

    /**
     * Vérifie si le taux de change est valide (cron job)
     */
    public function validateRates()
    {
        $currencies = Currency::where('is_active', true)->get();
        $invalidRates = [];

        foreach ($currencies as $currency) {
            if ($currency->rate <= 0) {
                $invalidRates[] = $currency->code;
            }
        }

        if (count($invalidRates) > 0) {
            // Log ou notification
            \Log::warning('Taux de change invalides pour: ' . implode(', ', $invalidRates));
        }

        return response()->json([
            'valid' => count($invalidRates) === 0,
            'invalid_currencies' => $invalidRates
        ]);
    }

    /**
     * Synchronise les taux de change depuis une API externe (optionnel)
     */
    public function syncRates()
    {
        // Cette méthode peut être utilisée pour synchroniser les taux depuis une API externe
        // Exemple avec une API gratuite comme https://exchangerate.host
        
        try {
            $response = file_get_contents('https://api.exchangerate.host/latest?base=USD');
            $data = json_decode($response, true);
            
            if (isset($data['rates'])) {
                $updated = 0;
                foreach ($data['rates'] as $code => $rate) {
                    $currency = Currency::where('code', $code)->first();
                    if ($currency && $currency->code !== 'USD') {
                        $currency->update(['rate' => $rate]);
                        $updated++;
                    }
                }
                
                return redirect()
                    ->back()
                    ->with('success', "Taux synchronisés avec succès. {$updated} devise(s) mises à jour.");
            }
            
            return back()->withErrors(['error' => 'Impossible de synchroniser les taux.']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur de synchronisation: ' . $e->getMessage()]);
        }
    }
}