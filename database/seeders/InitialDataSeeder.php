<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class InitialDataSeeder extends Seeder
{
    /**
     * Exécute les seeders
     */
    public function run(): void
    {
        // ========== 1. DEVISES (Currencies) ==========
        echo "\n🌍 Création des devises...\n";
        
        $usd = Currency::create([
            'code' => 'USD',
            'symbol' => '$',
            'name' => 'Dollar américain',
            'rate' => 1.0000,
            'is_default' => true,
            'is_active' => true
        ]);
        
        $cdf = Currency::create([
            'code' => 'CDF',
            'symbol' => 'FC',
            'name' => 'Franc congolais',
            'rate' => 2850.0000,
            'is_default' => false,
            'is_active' => true
        ]);
        
        $eur = Currency::create([
            'code' => 'EUR',
            'symbol' => '€',
            'name' => 'Euro',
            'rate' => 0.9200,
            'is_default' => false,
            'is_active' => true
        ]);
        
        echo "✓ 3 devises créées : USD, CDF, EUR\n";
        
        // ========== 2. CATÉGORIES (Categories) ==========
        echo "\n📂 Création des catégories...\n";
        
        // Volets principaux
        $alimentaire = Category::create([
            'name' => 'Produits Alimentaires',
            'slug' => 'produits-alimentaires',
            'description' => 'Découvrez notre sélection de produits alimentaires de qualité, sélectionnés pour vous et votre famille.',
            'icon' => 'fas fa-utensils',
            'position' => 1,
            'is_active' => true
        ]);
        
        $cosmetique = Category::create([
            'name' => 'Produits Cosmétiques',
            'slug' => 'produits-cosmetiques',
            'description' => 'Prenez soin de vous avec nos cosmétiques naturels et produits de beauté.',
            'icon' => 'fas fa-spa',
            'position' => 2,
            'is_active' => true
        ]);
        
        $electromenager = Category::create([
            'name' => 'Électroménager',
            'slug' => 'electromenager',
            'description' => 'Équipez votre maison avec nos appareils électroménagers de qualité.',
            'icon' => 'fas fa-blender',
            'position' => 3,
            'is_active' => true
        ]);
        
        // Sous-catégories ALIMENTAIRES
        $rizFarine = Category::create([
            'name' => 'Riz & Farine',
            'slug' => 'riz-farine',
            'description' => 'Riz, farine et céréales de base',
            'icon' => 'fas fa-bread-slice',
            'parent_id' => $alimentaire->id,
            'position' => 1,
            'is_active' => true
        ]);
        
        $boissons = Category::create([
            'name' => 'Boissons',
            'slug' => 'boissons',
            'description' => 'Jus, sodas, eaux et boissons traditionnelles',
            'icon' => 'fas fa-wine-bottle',
            'parent_id' => $alimentaire->id,
            'position' => 2,
            'is_active' => true
        ]);
        
        $produitsFrais = Category::create([
            'name' => 'Produits frais',
            'slug' => 'produits-frais',
            'description' => 'Produits laitiers, œufs, beurre...',
            'icon' => 'fas fa-egg',
            'parent_id' => $alimentaire->id,
            'position' => 3,
            'is_active' => true
        ]);
        
        $cereales = Category::create([
            'name' => 'Céréales',
            'slug' => 'cereales',
            'description' => 'Maïs, mil, sorgho et dérivés',
            'icon' => 'fas fa-seedling',
            'parent_id' => $alimentaire->id,
            'position' => 4,
            'is_active' => true
        ]);
        
        $huilesSucres = Category::create([
            'name' => 'Huiles & Sucres',
            'slug' => 'huiles-sucres',
            'description' => 'Huiles végétales, sucre, miel',
            'icon' => 'fas fa-oil-can',
            'parent_id' => $alimentaire->id,
            'position' => 5,
            'is_active' => true
        ]);
        
        $epices = Category::create([
            'name' => 'Épices & Condiments',
            'slug' => 'epices-condiments',
            'description' => 'Épices locales et internationales',
            'icon' => 'fas fa-pepper-hot',
            'parent_id' => $alimentaire->id,
            'position' => 6,
            'is_active' => true
        ]);
        
        // Sous-catégories COSMÉTIQUES
        $savonsCremes = Category::create([
            'name' => 'Savons & Crèmes',
            'slug' => 'savons-cremes',
            'description' => 'Savons artisanaux, crèmes hydratantes',
            'icon' => 'fas fa-soap',
            'parent_id' => $cosmetique->id,
            'position' => 1,
            'is_active' => true
        ]);
        
        $soinsCheveux = Category::create([
            'name' => 'Soins cheveux',
            'slug' => 'soins-cheveux',
            'description' => 'Shampoings, après-shampoings, huiles capillaires',
            'icon' => 'fas fa-hand-holding-heart',
            'parent_id' => $cosmetique->id,
            'position' => 2,
            'is_active' => true
        ]);
        
        $parfums = Category::create([
            'name' => 'Parfums',
            'slug' => 'parfums',
            'description' => 'Parfums de qualité pour hommes et femmes',
            'icon' => 'fas fa-perfume',
            'parent_id' => $cosmetique->id,
            'position' => 3,
            'is_active' => true
        ]);
        
        $soinsFemme = Category::create([
            'name' => 'Soins femme',
            'slug' => 'soins-femme',
            'description' => 'Produits spécialement conçus pour les femmes',
            'icon' => 'fas fa-venus',
            'parent_id' => $cosmetique->id,
            'position' => 4,
            'is_active' => true
        ]);
        
        $soinsHomme = Category::create([
            'name' => 'Soins homme',
            'slug' => 'soins-homme',
            'description' => 'Gamme masculine: rasage, soin du visage',
            'icon' => 'fas fa-mars',
            'parent_id' => $cosmetique->id,
            'position' => 5,
            'is_active' => true
        ]);
        
        $gelsHuiles = Category::create([
            'name' => 'Gels & Huiles',
            'slug' => 'gels-huiles',
            'description' => 'Gels douche, huiles corporelles',
            'icon' => 'fas fa-tint',
            'parent_id' => $cosmetique->id,
            'position' => 6,
            'is_active' => true
        ]);
        
        // Sous-catégories ÉLECTROMÉNAGER
        $petitElectro = Category::create([
            'name' => 'Petit électroménager',
            'slug' => 'petit-electromenager',
            'description' => 'Mixeurs, grille-pain, cafetières...',
            'icon' => 'fas fa-blender',
            'parent_id' => $electromenager->id,
            'position' => 1,
            'is_active' => true
        ]);
        
        $grosElectro = Category::create([
            'name' => 'Gros électroménager',
            'slug' => 'gros-electromenager',
            'description' => 'Réfrigérateurs, lave-linge, cuisinières...',
            'icon' => 'fas fa-冰箱',
            'parent_id' => $electromenager->id,
            'position' => 2,
            'is_active' => true
        ]);
        
        echo "✓ 3 volets et 16 sous-catégories créées\n";
        
       
        
        // ========== 4. UTILISATEUR ADMIN ==========
        echo "\n👤 Création de l'utilisateur admin...\n";
        
        // Dans InitialDataSeeder.php
        $admin = User::create([
            'name' => 'Administrateur',
            'email' => 'admin@mibaraka-house.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'is_admin' => true,  // Ajouter cette ligne
        ]);
        
        $manager = User::create([
            'name' => 'Gestionnaire Stock',
            'email' => 'stock@mibaraka-house.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'is_admin' => false,  // Ajouter cette ligne
        ]);
        
        echo "✓ Admin créé: admin@mibaraka-house.com / password123\n";
        echo "✓ Manager créé: stock@mibaraka-house.com / password123\n";
         // ========== ENTREPRISE ==========
        echo "\n🏢 Création des informations de l'entreprise...\n";

        $company = \App\Models\Company::create([
            'name' => 'Mibaraka House',
            'slogan' => 'Votre boutique de confiance en RDC',
            'email' => 'contact@mibaraka-house.com',
            'phone' => '0812345678',
            'whatsapp' => '0812345678',
            'address' => '12 avenue de la Révolution',
            'city' => 'Kinshasa',
            'country' => 'République Démocratique du Congo',
            'delivery_fee_standard' => 3.00,
            'delivery_fee_free_threshold' => 50.00,
            'opening_hours' => '08:00',
            'closing_hours' => '20:00',
            'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            'registration_number' => 'RCCM/CD/1234',
            'tax_number' => 'A1234567',
            'primary_color' => '#2563eb',
            'secondary_color' => '#7c3aed',
            'whatsapp_greeting' => 'Bonjour ! Merci de votre commande. Nous vous contacterons sous peu.',
            'whatsapp_notifications' => true,
        ]);

        echo "✓ Informations de l'entreprise créées\n";
        
        
        // ========== 10. RÉSUMÉ FINAL ==========
        echo "\n";
        echo "═══════════════════════════════════════════════════════════════════════\n";
        echo "✅ SEEDER TERMINÉ AVEC SUCCÈS !\n";
        echo "═══════════════════════════════════════════════════════════════════════\n";
        echo "📊 STATISTIQUES FINALES:\n";
        echo "   • Devises: " . Currency::count() . "\n";
        echo "   • Catégories: " . Category::count() . " (dont " . Category::whereNull('parent_id')->count() . " volets)\n";
        echo "   • Produits: " . Product::count() . "\n";
        echo "   • Clients: " . Customer::count() . "\n";
        echo "   • Commandes: " . Order::count() . "\n";
        echo "   • Lignes de commande: " . OrderItem::count() . "\n";
        echo "   • Utilisateurs: " . User::count() . "\n";
        echo "═══════════════════════════════════════════════════════════════════════\n";
        echo "\n🔑 ACCÈS ADMINISTRATION:\n";
        echo "   • Email: admin@mibaraka-house.com\n";
        echo "   • Mot de passe: password123\n";
        echo "\n🔑 ACCÈS MANAGER STOCK:\n";
        echo "   • Email: stock@mibaraka-house.com\n";
        echo "   • Mot de passe: password123\n";
        echo "\n💡 CONSEIL: Lancez 'php artisan serve' et connectez-vous pour tester.\n";
        echo "═══════════════════════════════════════════════════════════════════════\n";
    }
}