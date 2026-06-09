@extends('admin.layouts.app')

@section('title', 'Nouvelle commande')
@section('header', 'Créer une commande manuelle')

@section('content')
<div class="bg-white rounded-lg shadow">
    <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm">
        @csrf
        
        <div class="p-6 space-y-6">
            <!-- Sélection client -->
            <div class="border rounded-lg p-4">
                <h3 class="font-medium text-gray-800 mb-4">Client</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="flex items-center">
                            <input type="radio" name="customer_type" value="existing" checked class="mr-2" id="existing_customer">
                            <span>Client existant</span>
                        </label>
                        <div id="existing_customer_div" class="mt-2">
                            <select name="customer_id" class="w-full border rounded-lg px-3 py-2">
                                <option value="">-- Sélectionner un client --</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                    data-name="{{ $customer->name }}"
                                    data-phone="{{ $customer->phone }}"
                                    data-email="{{ $customer->email }}"
                                    data-address="{{ $customer->default_address }}"
                                    data-city="{{ $customer->city }}"
                                    data-neighborhood="{{ $customer->neighborhood }}">
                                    {{ $customer->name }} - {{ $customer->formatted_phone }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="flex items-center">
                            <input type="radio" name="customer_type" value="new" class="mr-2" id="new_customer">
                            <span>Nouveau client</span>
                        </label>
                        <div id="new_customer_div" class="mt-2 hidden space-y-3">
                            <input type="text" name="customer_name" placeholder="Nom complet" class="w-full border rounded-lg px-3 py-2">
                            <input type="tel" name="customer_phone" placeholder="Téléphone" class="w-full border rounded-lg px-3 py-2">
                            <input type="email" name="customer_email" placeholder="Email (optionnel)" class="w-full border rounded-lg px-3 py-2">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Adresse de livraison -->
            <div class="border rounded-lg p-4">
                <h3 class="font-medium text-gray-800 mb-4">Adresse de livraison</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <input type="text" name="delivery_address" placeholder="Adresse" required class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <input type="text" name="delivery_neighborhood" placeholder="Quartier" class="w-full border rounded-lg px-3 py-2">
                    <input type="text" name="delivery_city" placeholder="Ville" class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>
            
            <!-- Produits -->
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-medium text-gray-800">Produits</h3>
                    <button type="button" onclick="addProductRow()" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-plus mr-1"></i>Ajouter un produit
                    </button>
                </div>
                
                <div id="products_container" class="space-y-3">
                    <div class="product-row grid grid-cols-1 md:grid-cols-4 gap-3">
                        <select name="items[0][product_id]" required class="product-select border rounded-lg px-3 py-2">
                            <option value="">-- Produit --</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                {{ $product->name }} - {{ number_format($product->price, 2) }} $
                            </option>
                            @endforeach
                        </select>
                        <input type="number" name="items[0][quantity]" placeholder="Quantité" value="1" min="1" required class="border rounded-lg px-3 py-2">
                        <div class="product-price text-right font-medium px-3 py-2 bg-gray-50 rounded">0.00 $</div>
                        <button type="button" onclick="removeProductRow(this)" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mt-4 text-right">
                    <p class="text-lg font-bold">
                        Total: <span id="total_preview">0.00</span> $
                    </p>
                </div>
            </div>
            
            <!-- Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Frais de livraison</label>
                    <input type="number" name="delivery_fee" value="0" step="0.01" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Devise</label>
                    <select name="currency_code" class="w-full border rounded-lg px-3 py-2">
                        @foreach($currencies as $currency)
                        <option value="{{ $currency->code }}" data-rate="{{ $currency->rate }}">
                            {{ $currency->code }} - {{ $currency->symbol }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Créer la commande
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let productIndex = 1;
    
    // Gestion affichage client existant/nouveau
    const existingRadio = document.getElementById('existing_customer');
    const newRadio = document.getElementById('new_customer');
    const existingDiv = document.getElementById('existing_customer_div');
    const newDiv = document.getElementById('new_customer_div');
    const customerSelect = document.querySelector('select[name="customer_id"]');
    
    existingRadio.addEventListener('change', function() {
        existingDiv.classList.remove('hidden');
        newDiv.classList.add('hidden');
    });
    
    newRadio.addEventListener('change', function() {
        existingDiv.classList.add('hidden');
        newDiv.classList.remove('hidden');
    });
    
    // Auto-remplissage adresse client existant
    customerSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            document.querySelector('input[name="delivery_address"]').value = selected.dataset.address || '';
            document.querySelector('input[name="delivery_city"]').value = selected.dataset.city || '';
            document.querySelector('input[name="delivery_neighborhood"]').value = selected.dataset.neighborhood || '';
        }
    });
    
    // Gestion produits dynamiques
    function addProductRow() {
        const container = document.getElementById('products_container');
        const template = `
            <div class="product-row grid grid-cols-1 md:grid-cols-4 gap-3">
                <select name="items[${productIndex}][product_id]" required class="product-select border rounded-lg px-3 py-2">
                    <option value="">-- Produit --</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                        {{ $product->name }} - {{ number_format($product->price, 2) }} $
                    </option>
                    @endforeach
                </select>
                <input type="number" name="items[${productIndex}][quantity]" placeholder="Quantité" value="1" min="1" required class="border rounded-lg px-3 py-2">
                <div class="product-price text-right font-medium px-3 py-2 bg-gray-50 rounded">0.00 $</div>
                <button type="button" onclick="removeProductRow(this)" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', template);
        attachProductEvents(container.lastElementChild);
        productIndex++;
    }
    
    function attachProductEvents(row) {
        const select = row.querySelector('.product-select');
        const quantity = row.querySelector('input[type="number"]');
        const priceDisplay = row.querySelector('.product-price');
        
        const updatePrice = () => {
            const selected = select.options[select.selectedIndex];
            const price = selected.value ? parseFloat(selected.dataset.price) : 0;
            const qty = parseInt(quantity.value) || 0;
            const total = price * qty;
            priceDisplay.textContent = total.toFixed(2) + ' $';
            updateTotal();
        };
        
        select.addEventListener('change', updatePrice);
        quantity.addEventListener('input', updatePrice);
        updatePrice();
    }
    
    function removeProductRow(btn) {
        if (document.querySelectorAll('.product-row').length > 1) {
            btn.closest('.product-row').remove();
            updateTotal();
        } else {
            alert('Au moins un produit est requis');
        }
    }
    
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const priceDisplay = row.querySelector('.product-price');
            if (priceDisplay) {
                const price = parseFloat(priceDisplay.textContent) || 0;
                total += price;
            }
        });
        const deliveryFee = parseFloat(document.querySelector('input[name="delivery_fee"]').value) || 0;
        document.getElementById('total_preview').textContent = (total + deliveryFee).toFixed(2);
    }
    
    document.querySelector('input[name="delivery_fee"]').addEventListener('input', updateTotal);
    
    // Attacher événements à la première ligne
    attachProductEvents(document.querySelector('.product-row'));
</script>
@endpush
@endsection