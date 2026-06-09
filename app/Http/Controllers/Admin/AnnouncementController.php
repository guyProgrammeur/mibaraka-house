<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AnnouncementController extends Controller
{
    /**
     * Liste des annonces
     */
    public function index(Request $request)
    {
        $query = Announcement::orderBy('position')
            ->orderBy('order')
            ->orderBy('created_at', 'desc');
        
        // Filtre par recherche
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filtre par position
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }
        
        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $announcements = $query->paginate(20)->withQueryString();
        
        // Statistiques
        $stats = [
            'total' => Announcement::count(),
            'active' => Announcement::where('is_active', true)->count(),
            'inactive' => Announcement::where('is_active', false)->count(),
            'types' => Announcement::getTypes(),
            'positions' => Announcement::getPositions(),
        ];
        
        return view('admin.announcements.index', compact('announcements', 'stats'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $types = Announcement::getTypes();
        $positions = Announcement::getPositions();
        $linkOptions = $this->getLinkOptions();
        $iconOptions = $this->getIconOptions();
        
        return view('admin.announcements.create', compact('types', 'positions', 'linkOptions', 'iconOptions'));
    }

    /**
     * Enregistrer une annonce
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'nullable|string|max:1000',
            'type' => 'required|in:' . implode(',', array_keys(Announcement::getTypes())),
            'position' => 'required|in:' . implode(',', array_keys(Announcement::getPositions())),
            'badge' => 'nullable|string|max:50',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Gestion de l'image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['order'] = $request->order ?? 0;

        $announcement = Announcement::create($validated);
        
        // Vider le cache des annonces
        $this->clearAnnouncementsCache();

        return redirect()->route('admin.announcements.index')
            ->with('success', "Annonce « {$announcement->title} » créée avec succès");
    }

    /**
     * Afficher une annonce
     */
    public function show(Announcement $announcement)
    {
        return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Announcement $announcement)
    {
        $types = Announcement::getTypes();
        $positions = Announcement::getPositions();
        $linkOptions = $this->getLinkOptions();
        $iconOptions = $this->getIconOptions();
        
        return view('admin.announcements.edit', compact('announcement', 'types', 'positions', 'linkOptions', 'iconOptions'));
    }

    /**
     * Mettre à jour une annonce
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'nullable|string|max:1000',
            'type' => 'required|in:' . implode(',', array_keys(Announcement::getTypes())),
            'position' => 'required|in:' . implode(',', array_keys(Announcement::getPositions())),
            'badge' => 'nullable|string|max:50',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Gestion de l'image
        if ($request->hasFile('image')) {
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        // Suppression de l'image si demandée
        if ($request->boolean('remove_image') && $announcement->image) {
            Storage::disk('public')->delete($announcement->image);
            $validated['image'] = null;
        }

        $validated['is_active'] = $request->boolean('is_active', $announcement->is_active);
        $validated['order'] = $request->order ?? $announcement->order;

        $announcement->update($validated);
        
        // Vider le cache des annonces
        $this->clearAnnouncementsCache();

        return redirect()->route('admin.announcements.index')
            ->with('success', "Annonce « {$announcement->title} » modifiée avec succès");
    }

    /**
     * Supprimer une annonce
     */
    public function destroy(Announcement $announcement)
    {
        $title = $announcement->title;
        
        if ($announcement->image) {
            Storage::disk('public')->delete($announcement->image);
        }
        
        $announcement->delete();
        
        // Vider le cache des annonces
        $this->clearAnnouncementsCache();

        return redirect()->route('admin.announcements.index')
            ->with('success', "Annonce « {$title} » supprimée avec succès");
    }

    /**
     * Activer/Désactiver une annonce (AJAX)
     */
    public function toggle(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);
        $status = $announcement->is_active ? 'activée' : 'désactivée';
        
        // Vider le cache des annonces
        $this->clearAnnouncementsCache();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Annonce {$status}",
                'is_active' => $announcement->is_active
            ]);
        }
        
        return back()->with('success', "Annonce {$status}");
    }

    /**
     * Dupliquer une annonce
     */
    public function duplicate(Announcement $announcement)
    {
        $newAnnouncement = $announcement->replicate();
        $newAnnouncement->title = $announcement->title . ' (copie)';
        $newAnnouncement->is_active = false;
        $newAnnouncement->save();
        
        // Vider le cache
        $this->clearAnnouncementsCache();
        
        return redirect()->route('admin.announcements.edit', $newAnnouncement)
            ->with('success', 'Annonce dupliquée avec succès');
    }

    /**
     * Réordonner les annonces (AJAX)
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'positions' => 'required|array',
            'positions.*.id' => 'required|exists:announcements,id',
            'positions.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->positions as $item) {
            Announcement::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        
        // Vider le cache
        $this->clearAnnouncementsCache();
        
        return response()->json(['success' => true]);
    }

    /**
     * Exporter les annonces en CSV
     */
    public function export()
    {
        $announcements = Announcement::orderBy('position')
            ->orderBy('order')
            ->get();
        
        $filename = 'annonces_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // En-têtes
        fputcsv($handle, ['ID', 'Titre', 'Message', 'Type', 'Position', 'Badge', 'Ordre', 'Active', 'Créé le']);
        
        foreach ($announcements as $announcement) {
            fputcsv($handle, [
                $announcement->id,
                $announcement->title,
                strip_tags($announcement->message),
                $announcement->type,
                $announcement->position,
                $announcement->badge,
                $announcement->order,
                $announcement->is_active ? 'Oui' : 'Non',
                $announcement->created_at->format('d/m/Y H:i'),
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
     * Statistiques pour le dashboard (AJAX)
     */
    public function stats()
    {
        $stats = [
            'total' => Announcement::count(),
            'active' => Announcement::where('is_active', true)->count(),
            'by_type' => Announcement::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'by_position' => Announcement::selectRaw('position, COUNT(*) as count')
                ->groupBy('position')
                ->get(),
        ];
        
        return response()->json($stats);
    }
    
    /**
     * Vider le cache des annonces
     */
    private function clearAnnouncementsCache()
    {
        // Vider le cache par position
        foreach (['top', 'middle', 'bottom'] as $position) {
            Cache::forget("announcements_{$position}");
        }
    }
    
    /**
     * Options des liens
     */
    private function getLinkOptions(): array
    {
        return [
            '' => '— Aucun lien —',
            route('client.catalog') => '🏠 Accueil',
            route('client.catalog') . '?featured=1' => '⭐ Produits phares',
            route('client.catalog') . '?sort=newest' => '🆕 Nouveautés',
            route('client.catalog') . '?sort=price_asc' => '💰 Meilleurs prix',
            route('client.catalog') . '?promo=1' => '🏷️ Promotions',
            'https://wa.me/' => '📱 WhatsApp',
            'https://www.instagram.com/' => '📷 Instagram',
        ];
    }
    
    /**
     * Options des icônes
     */
    private function getIconOptions(): array
    {
        return [
            '' => '— Aucune icône —',
            'fas fa-bullhorn' => '📢 Mégaphone',
            'fas fa-gift' => '🎁 Cadeau',
            'fas fa-tag' => '🏷️ Étiquette',
            'fas fa-truck' => '🚚 Livraison',
            'fas fa-star' => '⭐ Étoile',
            'fas fa-heart' => '❤️ Cœur',
            'fas fa-bell' => '🔔 Cloche',
            'fas fa-info-circle' => 'ℹ️ Information',
            'fas fa-check-circle' => '✅ Validation',
            'fas fa-exclamation-triangle' => '⚠️ Attention',
            'fas fa-store' => '🏪 Magasin',
            'fas fa-percent' => '% Pourcentage',
            'fas fa-rocket' => '🚀 Rapide',
            'fas fa-shield-alt' => '🛡️ Sécurité',
            'fas fa-headset' => '🎧 Support',
            'fas fa-calendar' => '📅 Calendrier',
            'fas fa-clock' => '⏰ Horloge',
        ];
    }
}