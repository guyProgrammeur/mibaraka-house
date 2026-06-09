<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InitialDataSeederInit extends Seeder
{
    /**
     * Exécute les seeders pour la production
     * Données minimales et essentielles uniquement
     */
    public function run(): void
    {
        echo "\n═══════════════════════════════════════════════════════════\n";
        echo "🌱 SEEDING DES DONNÉES MINIMALES POUR PRODUCTION\n";
        echo "═══════════════════════════════════════════════════════════\n\n";

        // ========== 1. DEVISES ==========
        echo "📌 1/4 - Création des devises...\n";
        
        Currency::create([
            'code' => 'USD',
            'symbol' => '$',
            'name' => 'Dollar américain',
            'rate' => 1.0000,
            'is_default' => true,
            'is_active' => true
        ]);
        
        Currency::create([
            'code' => 'CDF',
            'symbol' => 'FC',
            'name' => 'Franc congolais',
            'rate' => 2850.0000,
            'is_default' => false,
            'is_active' => true
        ]);
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
        
        echo "   ✓ 2 devises créées (USD, CDF)\n\n";

        // ========== 2. CATÉGORIES PRINCIPALES ==========
        echo "📌 2/4 - Création des catégories...\n";
        
        // Volets principaux uniquement (sans sous-catégories)
        Category::create([
            'name' => 'Produits Alimentaires',
            'slug' => 'produits-alimentaires',
            'description' => 'Tous nos produits alimentaires',
            'position' => 1,
            'is_active' => true
        ]);
        
        Category::create([
            'name' => 'Produits Cosmétiques',
            'slug' => 'produits-cosmetiques',
            'description' => 'Soins et beauté',
            'position' => 2,
            'is_active' => true
        ]);
        
        echo "   ✓ 2 catégories principales créées\n\n";

        // ========== 3. UTILISATEUR ADMIN ==========
        echo "📌 3/4 - Création de l'utilisateur admin...\n";
        
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@mibaraka-house.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        
        echo "   ✓ Admin créé: admin@mibaraka-house.com / password123\n\n";

        // ========== 4. RÉSUMÉ ==========
        echo "═══════════════════════════════════════════════════════════\n";
        echo "✅ SEEDING TERMINÉ\n";
        echo "═══════════════════════════════════════════════════════════\n";
        echo "📊 DONNÉES CRÉÉES:\n";
        echo "   • Devises: 2\n";
        echo "   • Catégories: 2\n";
        echo "   • Utilisateur admin: 1\n";
        echo "═══════════════════════════════════════════════════════════\n";
        echo "🔑 ACCÈS ADMINISTRATION:\n";
        echo "   • Email: admin@mibaraka-house.com\n";
        echo "   • Mot de passe: password123\n";
        echo "═══════════════════════════════════════════════════════════\n\n";
    }

}