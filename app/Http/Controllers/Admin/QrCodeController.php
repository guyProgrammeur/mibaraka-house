<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrCode as QrCodeModel;
use App\Models\Category;
use App\Models\Product;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Logo\Logo;
use Barryvdh\DomPDF\Facade\Pdf;

class QrCodeController extends Controller
{
    /**
     * Liste des QR codes
     */
    public function index(Request $request)
    {
        $query = QrCodeModel::with(['category', 'product']);
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $qrCodes = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        $stats = [
            'total' => QrCodeModel::count(),
            'active' => QrCodeModel::where('is_active', true)->count(),
            'catalog' => QrCodeModel::where('type', 'catalog')->count(),
            'category' => QrCodeModel::where('type', 'category')->count(),
            'product' => QrCodeModel::where('type', 'product')->count(),
            'total_scans' => QrCodeModel::sum('scan_count'),
        ];
        
        return view('admin.qr-codes.index', compact('qrCodes', 'stats'));
    }
    
    /**
     * Formulaire de création
     */
    public function create()
    {
        $categories = Category::with('parent')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price']);
        
        $company = Company::instance();
            
        return view('admin.qr-codes.create', compact('categories', 'products', 'company'));
    }
    
    /**
     * Enregistrer un QR code
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:qr_codes,name',
            'type' => 'required|in:catalog,category,product',
            'category_id' => 'required_if:type,category|nullable|exists:categories,id',
            'product_id' => 'required_if:type,product|nullable|exists:products,id',
            'size' => 'required|in:small,medium,large',
            'color' => 'required|in:black,white,custom',
            'custom_color' => 'required_if:color,custom|nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:512',
            'description' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
            'poster_template' => 'nullable|in:classic,elegant,modern,minimal,luxury',
            'show_brand_name' => 'sometimes|boolean',
            'show_tagline' => 'sometimes|boolean',
            'poster_background_color' => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'poster_primary_color' => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'poster_text_color' => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'custom_message' => 'nullable|string|max:200',
        ]);
        
        $validated['code'] = QrCodeModel::generateUniqueCode();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['poster_template'] = $request->poster_template ?? 'classic';
        $validated['show_brand_name'] = $request->boolean('show_brand_name', true);
        $validated['show_tagline'] = $request->boolean('show_tagline', true);
        $validated['poster_background_color'] = $request->poster_background_color ?? '#FFFFFF';
        $validated['poster_primary_color'] = $request->poster_primary_color ?? '#D4AF37';
        $validated['poster_text_color'] = $request->poster_text_color ?? '#1a1a1a';
        $validated['custom_message'] = $request->custom_message;
        
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('qr-logos', 'public');
            $validated['logo_path'] = $path;
        }
        
        $qrCode = QrCodeModel::create($validated);
        
        return redirect()
            ->route('admin.qr-codes.show', $qrCode)
            ->with('success', 'QR code créé avec succès.');
    }
    
    /**
     * Afficher un QR code (aperçu + téléchargement)
     */
    public function show(QrCodeModel $qrCode)
    {
        $qrCodeDataUrl = $this->generateQrCodeDataUrl($qrCode);
        $company = Company::instance();
        
        return view('admin.qr-codes.show', compact('qrCode', 'qrCodeDataUrl', 'company'));
    }
    
    /**
     * Formulaire d'édition
     */
    public function edit(QrCodeModel $qrCode)
    {
        $categories = Category::with('parent')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price']);
        
        $company = Company::instance();
            
        return view('admin.qr-codes.edit', compact('qrCode', 'categories', 'products', 'company'));
    }
    
