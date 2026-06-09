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
        
        // ========== 3. PRODUITS (Products) ==========
        echo "\n🛍️ Création des produits de test...\n";
        
        $products = [
            // ===== Riz & Farine (8 produits) =====
            [
                'name' => 'Riz Parfumé Thai - Sac 5kg',
                'description' => 'Riz thaïlandais de première qualité, long grain, très parfumé. Idéal pour les plats en sauce et les riz cantonais.',
                'price' => 12.50,
                'category_id' => $rizFarine->id,
                'stock_quantity' => 50,
                'track_stock' => true,
                'is_featured' => true,
                'unit' => 'sac',
                'weight' => 5.00
            ],
            [
                'name' => 'Farine de Blé T55 - 1kg',
                'description' => 'Farine de blé française T55, idéale pour le pain, les pâtisseries et les crêpes.',
                'price' => 2.80,
                'category_id' => $rizFarine->id,
                'stock_quantity' => 100,
                'track_stock' => true,
                'unit' => 'kg',
                'weight' => 1.00
            ],
            [
                'name' => 'Riz Local Congolais - 25kg',
                'description' => 'Riz cultivé dans la région du Bas-Congo. Un produit 100% local pour soutenir nos agriculteurs.',
                'price' => 28.00,
                'category_id' => $rizFarine->id,
                'stock_quantity' => 30,
                'track_stock' => true,
                'is_featured' => true,
                'unit' => 'sac',
                'weight' => 25.00
            ],
            [
                'name' => 'Farine de Maïs - 500g',
                'description' => 'Farine de maïs fine, idéale pour la préparation de la bouillie, du pain et des galettes.',
                'price' => 1.50,
                'category_id' => $rizFarine->id,
                'stock_quantity' => 75,
                'track_stock' => true,
                'unit' => 'sachet',
                'weight' => 0.50
            ],
            [
                'name' => 'Semoule de Blé - 500g',
                'description' => 'Semoule de blé fine, parfaite pour le couscous et les pâtisseries orientales.',
                'price' => 2.20,
                'category_id' => $rizFarine->id,
                'stock_quantity' => 60,
                'track_stock' => true,
                'unit' => 'sachet',
                'weight' => 0.50
            ],
            [
                'name' => 'Riz Basmati - 1kg',
                'description' => 'Riz basmati long grain, très parfumé. Idéal pour les plats indiens et orientaux.',
                'price' => 4.50,
                'category_id' => $rizFarine->id,
                'stock_quantity' => 40,
                'track_stock' => true,
                'unit' => 'kg',
                'weight' => 1.00
            ],
            [
                'name' => 'Farine de manioc - 1kg',
                'description' => 'Farine de manioc naturelle, utilisée pour la préparation du fufu et du chikwangue.',
                'price' => 3.00,
                'category_id' => $rizFarine->id,
                'stock_quantity' => 55,
                'track_stock' => true,
                'unit' => 'kg',
                'weight' => 1.00
            ],
            [
                'name' => 'Farine complète bio - 1kg',
                'description' => 'Farine de blé complète biologique, riche en fibres et nutriments.',
                'price' => 3.80,
                'category_id' => $rizFarine->id,
                'stock_quantity' => 35,
                'track_stock' => true,
                'unit' => 'kg',
                'weight' => 1.00
            ],
            
            // ===== Boissons (8 produits) =====
            [
                'name' => 'Jus d\'Ananas Maison - 1L',
                'description' => 'Jus d\'ananas frais, sans conservateurs, préparé artisanalement.',
                'price' => 3.50,
                'category_id' => $boissons->id,
                'stock_quantity' => 40,
                'track_stock' => true,
                'is_featured' => true,
                'unit' => 'bouteille',
                'weight' => 1.00
            ],
            [
                'name' => 'Eau Minérale - Pack 12 x 1.5L',
                'description' => 'Eau naturelle pure, conditionnée dans nos usines. Pack économique de 12 bouteilles de 1.5L.',
                'price' => 8.00,
                'category_id' => $boissons->id,
                'stock_quantity' => 200,
                'track_stock' => true,
                'unit' => 'pack',
                'weight' => 18.00
            ],
            [
                'name' => 'Thé Vert Menthe - 25 sachets',
                'description' => 'Thé vert à la menthe, idéal pour la digestion. 25 sachets individuels.',
                'price' => 4.20,
                'category_id' => $boissons->id,
                'stock_quantity' => 60,
                'track_stock' => true,
                'unit' => 'boîte',
                'weight' => 0.10
            ],
            [
                'name' => 'Coca-Cola - Pack 6 x 33cl',
                'description' => 'Pack de 6 canettes de Coca-Cola original, 33cl chacune.',
                'price' => 3.50,
                'category_id' => $boissons->id,
                'stock_quantity' => 120,
                'track_stock' => true,
                'unit' => 'pack',
                'weight' => 2.00
            ],
            [
                'name' => 'Jus de Mangue - 1L',
                'description' => 'Jus de mangue frais, 100% pur fruit, sans sucre ajouté.',
                'price' => 3.80,
                'category_id' => $boissons->id,
                'stock_quantity' => 45,
                'track_stock' => true,
                'unit' => 'bouteille',
                'weight' => 1.00
            ],
            [
                'name' => 'Café Moulu - 250g',
                'description' => 'Café arabica moulu, torréfaction moyenne, arômes intenses.',
                'price' => 6.00,
                'category_id' => $boissons->id,
                'stock_quantity' => 30,
                'track_stock' => true,
                'unit' => 'sachet',
                'weight' => 0.25
            ],
            [
                'name' => 'Chocolat Chaud - 200g',
                'description' => 'Poudre de cacao sucrée pour boisson chaude. Idéal pour le petit-déjeuner.',
                'price' => 4.50,
                'category_id' => $boissons->id,
                'stock_quantity' => 50,
                'track_stock' => true,
                'unit' => 'boîte',
                'weight' => 0.20
            ],
            [
                'name' => 'Bissap - 1L',
                'description' => 'Jus de bissap (hibiscus) traditionnel, préparé artisanalement. Rafraîchissant et bon pour la santé.',
                'price' => 4.00,
                'category_id' => $boissons->id,
                'stock_quantity' => 35,
                'track_stock' => true,
                'is_featured' => true,
                'unit' => 'bouteille',
                'weight' => 1.00
            ],
            
            // ===== Produits frais (8 produits) =====
            [
                'name' => 'Lait Frais Pasteurisé - 1L',
                'description' => 'Lait entier pasteurisé, riche en calcium. À consommer dans les 5 jours.',
                'price' => 2.50,
                'category_id' => $produitsFrais->id,
                'stock_quantity' => 30,
                'track_stock' => true,
                'unit' => 'brique',
                'weight' => 1.00
            ],
            [
                'name' => 'Beurre Doux - 250g',
                'description' => 'Beurre doux d\'origine française, idéal pour la cuisine et la pâtisserie.',
                'price' => 3.20,
                'category_id' => $produitsFrais->id,
                'stock_quantity' => 45,
                'track_stock' => true,
                'unit' => 'plaquette',
                'weight' => 0.25
            ],
            [
                'name' => 'Yaourt Nature - 4x125g',
                'description' => 'Pack de 4 yaourts nature, sans sucre ajouté. Probiotiques naturels.',
                'price' => 2.00,
                'category_id' => $produitsFrais->id,
                'stock_quantity' => 80,
                'track_stock' => true,
                'unit' => 'pack',
                'weight' => 0.50
            ],
            [
                'name' => 'Fromage Râpé - 200g',
                'description' => 'Mélange de fromages râpés (emmental, gruyère, parmesan).',
                'price' => 4.50,
                'category_id' => $produitsFrais->id,
                'stock_quantity' => 40,
                'track_stock' => true,
                'unit' => 'sachet',
                'weight' => 0.20
            ],
            [
                'name' => 'Crème Fraîche - 200ml',
                'description' => 'Crème fraîche épaisse, idéale pour les sauces et les desserts.',
                'price' => 2.80,
                'category_id' => $produitsFrais->id,
                'stock_quantity' => 35,
                'track_stock' => true,
                'unit' => 'pot',
                'weight' => 0.20
            ],
            [
                'name' => 'Œufs Frais - 6 unités',
                'description' => 'Œufs de poules élevées en plein air. Fraîcheur garantie.',
                'price' => 1.80,
                'category_id' => $produitsFrais->id,
                'stock_quantity' => 100,
                'track_stock' => true,
                'unit' => 'boîte',
                'weight' => 0.30
            ],
            [
                'name' => 'Jambon Blanc - 4 tranches',
                'description' => 'Jambon blanc supérieur, 4 tranches fines.',
                'price' => 3.50,
                'category_id' => $produitsFrais->id,
                'stock_quantity' => 25,
                'track_stock' => true,
                'unit' => 'barquette',
                'weight' => 0.10
            ],
            [
                'name' => 'Fromage de Chèvre - 150g',
                'description' => 'Fromage de chèvre frais, idéal en salade ou sur toast.',
                'price' => 5.00,
                'category_id' => $produitsFrais->id,
                'stock_quantity' => 20,
                'track_stock' => true,
                'unit' => 'barquette',
                'weight' => 0.15
            ],
            
            // ===== Huiles & Sucres (8 produits) =====
            [
                'name' => 'Huile d\'Olive Vierge Extra - 500ml',
                'description' => 'Huile d\'olive extra vierge, première pression à froid. Origine Méditerranée.',
                'price' => 7.50,
                'category_id' => $huilesSucres->id,
                'stock_quantity' => 35,
                'track_stock' => true,
                'is_featured' => true,
                'unit' => 'bouteille',
                'weight' => 0.50
            ],
            [
                'name' => 'Huile Végétale - 1L',
                'description' => 'Huile de soja et tournesol, idéale pour la friture et la cuisson.',
                'price' => 3.00,
                'category_id' => $huilesSucres->id,
                'stock_quantity' => 100,
                'track_stock' => true,
                'unit' => 'bouteille',
                'weight' => 1.00
            ],
            [
                'name' => 'Sucre Blanc - 1kg',
                'description' => 'Sucre cristallisé blanc, idéal pour boissons et pâtisseries.',
                'price' => 1.80,
                'category_id' => $huilesSucres->id,
                'stock_quantity' => 120,
                'track_stock' => true,
                'unit' => 'kg',
                'weight' => 1.00
            ],
            [
                'name' => 'Miel de Miellat - 500g',
                'description' => 'Miel 100% naturel, récolté dans les forêts du Congo. Saveur intense et boisée.',
                'price' => 9.00,
                'category_id' => $huilesSucres->id,
                'stock_quantity' => 20,
                'track_stock' => true,
                'is_featured' => true,
                'unit' => 'pot',
                'weight' => 0.50
            ],
            [
                'name' => 'Huile de Coco Vierge - 500ml',
                'description' => 'Huile de coco vierge pressée à froid, idéale pour la cuisine et les soins.',
                'price' => 8.50,
                'category_id' => $huilesSucres->id,
                'stock_quantity' => 25,
                'track_stock' => true,
                'unit' => 'bouteille',
                'weight' => 0.50
            ],
            [
                'name' => 'Sucre Roux - 500g',
                'description' => 'Sucre roux de canne, non raffiné, saveur caramélisée.',
                'price' => 2.20,
                'category_id' => $huilesSucres->id,
                'stock_quantity' => 60,
                'track_stock' => true,
                'unit' => 'sachet',
                'weight' => 0.50
            ],
            [
                'name' => 'Vinaigre Balsamique - 250ml',
                'description' => 'Vinaigre balsamique de Modène, vieilli en fût de chêne.',
                'price' => 5.50,
                'category_id' => $huilesSucres->id,
                'stock_quantity' => 30,
                'track_stock' => true,
                'unit' => 'bouteille',
                'weight' => 0.25
            ],
            [
                'name' => 'Huile de Palme Rouge - 1L',
                'description' => 'Huile de palme artisanale, riche en vitamine A. Ingrédient traditionnel de la cuisine congolaise.',
                'price' => 4.00,
                'category_id' => $huilesSucres->id,
                'stock_quantity' => 50,
                'track_stock' => true,
                'unit' => 'bouteille',
                'weight' => 1.00
            ],
            
            // ===== COSMÉTIQUES =====
            // Savons & Crèmes (8 produits)
            [
                'name' => 'Savon Artisanal au Karité - 100g',
                'description' => 'Savon naturel à base de beurre de karité. Hydrate et adoucit la peau.',
                'price' => 3.50,
                'category_id' => $savonsCremes->id,
                'stock_quantity' => 80,
                'track_stock' => true,
                'is_featured' => true,
                'unit' => 'pièce',
                'weight' => 0.10
            ],
            [
                'name' => 'Crème Hydratante Corps - 500ml',
                'description' => 'Crème hydratante à l\'aloe vera et au beurre de karité. Non grasse, pénétration rapide.',
                'price' => 12.00,
                'category_id' => $savonsCremes->id,
                'stock_quantity' => 40,
                'track_stock' => true,
                'unit' => 'pot',
                'weight' => 0.50
            ],
            [
                'name' => 'Savon Désinfectant - 250ml',
                'description' => 'Savon liquide antibactérien, idéal pour les mains. En format pompe.',
                'price' => 4.00,
                'category_id' => $savonsCremes->id,
                'stock_quantity' => 100,
                'track_stock' => true,
                'unit' => 'flacon',
                'weight' => 0.25
            ],
            [
                'name' => 'Gommage Corps - 200g',
                'description' => 'Gommage corporel à base de sucre et d\'huile de coco. Élimine les peaux mortes.',
                'price' => 8.00,
                'category_id' => $savonsCremes->id,
                'stock_quantity' => 30,
                'track_stock' => true,
                'unit' => 'pot',
                'weight' => 0.20
            ],
            [
                'name' => 'Beurre de Karité Pur - 200g',
                'description' => 'Beurre de karité 100% pur, non raffiné. Idéal pour les soins intensifs.',
                'price' => 10.00,
                'category_id' => $savonsCremes->id,
                'stock_quantity' => 25,
                'track_stock' => true,
                'unit' => 'pot',
                'weight' => 0.20
            ],
            [
                'name' => 'Lait Corps Coco - 250ml',
                'description' => 'Lait corporel à la noix de coco, hydratation légère et parfum exotique.',
                'price' => 7.50,
                'category_id' => $savonsCremes->id,
                'stock_quantity' => 35,
                'track_stock' => true,
                'unit' => 'flacon',
                'weight' => 0.25
            ],
            [
                'name' => 'Savon Noir - 200g',
                'description' => 'Savon noir traditionnel à base d\'huile d\'olive. Nettoyage en profondeur.',
                'price' => 6.00,
                'category_id' => $savonsCremes->id,
                'stock_quantity' => 45,
                'track_stock' => true,
                'unit' => 'pot',
                'weight' => 0.20
            ],
            [
                'name' => 'Crème Anti-âge - 50ml',
                'description' => 'Crème de jour anti-âge à l\'acide hyaluronique et à la vitamine C.',
                'price' => 25.00,
                'category_id' => $savonsCremes->id,
                'stock_quantity' => 15,
                'track_stock' => true,
                'is_featured' => true,
                'unit' => 'pot',
                'weight' => 0.05
            ],
            
            // Soins cheveux (8 produits)
            [
                'name' => 'Shampoing Hydratation - 500ml',
                'description' => 'Shampoing doux pour cheveux secs et fragiles. Enrichi en huile d\'argan.',
                'price' => 9.00,
                'category_id' => $soinsCheveux->id,
                'stock_quantity' => 45,
                'track_stock' => true,
                'is_featured' => true,
                'unit' => 'flacon',
                'weight' => 0.50
            ],
            [
                'name' => 'Huile Capillaire Coco - 200ml',
                'description' => 'Huile de coco vierge, idéale pour nourrir et faire briller les cheveux.',
                'price' => 7.00,
                'category_id' => $soinsCheveux->id,
                'stock_quantity' => 30,
                'track_stock' => true,
                'unit' => 'flacon',
                'weight' => 0.20
            ],
            [
                'name' => 'Soin Sans Rinçage - 250ml',
                'description' => 'Soin protecteur sans rinçage, démêle et nourrit les cheveux bouclés.',
                'price' => 10.50,
                'category_id' => $soinsCheveux->id,
                'stock_quantity' => 25,
                'track_stock' => true,
                'unit' => 'flacon',
                'weight' => 0.25
            ],
            [
                'name' => 'Masque Capillaire - 300g',
                'description' => 'Masque nourrissant à la kératine, répare les cheveux abîmés.',
                'price' => 12.00,
                'category_id' => $soinsCheveux->id,
                'stock_quantity' => 20,
                'track_stock' => true,
                'unit' => 'pot',
                'weight' => 0.30
            ],
            [
                'name' => 'Shampoing Sec - 200ml',
                'description' => 'Shampoing sec pour cheveux, rafraîchit sans eau.',
                'price' => 8.00,
                'category_id' => $soinsCheveux->id,
                'stock_quantity' => 40,
                'track_stock' => true,
                'unit' => 'flacon',
                'weight' => 0.20
            ],
            [
                'name' => 'Sérum Brillance - 100ml',
                'description' => 'Sérum à l\'huile d\'argan, apporte brillance et douceur.',
                'price' => 11.00,
                'category_id' => $soinsCheveux->id,
                'stock_quantity' => 35,
                'track_stock' => true,
                'unit' => 'flacon',
                'weight' => 0.10
            ],
            [
                'name' => 'Lotion Anti-chute - 200ml',
                'description' => 'Lotion fortifiante contre la chute des cheveux. Base de plantes.',
                'price' => 15.00,
                'category_id' => $soinsCheveux->id,
                'stock_quantity' => 18,
                'track_stock' => true,
                'unit' => 'flacon',
                'weight' => 0.20
            ],
            [
                'name' => 'Crème Coiffante - 150ml',
                'description' => 'Crème coiffante à l\'aloe vera, maintien naturel sans durcir.',
                'price' => 6.50,
                'category_id' => $soinsCheveux->id,
                'stock_quantity' => 50,
                'track_stock' => true,
                'unit' => 'pot',
                'weight' => 0.15
            ],
            
            // Parfums (8 produits)
            [
                'name' => 'Parfum Homme Océan - 100ml',
                'description' => 'Fragrance marine et boisée. Notes de bergamote, sel marin et cèdre.',
                'price' => 35.00,
                'category_id' => $parfums->id,
                'stock_quantity' => 20,
                'track_stock' => true,
                'unit' => 'flacon',
                'weight' => 0.30
            ],
            [
                'name' => 'Parfum Femme Fleur de Lotus - 100ml',
                'description' => 'Parfum floral doux et féminin. Notes de lotus, jasmin et vanille.',
                'price' => 38.00,
                'category_id' => $parfums->id,
                'stock_quantity' => 18,
                'track_stock' => true,
                'is_featured' => true,
                'unit' => 'flacon',
                'weight' => 0.30
            ],
            [
                'name' => 'Eau de Toilette Mixte - 50ml',
                'description' => 'Fragrance unisexe aux notes d\'agrumes et de bois blanc.',
                'price' => 22.00,
                'category_id' => $parfums->id,
                'stock_quantity' => 25,
                'track_stock' => true,
                'unit' => 'flacon',
                'weight' => 0.15
            ],
            [
                'name' => 'Parfum Vanille - 50ml',
                'description' => 'Parfum gourmand à la vanille de Madagascar.',
                'price' => 28.00,
                'category_id' => $parfums->id,
                'stock_quantity' => 15,
                'track_stock' => true,
                'unit' => 'flacon',
                'weight' => 0.15
            ],
            [
                'name' => 'Parfum Homme Cuir - 100ml',
                'description' => 'Fragrance cuir et tabac. Notes de cuir, tabac blond et épices.',
                'price' => 42.00,
                'category_id' => $parfums->id,
                'stock_quantity' => 12,
                'track_stock' => true,
                'unit' => 'flacon',
                'weight' => 0.30
            ],
            [
                'name' => 'Parfum Femme Rose - 100ml',
                'description' => 'Parfum floral à la rose de Damas. Notes de rose, pivoine et musc.',
                'price' => 45.00,
                'category_id' => $parfums->id,
                'stock_quantity' => 10,
                'track_stock' => true,
                'unit' => 'flacon',
                'weight' => 0.30
            ],
            [
                'name' => 'Parfum Fruité - 50ml',
                'description' => 'Parfum fruité aux notes de framboise, grenade et fleur d\'oranger.',
                'price' => 25.00,
                'category_id' => $parfums->id,
                'stock_quantity' => 22,
                'track_stock' => true,
                'unit' => 'flacon',
                'weight' => 0.15
            ],
            [
                'name' => 'Déodorant - 50ml',
                'description' => 'Déodorant sans sel d\'aluminium. Protection 24h.',
                'price' => 6.00,
                'category_id' => $parfums->id,
                'stock_quantity' => 60,
                'track_stock' => true,
                'unit' => 'roll-on',
                'weight' => 0.05
            ],
            
            // ÉLECTROMÉNAGER (6 produits)
            [
                'name' => 'Mixeur Plongeant - 800W',
                'description' => 'Mixeur plongeant puissant, idéal pour les soupes et purées. Livré avec accessoires.',
                'price' => 45.00,
                'category_id' => $petitElectro->id,
                'stock_quantity' => 10,
                'track_stock' => true,
                'unit' => 'pièce',
                'weight' => 1.20
            ],
            [
                'name' => 'Grille-Pain 2 tranches',
                'description' => 'Grille-pain design, 6 niveaux de chauffe, fonction décongélation.',
                'price' => 35.00,
                'category_id' => $petitElectro->id,
                'stock_quantity' => 8,
                'track_stock' => true,
                'unit' => 'pièce',
                'weight' => 1.00
            ],
            [
                'name' => 'Cafetière Filtre - 1L',
                'description' => 'Cafetière électrique 10 tasses, maintien au chaud, carafe en verre.',
                'price' => 55.00,
                'category_id' => $petitElectro->id,
                'stock_quantity' => 6,
                'track_stock' => true,
                'unit' => 'pièce',
                'weight' => 2.00
            ],
            [
                'name' => 'Réfrigérateur - 200L',
                'description' => 'Réfrigérateur compact, classe A+, 200L, freezer intégré.',
                'price' => 350.00,
                'category_id' => $grosElectro->id,
                'stock_quantity' => 5,
                'track_stock' => true,
                'unit' => 'pièce',
                'weight' => 45.00
            ],
            [
                'name' => 'Machine à Laver - 7kg',
                'description' => 'Lave-linge hublot, 7kg, 1200 tours/min, classe A+++',
                'price' => 420.00,
                'category_id' => $grosElectro->id,
                'stock_quantity' => 4,
                'track_stock' => true,
                'unit' => 'pièce',
                'weight' => 65.00
            ],
            [
                'name' => 'Cuisinière - 4 feux',
                'description' => 'Cuisinière gaz, 4 feux, four électrique, sécurité enfants.',
                'price' => 380.00,
                'category_id' => $grosElectro->id,
                'stock_quantity' => 3,
                'track_stock' => true,
                'unit' => 'pièce',
                'weight' => 50.00
            ]
        ];
        
        // Insertion des produits avec gestion des images fictives
        foreach ($products as $index => $productData) {
            // Simuler des images pour les tests (à remplacer par de vraies images plus tard)
            $productData['image_path'] = 'products/placeholder-' . ($index % 5 + 1) . '.jpg';
            Product::create($productData);
        }
        
        $totalProducts = count($products);
        echo "✓ {$totalProducts} produits créés\n";
        
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
        // ========== 5. CLIENTS (Customers) ==========
        echo "\n👥 Création des clients de test...\n";
        
        $customers = [
            [
                'name' => 'Jean-Paul Kabila',
                'phone' => '0812345678',
                'email' => 'jeanpaul.kabila@gmail.com',
                'default_address' => '12 avenue de la Révolution',
                'city' => 'Kinshasa',
                'neighborhood' => 'Gombe',
                'preferred_currency' => 'USD'
            ],
            [
                'name' => 'Marie-Claire Mbuyi',
                'phone' => '0898765432',
                'email' => 'marieclaire.mbuyi@yahoo.fr',
                'default_address' => '45 boulevard du 30 Juin',
                'city' => 'Kinshasa',
                'neighborhood' => 'Limete',
                'preferred_currency' => 'CDF'
            ],
            [
                'name' => 'Patient Tshisekedi',
                'phone' => '0855566778',
                'email' => 'patient.tshisekedi@gmail.com',
                'default_address' => '78 avenue Kasa-Vubu',
                'city' => 'Lubumbashi',
                'neighborhood' => 'Bel-Air',
                'preferred_currency' => 'USD'
            ],
            [
                'name' => 'Grace Ngalula',
                'phone' => '0822334455',
                'email' => 'grace.ngalula@outlook.com',
                'default_address' => '23 avenue du Commerce',
                'city' => 'Kinshasa',
                'neighborhood' => 'Ngaliema',
                'preferred_currency' => 'CDF'
            ],
            [
                'name' => 'Michel Lumbu',
                'phone' => '0977889900',
                'email' => 'michel.lumbu@gmail.com',
                'default_address' => '56 avenue Wangata',
                'city' => 'Kinshasa',
                'neighborhood' => 'Barumbu',
                'preferred_currency' => 'USD'
            ],
            [
                'name' => 'Fatou Diallo',
                'phone' => '0811122233',
                'email' => 'fatou.diallo@yahoo.com',
                'default_address' => '89 avenue des Aviateurs',
                'city' => 'Kisangani',
                'neighborhood' => 'Lubunga',
                'preferred_currency' => 'USD'
            ],
            [
                'name' => 'Olivier Kabongo',
                'phone' => '0844455667',
                'email' => 'olivier.kabongo@gmail.com',
                'default_address' => '34 avenue de l\'Église',
                'city' => 'Mbuji-Mayi',
                'neighborhood' => 'Dibindi',
                'preferred_currency' => 'CDF'
            ],
            [
                'name' => 'Valentine Kapinga',
                'phone' => '0899988877',
                'email' => 'valentine.kapinga@outlook.com',
                'default_address' => '12 avenue Sendwe',
                'city' => 'Lubumbashi',
                'neighborhood' => 'Golf',
                'preferred_currency' => 'USD'
            ]
        ];
        
        $customerModels = [];
        foreach ($customers as $customerData) {
            $customerModels[] = Customer::create(array_merge($customerData, [
                'total_orders' => 0,
                'total_spent' => 0,
                'is_active' => true
            ]));
        }
        
        echo "✓ " . count($customerModels) . " clients créés\n";
        
        // ========== 6. COMMANDES (Orders) avec historique ==========
        echo "\n📦 Création des commandes avec historique...\n";
        
        $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid'];
        $paymentMethods = ['cash', 'mobile_money', 'bank_transfer'];
        $productsList = Product::all();
        
        $orderCount = 0;
        $orderItemCount = 0;
        
        // Créer 50 commandes réparties sur les 6 derniers mois
        for ($i = 0; $i < 50; $i++) {
            $customer = $customerModels[array_rand($customerModels)];
            $currency = rand(1, 10) > 2 ? $usd : $cdf; // 80% USD, 20% CDF
            $status = $statuses[array_rand($statuses)];
            $paymentStatus = $status === 'cancelled' ? 'pending' : $paymentStatuses[array_rand($paymentStatuses)];
            
            // Date aléatoire sur les 6 derniers mois
            $randomDays = rand(0, 180);
            $orderDate = Carbon::now()->subDays($randomDays);
            
            $order = Order::create([
                'order_number' => 'MB-' . $orderDate->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
                'customer_id' => $customer->id,
                'user_id' => rand(1, 10) > 7 ? $admin->id : null,
                'status' => $status,
                'delivery_fee' => rand(0, 5) > 3 ? 0 : rand(2, 8),
                'currency_code' => $currency->code,
                'currency_id' => $currency->id,
                'exchange_rate' => $currency->rate,
                'customer_name' => $customer->name,
                'customer_phone' => $customer->phone,
                'customer_email' => $customer->email,
                'delivery_address' => $customer->default_address,
                'delivery_city' => $customer->city,
                'delivery_neighborhood' => $customer->neighborhood,
                'delivery_notes' => rand(1, 10) > 8 ? 'Sonner à la porte, 2ème étage' : null,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_status' => $paymentStatus,
                'whatsapp_sent' => rand(1, 10) > 3,
                'whatsapp_sent_at' => rand(1, 10) > 5 ? $orderDate->addHours(rand(1, 24)) : null,
                'delivered_at' => $status === 'delivered' ? $orderDate->addDays(rand(1, 5)) : null,
                'admin_notes' => rand(1, 20) > 18 ? 'Client VIP - Livraison prioritaire' : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate
            ]);
            
            // Ajouter entre 1 et 5 produits à la commande
            $numItems = rand(1, 5);
            $orderSubtotal = 0;
            $selectedProducts = $productsList->random($numItems);
            
            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $subtotal = $product->price * $quantity;
                $orderSubtotal += $subtotal;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->image_path,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate
                ]);
                
                $orderItemCount++;
            }
            
            $order->subtotal = $orderSubtotal;
            $order->total_amount = $orderSubtotal + $order->delivery_fee;
            $order->save();
            
            $orderCount++;
        }
        
        echo "✓ {$orderCount} commandes créées\n";
        echo "✓ {$orderItemCount} lignes de commande créées\n";
        
        // ========== 7. METTRE À JOUR LES STATISTIQUES CLIENTS ==========
        echo "\n📊 Mise à jour des statistiques clients...\n";
        
        foreach ($customerModels as $customer) {
            $customer->updateStats();
        }
        
        echo "✓ Statistiques clients mises à jour\n";
        
        // ========== 8. PRODUITS AVEC STOCK BAS (pour alerte) ==========
        echo "\n⚠️ Création de produits avec stock bas pour alerte...\n";
        
        // Quelques produits avec stock très bas
        $lowStockProducts = Product::inRandomOrder()->limit(5)->get();
        foreach ($lowStockProducts as $product) {
            $product->update([
                'stock_quantity' => rand(1, 4),
                'track_stock' => true,
                'stock_alert_threshold' => 5
            ]);
        }
        
        echo "✓ " . $lowStockProducts->count() . " produits en stock bas créés\n";
        
        // ========== 9. PRODUITS EN RUPTURE ==========
        echo "\n❌ Création de produits en rupture pour alerte...\n";
        
        $outOfStockProducts = Product::inRandomOrder()->limit(3)->get();
        foreach ($outOfStockProducts as $product) {
            $product->update([
                'stock_quantity' => 0,
                'track_stock' => true,
                'stock_alert_threshold' => 5
            ]);
        }
        
        echo "✓ " . $outOfStockProducts->count() . " produits en rupture créés\n";
        
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