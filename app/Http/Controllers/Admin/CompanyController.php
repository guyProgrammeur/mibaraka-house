<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Afficher le formulaire des informations de l'entreprise
     */
    public function index()
    {
        $company = Company::instance();
        return view('admin.company.index', compact('company'));
    }
    
    /**
     * Mettre à jour les informations de l'entreprise
     */
    public function update(Request $request)
    {
        $company = Company::instance();
        
        $validated = $request->validate([
            // Informations générales
            'name' => 'required|string|max:255',
            'slogan' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            
            // Logos
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:512',
            'invoice_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Réseaux sociaux
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            
            // Livraison
            'delivery_fee_standard' => 'required|numeric|min:0',
            'delivery_fee_free_threshold' => 'required|numeric|min:0',
            
            // Horaires
            'opening_hours' => 'nullable|date_format:H:i',
            'closing_hours' => 'nullable|date_format:H:i',
            'working_days' => 'nullable|array',
            'working_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            
            // Légal
            'registration_number' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:100',
            
            // Personnalisation
            'primary_color' => 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color' => 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            
            // WhatsApp
            'whatsapp_greeting' => 'nullable|string|max:500',
            'whatsapp_notifications' => 'sometimes|boolean',
        ]);
        
        // Gestion des logos
        if ($request->hasFile('logo')) {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('company', 'public');
        }
        
        if ($request->hasFile('favicon')) {
            if ($company->favicon_path) {
                Storage::disk('public')->delete($company->favicon_path);
            }
            $validated['favicon_path'] = $request->file('favicon')->store('company', 'public');
        }
        
        if ($request->hasFile('invoice_logo')) {
            if ($company->invoice_logo_path) {
                Storage::disk('public')->delete($company->invoice_logo_path);
            }
            $validated['invoice_logo_path'] = $request->file('invoice_logo')->store('company', 'public');
        }
        
        $validated['whatsapp_notifications'] = $request->boolean('whatsapp_notifications', true);
        
        $company->update($validated);
        
        return redirect()
            ->route('admin.company.index')
            ->with('success', 'Informations de l\'entreprise mises à jour avec succès.');
    }
}