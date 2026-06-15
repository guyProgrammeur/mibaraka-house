@extends('admin.layouts.app')

@section('title', 'Nouvelle commande')
@section('header', 'Nouvelle commande')

@section('breadcrumb')
    <div class="text-sm text-neutral-500">
        <a href="{{ route('admin.orders.index') }}" class="hover:text-gold">Commandes</a> <span class="mx-1">/</span>
        <span class="text-gold">Nouvelle commande</span>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne gauche : Informations client -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Section Client -->
                <div class="bg-white border border-neutral-200 rounded-sm">
                    <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                        <h3 class="font-semibold text-neutral-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Informations client
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">
                                    Nom complet <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                                       class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition @error('customer_name') border-red-500 @enderror">
                                @error('customer_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">
                                    Téléphone <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required
                                       placeholder="ex: 243XXXXXXXXX"
                                       class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition @error('customer_phone') border-red-500 @enderror">
                                @error('customer_phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">
                                Email
                            </label>
                            <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                   class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition @error('customer_email') border-red-500 @enderror">
                            @error('customer_email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Sélection client existant (optionnel) -->
                        <div class="pt-2 border-t border-neutral-100">
                            <button type="button" onclick="toggleExistingCustomer()" class="text-sm text-gold hover:underline flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Charger un client existant
                            </button>
                            
                            <div id="existingCustomerSection" style="display: none;" class="mt-4">
                                <label class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">
                                    Rechercher un client
                                </label>
                                <select name="customer_id" id="customerSelect" class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm">
                                    <option value="">-- Sélectionner un client --</option>
                                    @foreach($customers ?? [] as $customer)
                                        <option value="{{ $customer->id }}" data-name="{{ $customer->name }}" data-phone="{{ $customer->phone }}" data-email="{{ $customer->email }}">
                                            {{ $customer->name }} - {{ $customer->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Section Adresse de livraison -->
                <div class="bg-white border border-neutral-200 rounded-sm">
                    <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                        <h3 class="font-semibold text-neutral-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Adresse de livraison
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">
                                Adresse complète
                            </label>
                            <textarea name="delivery_address" rows="2" class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">{{ old('delivery_address') }}</textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">
                                    Ville
                                </label>
                                <input type="text" name="delivery_city" value="{{ old('delivery_city') }}"
                                       class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">
                                    Quartier
                                </label>
                                <input type="text" name="delivery_neighborhood" value="{{ old('delivery_neighborhood') }}"
                                       class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">
                                Notes de livraison
                            </label>
                            <textarea name="delivery_notes" rows="2" placeholder="Point de repère, instructions particulières..." class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">{{ old('delivery_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Colonne droite : Produits et paiement -->
            <div class="space-y-6">
                <!-- Section Produits -->
                <div class="bg-white border border-neutral-200 rounded-sm">
                    <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50 flex justify-between items-center">
                        <h3 class="font-semibold text-neutral-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            Produits
                        </h3>
                        <button type="button" onclick="addProductRow()" class="text-xs bg-gold text-black px-3 py-1 rounded-sm hover:bg-gold-dark transition">
                            + Ajouter produit
                        </button>
                    </div>
                    <div class="p-5">
                        <div id="productsContainer" class="space-y-3">
                            <div class="product-row grid grid-cols-12 gap-2 items-start">
                                <div class="col-span-6">
                                    <select name="products[0][product_id]" class="product-select w-full border border-neutral-200 rounded-sm px-2 py-2 text-sm" onchange="updateProductPrice(this, 0)">
                                        <option value="">Sélectionner un produit</option>
                                        @foreach($products ?? [] as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                {{ $product->name }} - {{ number_format($product->price, 2) }} USD
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-3">
                                    <input type="number" name="products[0][quantity]" class="quantity-input w-full border border-neutral-200 rounded-sm px-2 py-2 text-sm" value="1" min="1" onchange="calculateRowTotal(0)">
                                </div>
                                <div class="col-span-2">
                                    <input type="text" name="products[0][subtotal]" class="row-subtotal w-full border border-neutral-200 rounded-sm px-2 py-2 text-sm bg-neutral-50" readonly>
                                </div>
                                <div class="col-span-1">
                                    <button type="button" onclick="removeProductRow(this)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-neutral-100">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-neutral-600">Sous-total:</span>
                                <span id="subtotalDisplay" class="font-semibold">0.00 USD</span>
                            </div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-neutral-600">Frais de livraison:</span>
                                <input type="number" name="delivery_fee" id="deliveryFee" value="0" step="0.01" class="w-32 text-right border border-neutral-200 rounded-sm px-2 py-1 text-sm" onchange="updateTotal()">
                            </div>
                            <div class="flex justify-between text-lg font-bold pt-2 border-t border-neutral-100">
                                <span>Total:</span>
                                <span id="totalDisplay">0.00 USD</span>
                            </div>
                            <input type="hidden" name="subtotal" id="subtotalInput" value="0">
                            <input type="hidden" name="total_amount" id="totalInput" value="0">
                        </div>
                    </div>
                </div>
                
                <!-- Section Paiement -->
                <div class="bg-white border border-neutral-200 rounded-sm">
                    <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                        <h3 class="font-semibold text-neutral-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Paiement
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">
                                Méthode de paiement
                            </label>
                            <select name="payment_method" class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm">
                                <option value="cash">Espèces</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="bank_transfer">Virement bancaire</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">
                                Statut du paiement
                            </label>
                            <select name="payment_status" class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm">
                                <option value="pending">En attente</option>
                                <option value="paid">Payé</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">
                                Devise
                            </label>
                            <select name="currency_id" class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm">
                                @foreach($currencies ?? [] as $currency)
                                    <option value="{{ $currency->id }}" data-code="{{ $currency->code }}" data-rate="{{ $currency->rate }}">
                                        {{ $currency->code }} - {{ $currency->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">
                                Notes admin
                            </label>
                            <textarea name="admin_notes" rows="2" class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">{{ old('admin_notes') }}</textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Boutons d'action -->
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-gold hover:bg-gold-dark text-black px-4 py-2 text-sm uppercase tracking-wider font-semibold transition-all rounded-sm">
                        Créer la commande
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="flex-1 bg-neutral-200 hover:bg-neutral-300 text-neutral-700 px-4 py-2 text-sm uppercase tracking-wider font-semibold transition-all rounded-sm text-center">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let productCount = 1;

function addProductRow() {
    const container = document.getElementById('productsContainer');
    const newRow = document.createElement('div');
    newRow.className = 'product-row grid grid-cols-12 gap-2 items-start';
    newRow.innerHTML = `
        <div class="col-span-6">
            <select name="products[${productCount}][product_id]" class="product-select w-full border border-neutral-200 rounded-sm px-2 py-2 text-sm" onchange="updateProductPrice(this, ${productCount})">
                <option value="">Sélectionner un produit</option>
                @foreach($products ?? [] as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                        {{ $product->name }} - {{ number_format($product->price, 2) }} USD
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-span-3">
            <input type="number" name="products[${productCount}][quantity]" class="quantity-input w-full border border-neutral-200 rounded-sm px-2 py-2 text-sm" value="1" min="1" onchange="calculateRowTotal(${productCount})">
        </div>
        <div class="col-span-2">
            <input type="text" name="products[${productCount}][subtotal]" class="row-subtotal w-full border border-neutral-200 rounded-sm px-2 py-2 text-sm bg-neutral-50" readonly>
        </div>
        <div class="col-span-1">
            <button type="button" onclick="removeProductRow(this)" class="text-red-500 hover:text-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    `;
    container.appendChild(newRow);
    productCount++;
}

function removeProductRow(button) {
    const row = button.closest('.product-row');
    if (document.querySelectorAll('.product-row').length > 1) {
        row.remove();
        updateTotal();
    } else {
        alert('Vous devez avoir au moins un produit');
    }
}

function updateProductPrice(select, index) {
    const selectedOption = select.options[select.selectedIndex];
    const price = selectedOption.dataset.price || 0;
    const quantityInput = document.querySelector(`input[name="products[${index}][quantity]"]`);
    const subtotalInput = document.querySelector(`input[name="products[${index}][subtotal]"]`);
    
    if (quantityInput && subtotalInput) {
        const quantity = quantityInput.value;
        const subtotal = price * quantity;
        subtotalInput.value = subtotal.toFixed(2);
        updateTotal();
    }
}

function calculateRowTotal(index) {
    const productSelect = document.querySelector(`select[name="products[${index}][product_id]"]`);
    const quantityInput = document.querySelector(`input[name="products[${index}][quantity]"]`);
    const subtotalInput = document.querySelector(`input[name="products[${index}][subtotal]"]`);
    
    if (productSelect && productSelect.value) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = selectedOption.dataset.price || 0;
        const quantity = quantityInput.value;
        const subtotal = price * quantity;
        subtotalInput.value = subtotal.toFixed(2);
        updateTotal();
    }
}

function updateTotal() {
    let subtotal = 0;
    const subtotalInputs = document.querySelectorAll('.row-subtotal');
    subtotalInputs.forEach(input => {
        subtotal += parseFloat(input.value) || 0;
    });
    
    const deliveryFee = parseFloat(document.getElementById('deliveryFee').value) || 0;
    const total = subtotal + deliveryFee;
    
    document.getElementById('subtotalDisplay').innerHTML = subtotal.toFixed(2) + ' USD';
    document.getElementById('totalDisplay').innerHTML = total.toFixed(2) + ' USD';
    document.getElementById('subtotalInput').value = subtotal.toFixed(2);
    document.getElementById('totalInput').value = total.toFixed(2);
}

function toggleExistingCustomer() {
    const section = document.getElementById('existingCustomerSection');
    if (section.style.display === 'none') {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
    }
}

// Charger les infos client depuis la sélection
document.getElementById('customerSelect')?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        document.querySelector('input[name="customer_name"]').value = selectedOption.dataset.name;
        document.querySelector('input[name="customer_phone"]').value = selectedOption.dataset.phone;
        document.querySelector('input[name="customer_email"]').value = selectedOption.dataset.email || '';
    }
});

// Initialiser le premier produit
setTimeout(() => {
    updateTotal();
}, 100);
</script>
@endpush
@endsection