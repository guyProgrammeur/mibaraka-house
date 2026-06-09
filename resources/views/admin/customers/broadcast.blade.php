@extends('admin.layouts.app')

@section('title', 'Message groupé WhatsApp')
@section('header', 'Envoyer un message groupé')

@section('content')
<div class="bg-white rounded-lg shadow max-w-2xl mx-auto">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center gap-3 text-yellow-600 bg-yellow-50 p-3 rounded-lg">
            <i class="fas fa-exclamation-triangle text-xl"></i>
            <div class="text-sm">
                <strong>Note importante :</strong> WhatsApp n'autorise pas l'envoi de messages groupés automatisés.
                Cette fonction va ouvrir des onglets individuels pour chaque client. Vous devrez cliquer sur "Envoyer" dans chaque onglet.
            </div>
        </div>
    </div>
    
    <form action="{{ route('admin.customers.broadcast') }}" method="POST">
        @csrf
        
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                <textarea name="message" rows="8" required 
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Bonjour {nom},&#10;&#10;Nous avons une offre spéciale pour vous..."></textarea>
                <p class="text-xs text-gray-500 mt-1">
                    Utilisez <code class="bg-gray-100 px-1 rounded">{nom}</code> pour le nom du client (remplacement automatique)
                </p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Destinataires</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" name="recipient_type" value="all" checked class="mr-2" id="all_customers">
                        <span>Tous les clients actifs ({{ $customersCount }} clients)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="recipient_type" value="selected" class="mr-2" id="selected_customers">
                        <span>Sélectionner des clients spécifiques</span>
                    </label>
                </div>
            </div>
            
            <div id="customer_selection" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Choisir les clients</label>
                <select name="customer_ids[]" multiple class="w-full border rounded-lg px-3 py-2" size="8">
                    @foreach(\App\Models\Customer::where('is_active', true)->orderBy('name')->get() as $c)
                    <option value="{{ $c->id }}">
                        {{ $c->name }} - {{ $c->formatted_phone }} ({{ $c->total_orders }} commandes)
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs clients</p>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
            <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fab fa-whatsapp mr-2"></i>Préparer les messages
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const radioAll = document.getElementById('all_customers');
    const radioSelected = document.getElementById('selected_customers');
    const selectionDiv = document.getElementById('customer_selection');
    
    function toggleSelection() {
        selectionDiv.classList.toggle('hidden', radioAll.checked);
    }
    
    radioAll.addEventListener('change', toggleSelection);
    radioSelected.addEventListener('change', toggleSelection);
    toggleSelection();
</script>
@endpush
@endsection