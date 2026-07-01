<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreDetail;
use App\Models\MenuCategory;
use App\Models\MenuItem;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = StoreDetail::where('is_active', true)->get();

        if ($stores->isEmpty()) {
            return;
        }

        $menuData = [
            [
                'category' => 'APPETIZERS',
                'items' => [
                    ['name' => 'PUFF PUFF - EA', 'price' => 0.99, 'description' => 'Sweet, deep-fried dough balls, soft and airy.'],
                    ['name' => 'BUNS - EA', 'price' => 0.99, 'description' => 'Crispy on the outside, dense and sweet on the inside.'],
                    ['name' => 'MEAT PIE - EA', 'price' => 2.59, 'description' => 'Flaky pastry filled with seasoned minced beef and potatoes.'],
                    ['name' => 'BEEF SUYA', 'price' => 7.99, 'description' => 'Grilled beef mixed with our signature spicy suya spices.'],
                ]
            ],
            [
                'category' => 'SOUP',
                'items' => [
                    ['name' => 'PLATE OF EGUSI SOUP', 'price' => 15.99, 'description' => 'Cooked melon seeds, seasoned soup base, added spices, served with assorted meat.'],
                    ['name' => 'PLATE OF OGBONNO SOUP', 'price' => 15.99, 'description' => 'Cooked ground ogbono, seasoned soup base, served with assorted meat.'],
                    ['name' => 'PLATE OF OKRO SOUP', 'price' => 15.99, 'description' => 'Cooked cut okra, seasoned spicy soup base, served with assorted meat.'],
                ]
            ],
            [
                'category' => 'STEW',
                'items' => [
                    ['name' => 'PLATE OF RED STEW', 'price' => 7.99, 'description' => 'Fried tomato seasoned sauce blended with red peppers and beef broth.'],
                    ['name' => 'PLATE OF ASSORTED STEW', 'price' => 14.99, 'description' => 'Seasoned tomato blend with red peppers and assorted meat broth.'],
                ]
            ],
            [
                'category' => 'ENTREES',
                'items' => [
                    ['name' => 'PLATE OF JOLLOF RICE', 'price' => 7.49, 'description' => 'Rice mixed with tomato base sauce, served with choice of meat.'],
                    ['name' => 'PLATE OF FRIED RICE', 'price' => 7.49, 'description' => 'Seasoned stir-fried rice with vegetables and spices.'],
                    ['name' => 'BAKED FISH/PLANTAIN', 'price' => 14.99, 'description' => 'Grilled seasoned tilapia fish paired with sweet fried plantains.'],
                ]
            ],
            [
                'category' => 'SIDE',
                'items' => [
                    ['name' => 'FRIED PLANTAIN (8PC)', 'price' => 2.00, 'description' => 'Sweet, golden-fried ripe plantain slices.'],
                    ['name' => 'AGEGE BREAD BIG', 'price' => 3.89, 'description' => 'Soft, sweet, and stretchy local Nigerian bread.'],
                    ['name' => 'FUFU (POUNDED YAM)', 'price' => 2.00, 'description' => 'Smooth, dough-like side dish made of pounded yam.'],
                ]
            ]
        ];

        foreach ($stores as $store) {
            foreach ($menuData as $data) {
                // Create Menu Category for this store
                $category = MenuCategory::create([
                    'store_id' => $store->id,
                    'name' => $data['category'],
                    'description' => 'Freshly prepared ' . strtolower($data['category']) . ' menu.',
                    'is_active' => true,
                ]);

                // Create Menu Items under this category
                foreach ($data['items'] as $item) {
                    MenuItem::create([
                        'store_id' => $store->id,
                        'menu_category_id' => $category->id,
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
