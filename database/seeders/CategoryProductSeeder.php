<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class CategoryProductSeeder extends Seeder
{
    /**
     * Jalankan seeder kategori dan produk kopi.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Espresso Based',
                'description' => 'Minuman kopi yang dibuat dari ekstraksi biji kopi dengan mesin espresso.'
            ],
            [
                'name' => 'Manual Brew',
                'description' => 'Kopi diseduh manual seperti V60, Chemex, Kalita, untuk rasa yang lebih bersih dan kompleks.'
            ],
            [
                'name' => 'Cold Coffee',
                'description' => 'Kopi dingin seperti cold brew atau iced coffee, cocok untuk cuaca panas.'
            ],
            [
                'name' => 'Signature Drink',
                'description' => 'Kreasi minuman kopi khas yang unik dan hanya tersedia di toko ini.'
            ],
            [
                'name' => 'Non-Coffee',
                'description' => 'Pilihan minuman selain kopi, cocok bagi penikmat teh, cokelat, atau susu.'
            ],
        ];

        $products = [
            // Espresso Based
            ['Espresso Single Shot', 'Espresso murni dengan rasa pekat dan konsentrasi tinggi.', 18000],
            ['Cappuccino', 'Perpaduan espresso, steamed milk, dan foam yang seimbang.', 25000],
            ['Caramel Latte', 'Latte lembut dengan sentuhan manis dari caramel.', 28000],

            // Manual Brew
            ['V60 Arabica Aceh Gayo', 'Manual brew dengan biji arabika Gayo yang fruity dan floral.', 27000],
            ['Japanese Iced Pour Over', 'Kopi dingin hasil seduhan V60 dengan metode Japanese style.', 29000],
            ['Kalita Toraja Sapan', 'Seduhan Kalita dengan rasa earthy dan aftertaste cokelat.', 27000],

            // Cold Coffee
            ['Classic Cold Brew', 'Cold brew 18 jam dengan rasa smooth dan rendah asam.', 30000],
            ['Iced Americano', 'Espresso yang disajikan dingin dengan air es.', 23000],
            ['Creamy Cold Latte', 'Cold brew dengan tambahan susu creamy dan es batu.', 32000],

            // Signature Drink
            ['Kopi Susu Gula Aren', 'Signature drink dengan espresso, susu segar, dan gula aren alami.', 25000],
            ['Hazelnut Cloud', 'Minuman kopi lembut dengan foam hazelnut yang khas.', 28000],
            ['Mocha Mint Blast', 'Kopi mocha dengan rasa mint segar dan cokelat.', 29000],

            // Non-Coffee
            ['Matcha Latte', 'Minuman matcha Jepang dengan susu, manis dan menenangkan.', 27000],
            ['Dark Chocolate', 'Cokelat panas pekat untuk pencinta rasa bold.', 26000],
            ['Red Velvet Latte', 'Minuman manis creamy dengan warna merah lembut.', 27000],
        ];

        foreach ($categories as $i => $categoryData) {
            $category = Category::create($categoryData);

            // Tambahkan 3 produk untuk setiap kategori
            for ($j = 0; $j < 3; $j++) {
                $index = ($i * 3) + $j;
                Product::create([
                    'name' => $products[$index][0],
                    'description' => $products[$index][1],
                    'price' => $products[$index][2],
                    'category_id' => $category->id,
                    'is_available' => true,
                    'image' => strtolower(str_replace(' ', '_', $products[$index][0])) . '.jpg',
                ]);
            }
        }
    }
}
