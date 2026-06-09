@extends('admin.layouts.app')

@section('title', 'Entreprise')
@section('header', 'Informations de l\'entreprise')

@section('content')
<form action="{{ route('admin.company.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Colonne gauche -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Informations générales -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-medium">Informations générales</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'entreprise *</label>
                        <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slogan</label>
                        <input type="text" name="slogan" value="{{ old('slogan', $company->slogan) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $company->email) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $company->phone) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                        <input type="tel" name="whatsapp" value="{{ old('whatsapp', $company->whatsapp) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                        <input type="text" name="city" value="{{ old('city', $company->city) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                        <input type="text" name="address" value="{{ old('address', $company->address) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                        <input type="text" name="country" value="{{ old('country', $company->country) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>
            
            <!-- Livraison -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-medium">Frais de livraison</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frais standard (USD)</label>
                        <input type="number" name="delivery_fee_standard" step="0.5" 
                               value="{{ old('delivery_fee_standard', $company->delivery_fee_standard) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Livraison gratuite à partir de (USD)</label>
                        <input type="number" name="delivery_fee_free_threshold" step="1"
                               value="{{ old('delivery_fee_free_threshold', $company->delivery_fee_free_threshold) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>
            
            <!-- Horaires -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-medium">Horaires d'ouverture</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ouverture</label>
                        <input type="time" name="opening_hours" value="{{ old('opening_hours', $company->opening_hours) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fermeture</label>
                        <input type="time" name="closing_hours" value="{{ old('closing_hours', $company->closing_hours) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jours ouvrés</label>
                        <select name="working_days[]" multiple class="w-full border rounded-lg px-3 py-2" size="7">
                            <option value="monday" {{ in_array('monday', (array)old('working_days', $company->working_days ?? [])) ? 'selected' : '' }}>Lundi</option>
                            <option value="tuesday" {{ in_array('tuesday', (array)old('working_days', $company->working_days ?? [])) ? 'selected' : '' }}>Mardi</option>
                            <option value="wednesday" {{ in_array('wednesday', (array)old('working_days', $company->working_days ?? [])) ? 'selected' : '' }}>Mercredi</option>
                            <option value="thursday" {{ in_array('thursday', (array)old('working_days', $company->working_days ?? [])) ? 'selected' : '' }}>Jeudi</option>
                            <option value="friday" {{ in_array('friday', (array)old('working_days', $company->working_days ?? [])) ? 'selected' : '' }}>Vendredi</option>
                            <option value="saturday" {{ in_array('saturday', (array)old('working_days', $company->working_days ?? [])) ? 'selected' : '' }}>Samedi</option>
                            <option value="sunday" {{ in_array('sunday', (array)old('working_days', $company->working_days ?? [])) ? 'selected' : '' }}>Dimanche</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Réseaux sociaux -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-medium">Réseaux sociaux</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fab fa-facebook text-blue-600 mr-1"></i> Facebook
                        </label>
                        <input type="url" name="facebook" value="{{ old('facebook', $company->facebook) }}"
                               class="w-full border rounded-lg px-3 py-2" placeholder="https://facebook.com/...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fab fa-instagram text-pink-600 mr-1"></i> Instagram
                        </label>
                        <input type="url" name="instagram" value="{{ old('instagram', $company->instagram) }}"
                               class="w-full border rounded-lg px-3 py-2" placeholder="https://instagram.com/...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fab fa-twitter text-blue-400 mr-1"></i> Twitter
                        </label>
                        <input type="url" name="twitter" value="{{ old('twitter', $company->twitter) }}"
                               class="w-full border rounded-lg px-3 py-2" placeholder="https://twitter.com/...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fab fa-youtube text-red-600 mr-1"></i> YouTube
                        </label>
                        <input type="url" name="youtube" value="{{ old('youtube', $company->youtube) }}"
                               class="w-full border rounded-lg px-3 py-2" placeholder="https://youtube.com/...">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Colonne droite -->
        <div class="space-y-6">
            
            <!-- Logos -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-medium">Logos</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo principal</label>
                        @if($company->logo_path)
                        <div class="mb-2">
                            <img src="{{ $company->logo_url }}" class="h-20 object-contain border rounded p-2">
                        </div>
                        @endif
                        <input type="file" name="logo" accept="image/jpeg,image/png,image/svg+xml" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                        <input type="file" name="favicon" accept="image/x-icon,image/png" class="w-full border rounded-lg px-3 py-2">
                        <p class="text-xs text-gray-500 mt-1">Format ICO ou PNG (32x32px)</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo facture</label>
                        @if($company->invoice_logo_path)
                        <div class="mb-2">
                            <img src="{{ $company->invoice_logo_url }}" class="h-16 object-contain border rounded p-2">
                        </div>
                        @endif
                        <input type="file" name="invoice_logo" accept="image/jpeg,image/png" class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>
            
            <!-- Informations légales -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-medium">Informations légales</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">N° d'enregistrement</label>
                        <input type="text" name="registration_number" value="{{ old('registration_number', $company->registration_number) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">N° d'impôt</label>
                        <input type="text" name="tax_number" value="{{ old('tax_number', $company->tax_number) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>
            
            <!-- Personnalisation -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-medium">Personnalisation</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Couleur principale</label>
                        <div class="flex gap-2">
                            <input type="color" name="primary_color" value="{{ old('primary_color', $company->primary_color) }}"
                                   class="w-16 h-10 border rounded">
                            <input type="text" name="primary_color_text" value="{{ old('primary_color', $company->primary_color) }}"
                                   class="flex-1 border rounded-lg px-3 py-2" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Couleur secondaire</label>
                        <div class="flex gap-2">
                            <input type="color" name="secondary_color" value="{{ old('secondary_color', $company->secondary_color) }}"
                                   class="w-16 h-10 border rounded">
                            <input type="text" name="secondary_color_text" value="{{ old('secondary_color', $company->secondary_color) }}"
                                   class="flex-1 border rounded-lg px-3 py-2" readonly>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- WhatsApp -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-medium">Notifications WhatsApp</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message de bienvenue</label>
                        <textarea name="whatsapp_greeting" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('whatsapp_greeting', $company->whatsapp_greeting) }}</textarea>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="whatsapp_notifications" value="1" 
                                   {{ old('whatsapp_notifications', $company->whatsapp_notifications) ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm text-gray-700">Activer les notifications WhatsApp</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-6 flex justify-end gap-3">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-save mr-2"></i>Enregistrer
        </button>
    </div>
</form>

@push('scripts')
<script>
    // Synchronisation des champs de couleur
    const primaryColor = document.querySelector('input[name="primary_color"]');
    const primaryColorText = document.querySelector('input[name="primary_color_text"]');
    const secondaryColor = document.querySelector('input[name="secondary_color"]');
    const secondaryColorText = document.querySelector('input[name="secondary_color_text"]');
    
    if (primaryColor && primaryColorText) {
        primaryColor.addEventListener('input', () => primaryColorText.value = primaryColor.value);
        primaryColorText.addEventListener('input', () => primaryColor.value = primaryColorText.value);
    }
    
    if (secondaryColor && secondaryColorText) {
        secondaryColor.addEventListener('input', () => secondaryColorText.value = secondaryColor.value);
        secondaryColorText.addEventListener('input', () => secondaryColor.value = secondaryColorText.value);
    }
</script>
@endpush
@endsection