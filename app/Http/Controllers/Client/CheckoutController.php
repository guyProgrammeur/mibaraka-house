<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Company;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Afficher la page de checkout
     */
    public function index(Request $request)
    {
        $currency = $this->getCurrency($request);
        $company = Company::instance();
        
        $announcementsByPosition = [
            'top' => Announcement::active('top')->get(),
            'bottom' => Announcement::active('bottom')->get(),
        ];
        
        return view('client.checkout.index', compact(
            'currency', 'announcementsByPosition', 'company'
        ));
    }
    
    /**
     * Traiter la commande et envoyer WhatsApp
     */
    public function store(Request $request)
    {
        // Récupérer le panier depuis le champ caché
        $cartData = $request->input('cart_data');
        
        if (!$cartData) {
            return redirect()->route('client.cart.index')->with('error', 'Votre panier est vide.');
        }
        
        $cart = json_decode($cartData, true);
        
        if (empty($cart)) {
            return redirect()->route('client.cart.index')->with('error', 'Votre panier est vide.');
        }
        
        // Validation
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'delivery_address' => 'required|string|max:500',
            'delivery_city' => 'nullable|string|max:100',
            'delivery_neighborhood' => 'nullable|string|max:100',
            'delivery_notes' => 'nullable|string|max:500',
            'payment_method' => 'required|in:cash,mobile_money',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Calculer les totaux
        $currency = $this->getCurrency($request);
        $subtotal = 0;
        
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $company = Company::instance();
        $deliveryFee = $company->calculateDeliveryFee($subtotal);
        $total = $subtotal + $deliveryFee;
        
        // Nettoyer le téléphone
        $cleanPhone = preg_replace('/[^0-9]/', '', $request->customer_phone);
        if (strlen($cleanPhone) === 9) {
            $cleanPhone = '243' . $cleanPhone;
        } elseif (strlen($cleanPhone) === 10 && substr($cleanPhone, 0, 1) === '0') {
            $cleanPhone = '243' . substr($cleanPhone, 1);
        }
        
        // Construire le message WhatsApp
        $message = $this->buildWhatsAppMessage($request, $cart, $subtotal, $deliveryFee, $total, $currency);
        
        // URL WhatsApp
        $whatsappNumber = !empty($company->whatsapp) ? $company->whatsapp : '243976717162';
        $whatsappNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);
        if (strlen($whatsappNumber) === 9) {
            $whatsappNumber = '243' . $whatsappNumber;
        }
        
        $encodedMessage = urlencode($message);
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$encodedMessage}";
        
        // Rediriger vers WhatsApp
        return redirect()->away($whatsappUrl);
    }
    
    /**
     * Construire le message WhatsApp
     */
    private function buildWhatsAppMessage($request, $cart, $subtotal, $deliveryFee, $total, $currency)
    {
        $company = Company::instance();
        
        $message = "🛍️ *NOUVELLE COMMANDE* 🛍️\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "📋 *INFORMATIONS CLIENT*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "👤 *Nom:* {$request->customer_name}\n";
        $message .= "📞 *Téléphone:* {$request->customer_phone}\n";
        if ($request->customer_email) {
            $message .= "📧 *Email:* {$request->customer_email}\n";
        }
        $message .= "\n";
        
        $message .= "🚚 *LIVRAISON*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "📍 *Adresse:* {$request->delivery_address}\n";
        if ($request->delivery_neighborhood) {
            $message .= "🏘️ *Quartier:* {$request->delivery_neighborhood}\n";
        }
        if ($request->delivery_city) {
            $message .= "🌆 *Ville:* {$request->delivery_city}\n";
        }
        if ($request->delivery_notes) {
            $message .= "📝 *Notes:* {$request->delivery_notes}\n";
        }
        $message .= "\n";
        
        $message .= "💰 *PAIEMENT*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $paymentMethod = $request->payment_method === 'cash' ? '💰 Espèces à la livraison' : '📱 Mobile Money';
        $message .= "💳 *Méthode:* {$paymentMethod}\n";
        $message .= "\n";
        
        $message .= "📦 *DÉTAILS DE LA COMMANDE*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        
        foreach ($cart as $index => $item) {
            $num = $index + 1;
            $itemTotal = $item['price'] * $item['quantity'];
            $priceFormatted = $this->formatPrice($item['price'], $currency);
            $totalFormatted = $this->formatPrice($itemTotal, $currency);
            $message .= "{$num}. {$item['name']}\n";
            $message .= "   └─ Quantité: {$item['quantity']} × {$priceFormatted} = {$totalFormatted}\n";
        }
        
        $message .= "\n";
        $message .= "💰 *TOTAUX*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "📊 *Sous-total:* {$this->formatPrice($subtotal, $currency)}\n";
        $message .= "🚚 *Livraison:* " . ($deliveryFee > 0 ? $this->formatPrice($deliveryFee, $currency) : "Offerte") . "\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "💰 *TOTAL À PAYER:* {$this->formatPrice($total, $currency)}\n";
        $message .= "\n";
        
        $message .= "🙏 Merci d'avoir choisi " . (!empty($company->name) ? $company->name : 'Mibaraka House') . " !\n";
        $message .= "❤️ Nous vous contacterons sous peu pour la confirmation.";
        
        return $message;
    }
    
    /**
     * Formater un prix
     */
    private function formatPrice($price, $currency)
    {
        if ($currency->code === 'CDF') {
            return $currency->symbol . ' ' . number_format($price * $currency->rate, 0, ',', ' ');
        }
        return $currency->symbol . ' ' . number_format($price, 2, ',', ' ');
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
}