    /**
     * Mettre à jour un QR code
     */
    public function update(Request $request, QrCodeModel $qrCode)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('qr_codes')->ignore($qrCode->id)],
            'type' => 'required|in:catalog,category,product',
            'category_id' => 'required_if:type,category|nullable|exists:categories,id',
            'product_id' => 'required_if:type,product|nullable|exists:products,id',
            'size' => 'required|in:small,medium,large',
            'color' => 'required|in:black,white,custom',
            'custom_color' => 'required_if:color,custom|nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:512',
            'description' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
            'poster_template' => 'nullable|in:classic,elegant,modern,minimal,luxury',
            'show_brand_name' => 'sometimes|boolean',
            'show_tagline' => 'sometimes|boolean',
            'poster_background_color' => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'poster_primary_color' => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'poster_text_color' => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'custom_message' => 'nullable|string|max:200',
        ]);
        
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['poster_template'] = $request->poster_template ?? $qrCode->poster_template ?? 'classic';
        $validated['show_brand_name'] = $request->boolean('show_brand_name', $qrCode->show_brand_name ?? true);
        $validated['show_tagline'] = $request->boolean('show_tagline', $qrCode->show_tagline ?? true);
        $validated['poster_background_color'] = $request->poster_background_color ?? $qrCode->poster_background_color ?? '#FFFFFF';
        $validated['poster_primary_color'] = $request->poster_primary_color ?? $qrCode->poster_primary_color ?? '#D4AF37';
        $validated['poster_text_color'] = $request->poster_text_color ?? $qrCode->poster_text_color ?? '#1a1a1a';
        $validated['custom_message'] = $request->custom_message ?? $qrCode->custom_message;
        
        if ($request->hasFile('logo')) {
            if ($qrCode->logo_path) {
                Storage::disk('public')->delete($qrCode->logo_path);
            }
            $path = $request->file('logo')->store('qr-logos', 'public');
            $validated['logo_path'] = $path;
        }
        
        if ($request->boolean('remove_logo', false) && $qrCode->logo_path) {
            Storage::disk('public')->delete($qrCode->logo_path);
            $validated['logo_path'] = null;
        }
        
        $qrCode->update($validated);
        
        return redirect()
            ->route('admin.qr-codes.show', $qrCode)
            ->with('success', 'QR code mis à jour avec succès.');
    }
    
    /**
     * Supprimer un QR code
     */
    public function destroy(QrCodeModel $qrCode)
    {
        if ($qrCode->logo_path) {
            Storage::disk('public')->delete($qrCode->logo_path);
        }
        
        $qrCode->delete();
        
        return redirect()
            ->route('admin.qr-codes.index')
            ->with('success', 'QR code supprimé avec succès.');
    }
    
    /**
     * Activer/Désactiver un QR code
     */
    public function toggleActive(QrCodeModel $qrCode)
    {
        $qrCode->update(['is_active' => !$qrCode->is_active]);
        
        return response()->json([
            'success' => true,
            'message' => "QR code " . ($qrCode->is_active ? 'activé' : 'désactivé') . " avec succès.",
            'is_active' => $qrCode->is_active
        ]);
    }
    
    /**
     * Télécharger le QR code au format PDF
     */
    public function download(QrCodeModel $qrCode)
    {
        $qrCodeDataUrl = $this->generateQrCodeDataUrl($qrCode);
        $company = Company::instance();
        
        $pdf = Pdf::loadView('admin.qr-codes.poster', [
            'qrCode' => $qrCode,
            'qrCodeDataUrl' => $qrCodeDataUrl,
            'company' => $company
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download($qrCode->slug . '_poster.pdf');
    }
    
    /**
     * Aperçu du poster en HTML
     */
    public function preview(QrCodeModel $qrCode)
    {
        $qrCodeDataUrl = $this->generateQrCodeDataUrl($qrCode);
        $company = Company::instance();
        
        return view('admin.qr-codes.poster', compact('qrCode', 'qrCodeDataUrl', 'company'));
    }
    
    /**
     * Générer le QR code en Data URL
     */
    private function generateQrCodeDataUrl(QrCodeModel $qrCode): string
    {
        $size = match($qrCode->size) {
            'small' => 200,
            'large' => 400,
            default => 300
        };
        
        $foregroundColor = $this->getForegroundColor($qrCode);
        $backgroundColor = new Color(255, 255, 255);
        
        $builder = Builder::create()
            ->writer(new PngWriter())
            ->data($qrCode->destination_url ?? 'https://example.com')
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size($size)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->foregroundColor($foregroundColor)
            ->backgroundColor($backgroundColor);
        
        if ($qrCode->logo_path && Storage::disk('public')->exists($qrCode->logo_path)) {
            $logo = new Logo(
                path: Storage::disk('public')->path($qrCode->logo_path),
                resizeToWidth: 50,
                punchoutBackground: true
            );
            $builder->logo($logo);
        }
        
        $result = $builder->build();
        
        return 'data:image/png;base64,' . base64_encode($result->getString());
    }
    
    /**
     * Télécharger uniquement le QR code brut
     */
    public function downloadQrOnly(QrCodeModel $qrCode)
    {
        $pngContent = $this->generatePng($qrCode);
        $filename = $qrCode->slug . '.png';
        
        return response($pngContent)
            ->withHeaders([
                'Content-Type' => 'image/png',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
    }
    
    /**
     * Générer le QR code en PNG
     */
    private function generatePng(QrCodeModel $qrCode): string
    {
        $size = match($qrCode->size) {
            'small' => 200,
            'large' => 400,
            default => 300
        };
        
        $foregroundColor = $this->getForegroundColor($qrCode);
        $backgroundColor = new Color(255, 255, 255);
        
        $builder = Builder::create()
            ->writer(new PngWriter())
            ->data($qrCode->destination_url ?? 'https://example.com')
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size($size)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->foregroundColor($foregroundColor)
            ->backgroundColor($backgroundColor);
        
        if ($qrCode->logo_path && Storage::disk('public')->exists($qrCode->logo_path)) {
            $logo = new Logo(
                path: Storage::disk('public')->path($qrCode->logo_path),
                resizeToWidth: 50,
                punchoutBackground: true
            );
            $builder->logo($logo);
        }
        
        return $builder->build()->getString();
    }
    
    /**
     * Obtenir la couleur de premier plan du QR code
     */
    private function getForegroundColor(QrCodeModel $qrCode): Color
    {
        if ($qrCode->color === 'custom' && $qrCode->custom_color) {
            $hex = ltrim($qrCode->custom_color, '#');
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            return new Color($r, $g, $b);
        }
        
        if ($qrCode->color === 'white') {
            return new Color(255, 255, 255);
        }
        
        return new Color(0, 0, 0);
    }
    
    /**
     * Statistiques des QR codes (AJAX)
     */
    public function stats()
    {
        $stats = [
            'scans_by_day' => QrCodeModel::where('last_scanned_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(last_scanned_at) as date, SUM(scan_count) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'scans_by_type' => QrCodeModel::selectRaw('type, SUM(scan_count) as total')
                ->groupBy('type')
                ->get(),
            'top_qr_codes' => QrCodeModel::orderBy('scan_count', 'desc')
                ->limit(5)
                ->get(['name', 'type', 'scan_count']),
        ];
        
        return response()->json($stats);
    }
}