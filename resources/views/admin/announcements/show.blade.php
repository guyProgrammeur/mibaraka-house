@extends('admin.layouts.app')

@section('title', $announcement->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.announcements.index') }}" class="text-neutral-500 hover:text-neutral-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <span class="text-neutral-400">/</span>
                <a href="{{ route('admin.announcements.index') }}" class="text-neutral-500 hover:text-neutral-900 transition-colors">Annonces</a>
                <span class="text-neutral-400">/</span>
                <span class="text-neutral-900 font-medium">Détail</span>
            </div>
            <h1 class="text-2xl font-serif font-bold text-neutral-900">{{ $announcement->title }}</h1>
            <p class="text-sm text-neutral-500 mt-1">Détails de l'annonce</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.announcements.edit', $announcement) }}" 
               class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 hover:text-white transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Modifier
            </a>
            <a href="{{ route('admin.announcements.index') }}" 
               class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-100 transition-colors">
                Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche : Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Carte information -->
            <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <h2 class="font-semibold text-neutral-900">Informations générales</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Titre</label>
                        <p class="text-neutral-900">{{ $announcement->title }}</p>
                    </div>
                    
                    @if($announcement->badge)
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Badge</label>
                        <p class="inline-block bg-neutral-100 text-neutral-700 text-xs px-2 py-1 rounded">{{ $announcement->badge }}</p>
                    </div>
                    @endif
                    
                    @if($announcement->message)
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Message</label>
                        <p class="text-neutral-600 whitespace-pre-wrap">{{ $announcement->message }}</p>
                    </div>
                    @endif
                    
                    @if($announcement->icon)
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Icône</label>
                        <div class="flex items-center gap-2">
                            <i class="{{ $announcement->icon }} text-gold text-xl"></i>
                            <code class="text-xs bg-neutral-100 px-2 py-1 rounded">{{ $announcement->icon }}</code>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Carte bouton CTA -->
            @if($announcement->button_text)
            <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <h2 class="font-semibold text-neutral-900">Bouton d'appel à l'action</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Texte</label>
                        <p class="text-neutral-900">{{ $announcement->button_text }}</p>
                    </div>
                    @if($announcement->button_link)
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Lien</label>
                        <a href="{{ $announcement->button_link }}" target="_blank" class="text-gold hover:underline text-sm break-all">
                            {{ $announcement->button_link }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Carte image -->
            @if($announcement->image)
            <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <h2 class="font-semibold text-neutral-900">Image</h2>
                </div>
                <div class="p-6">
                    <img src="{{ Storage::url($announcement->image) }}" alt="{{ $announcement->title }}" class="max-w-full h-auto rounded-lg shadow-sm">
                </div>
            </div>
            @endif
        </div>

        <!-- Colonne droite : Métadonnées -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Statut -->
            <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <h2 class="font-semibold text-neutral-900">Statut</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $announcement->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        <span class="font-medium">{{ $announcement->is_active ? 'Actif' : 'Inactif' }}</span>
                    </div>
                </div>
            </div>

            <!-- Type et position -->
            <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <h2 class="font-semibold text-neutral-900">Affichage</h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-500">Type</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            {{ $announcement->type === 'text' ? 'bg-gray-100 text-gray-700' : '' }}
                            {{ $announcement->type === 'button' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $announcement->type === 'image' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $announcement->type === 'image_text' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $announcement->type === 'banner' ? 'bg-pink-100 text-pink-700' : '' }}">
                            {{ ucfirst($announcement->type) }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-500">Position</span>
                        <span class="text-neutral-900">{{ ucfirst($announcement->position) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-500">Ordre</span>
                        <span class="text-neutral-900">{{ $announcement->order }}</span>
                    </div>
                </div>
            </div>

            <!-- Période de validité -->
            <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <h2 class="font-semibold text-neutral-900">Validité</h2>
                </div>
                <div class="p-6 space-y-3">
                    @if($announcement->start_date || $announcement->end_date)
                        @if($announcement->start_date)
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-500">Date de début</span>
                            <span class="text-neutral-900">{{ $announcement->start_date->format('d/m/Y') }}</span>
                        </div>
                        @endif
                        @if($announcement->end_date)
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-500">Date de fin</span>
                            <span class="text-neutral-900">{{ $announcement->end_date->format('d/m/Y') }}</span>
                        </div>
                        @endif
                    @else
                        <p class="text-sm text-neutral-500">Période permanente</p>
                    @endif
                </div>
            </div>

            <!-- Dates système -->
            <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <h2 class="font-semibold text-neutral-900">Système</h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-500">Créé le</span>
                        <span class="text-neutral-900">{{ $announcement->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-500">Modifié le</span>
                        <span class="text-neutral-900">{{ $announcement->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-500">ID</span>
                        <span class="text-neutral-900 font-mono">{{ $announcement->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection