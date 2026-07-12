<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Apple'       => Brand::firstOrCreate(['name' => 'Apple'], ['slug' => 'apple', 'is_active' => true]),
            'Samsung'     => Brand::firstOrCreate(['name' => 'Samsung'], ['slug' => 'samsung', 'is_active' => true]),
            'OnePlus'     => Brand::firstOrCreate(['name' => 'OnePlus'], ['slug' => 'oneplus', 'is_active' => true]),
            'Xiaomi'      => Brand::firstOrCreate(['name' => 'Xiaomi'], ['slug' => 'xiaomi', 'is_active' => true]),
            'Google'      => Brand::firstOrCreate(['name' => 'Google'], ['slug' => 'google', 'is_active' => true]),
            'Huawei'      => Brand::firstOrCreate(['name' => 'Huawei'], ['slug' => 'huawei', 'is_active' => true]),
            'Sony'        => Brand::firstOrCreate(['name' => 'Sony'], ['slug' => 'sony', 'is_active' => true]),
            'Nothing'     => Brand::firstOrCreate(['name' => 'Nothing'], ['slug' => 'nothing', 'is_active' => true]),
            'Oppo'        => Brand::firstOrCreate(['name' => 'Oppo'], ['slug' => 'oppo', 'is_active' => true]),
            'Vivo'        => Brand::firstOrCreate(['name' => 'Vivo'], ['slug' => 'vivo', 'is_active' => true]),
        ];

        $categories = [
            'iPhone'        => Category::firstOrCreate(['name' => 'iPhone', 'parent_id' => null], ['slug' => 'iphone', 'is_active' => true]),
            'Samsung'       => Category::firstOrCreate(['name' => 'Samsung', 'parent_id' => null], ['slug' => 'samsung', 'is_active' => true]),
            'Android'       => Category::firstOrCreate(['name' => 'Android', 'parent_id' => null], ['slug' => 'android', 'is_active' => true]),
            'Refurbished'   => Category::firstOrCreate(['name' => 'Refurbished', 'parent_id' => null], ['slug' => 'refurbished', 'is_active' => true]),
            'Accessories'   => Category::firstOrCreate(['name' => 'Accessories', 'parent_id' => null], ['slug' => 'accessories', 'is_active' => true]),
            'Tablets'       => Category::firstOrCreate(['name' => 'Tablets', 'parent_id' => null], ['slug' => 'tablets', 'is_active' => true]),
        ];

        $products = [
            // iPhones
            [
                'name' => 'iPhone 15 Pro Max 256GB',
                'sku' => 'APL-IP15PM-256',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $categories['iPhone']->id,
                'purchase_price' => 380000,
                'selling_price' => 449000,
                'wholesale_price' => 430000,
                'description' => 'Apple iPhone 15 Pro Max with A17 Pro chip, titanium design, 48MP camera system, and USB-C.',
            ],
            [
                'name' => 'iPhone 15 Pro 128GB',
                'sku' => 'APL-IP15P-128',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $categories['iPhone']->id,
                'purchase_price' => 310000,
                'selling_price' => 369000,
                'wholesale_price' => 350000,
                'description' => 'Apple iPhone 15 Pro with A17 Pro chip, titanium design, 48MP camera system.',
            ],
            [
                'name' => 'iPhone 15 128GB',
                'sku' => 'APL-IP15-128',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $categories['iPhone']->id,
                'purchase_price' => 255000,
                'selling_price' => 299000,
                'wholesale_price' => 285000,
                'description' => 'Apple iPhone 15 with A16 Bionic chip, Dynamic Island, 48MP main camera.',
            ],
            [
                'name' => 'iPhone 15 Plus 128GB',
                'sku' => 'APL-IP15P-128',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $categories['iPhone']->id,
                'purchase_price' => 275000,
                'selling_price' => 329000,
                'wholesale_price' => 310000,
                'description' => 'Apple iPhone 15 Plus with 6.7-inch display, A16 Bionic chip, 48MP camera.',
            ],
            [
                'name' => 'iPhone 14 128GB',
                'sku' => 'APL-IP14-128',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $categories['iPhone']->id,
                'purchase_price' => 210000,
                'selling_price' => 255000,
                'wholesale_price' => 240000,
                'description' => 'Apple iPhone 14 with A15 Bionic chip, 12MP dual camera system,车祸检测.',
            ],
            [
                'name' => 'iPhone 13 128GB',
                'sku' => 'APL-IP13-128',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $categories['iPhone']->id,
                'purchase_price' => 170000,
                'selling_price' => 210000,
                'wholesale_price' => 195000,
                'description' => 'Apple iPhone 13 with A15 Bionic chip, 12MP dual camera, Ceramic Shield.',
            ],
            [
                'name' => 'iPhone SE 2022 64GB',
                'sku' => 'APL-IPSE-64',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $categories['iPhone']->id,
                'purchase_price' => 120000,
                'selling_price' => 149000,
                'wholesale_price' => 140000,
                'description' => 'Apple iPhone SE 3rd generation with A15 Bionic chip, 5G, 4.7-inch Retina HD display.',
            ],

            // Samsung
            [
                'name' => 'Samsung Galaxy S24 Ultra 256GB',
                'sku' => 'SAM-S24U-256',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $categories['Samsung']->id,
                'purchase_price' => 350000,
                'selling_price' => 419000,
                'wholesale_price' => 400000,
                'description' => 'Samsung Galaxy S24 Ultra with Snapdragon 8 Gen 3, S Pen, 200MP camera, titanium frame.',
            ],
            [
                'name' => 'Samsung Galaxy S24+ 256GB',
                'sku' => 'SAM-S24P-256',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $categories['Samsung']->id,
                'purchase_price' => 270000,
                'selling_price' => 329000,
                'wholesale_price' => 310000,
                'description' => 'Samsung Galaxy S24+ with Exynos 2400, 50MP camera, 6.7-inch Dynamic AMOLED 2X.',
            ],
            [
                'name' => 'Samsung Galaxy S24 128GB',
                'sku' => 'SAM-S24-128',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $categories['Samsung']->id,
                'purchase_price' => 220000,
                'selling_price' => 269000,
                'wholesale_price' => 255000,
                'description' => 'Samsung Galaxy S24 with AI features, 50MP camera, 6.2-inch display.',
            ],
            [
                'name' => 'Samsung Galaxy A54 128GB',
                'sku' => 'SAM-A54-128',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $categories['Android']->id,
                'purchase_price' => 105000,
                'selling_price' => 135000,
                'wholesale_price' => 125000,
                'description' => 'Samsung Galaxy A54 with 50MP OIS camera, 5000mAh battery, IP67 water resistance.',
            ],
            [
                'name' => 'Samsung Galaxy A15 128GB',
                'sku' => 'SAM-A15-128',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $categories['Android']->id,
                'purchase_price' => 52000,
                'selling_price' => 69000,
                'wholesale_price' => 64000,
                'description' => 'Samsung Galaxy A15 with Super AMOLED display, 50MP camera, 5000mAh battery.',
            ],
            [
                'name' => 'Samsung Galaxy Z Flip5 256GB',
                'sku' => 'SAM-ZF5-256',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $categories['Samsung']->id,
                'purchase_price' => 260000,
                'selling_price' => 319000,
                'wholesale_price' => 300000,
                'description' => 'Samsung Galaxy Z Flip5 foldable with Flex Window, Snapdragon 8 Gen 2, 50MP camera.',
            ],

            // OnePlus
            [
                'name' => 'OnePlus 12 256GB',
                'sku' => 'OP-12-256',
                'brand_id' => $brands['OnePlus']->id,
                'category_id' => $categories['Android']->id,
                'purchase_price' => 210000,
                'selling_price' => 259000,
                'wholesale_price' => 245000,
                'description' => 'OnePlus 12 with Snapdragon 8 Gen 3, Hasselblad camera, 5400mAh battery, 100W SUPERVOOC.',
            ],
            [
                'name' => 'OnePlus Nord CE4 128GB',
                'sku' => 'OP-NCE4-128',
                'brand_id' => $brands['OnePlus']->id,
                'category_id' => $categories['Android']->id,
                'purchase_price' => 75000,
                'selling_price' => 95000,
                'wholesale_price' => 88000,
                'description' => 'OnePlus Nord CE4 with Snapdragon 7 Gen 3, 100W SUPERVOOC, 50MP Sony LYT-600 camera.',
            ],

            // Xiaomi
            [
                'name' => 'Xiaomi 14 Ultra 512GB',
                'sku' => 'XI-14U-512',
                'brand_id' => $brands['Xiaomi']->id,
                'category_id' => $categories['Android']->id,
                'purchase_price' => 290000,
                'selling_price' => 349000,
                'wholesale_price' => 330000,
                'description' => 'Xiaomi 14 Ultra with Leica optics, Snapdragon 8 Gen 3, 1-inch sensor camera.',
            ],
            [
                'name' => 'Xiaomi Redmi Note 13 Pro 256GB',
                'sku' => 'XI-RN13P-256',
                'brand_id' => $brands['Xiaomi']->id,
                'category_id' => $categories['Android']->id,
                'purchase_price' => 72000,
                'selling_price' => 92000,
                'wholesale_price' => 85000,
                'description' => 'Xiaomi Redmi Note 13 Pro with 200MP camera, 5100mAh battery, 6.67-inch AMOLED.',
            ],
            [
                'name' => 'Xiaomi Poco X6 Pro 256GB',
                'sku' => 'XI-PX6P-256',
                'brand_id' => $brands['Xiaomi']->id,
                'category_id' => $categories['Android']->id,
                'purchase_price' => 85000,
                'selling_price' => 109000,
                'wholesale_price' => 100000,
                'description' => 'Xiaomi Poco X6 Pro with Dimensity 8300 Ultra, 64MP OIS camera, 120Hz AMOLED.',
            ],

            // Google
            [
                'name' => 'Google Pixel 8 Pro 256GB',
                'sku' => 'GOO-P8P-256',
                'brand_id' => $brands['Google']->id,
                'category_id' => $categories['Android']->id,
                'purchase_price' => 240000,
                'selling_price' => 295000,
                'wholesale_price' => 280000,
                'description' => 'Google Pixel 8 Pro with Tensor G3, 50MP camera, 7 years of updates, AI features.',
            ],
            [
                'name' => 'Google Pixel 8a 128GB',
                'sku' => 'GOO-P8A-128',
                'brand_id' => $brands['Google']->id,
                'category_id' => $categories['Android']->id,
                'purchase_price' => 120000,
                'selling_price' => 155000,
                'wholesale_price' => 145000,
                'description' => 'Google Pixel 8a with Tensor G3, 64MP camera, 7 years of updates, compact design.',
            ],

            // Nothing
            [
                'name' => 'Nothing Phone (2a) 256GB',
                'sku' => 'NOT-2A-256',
                'brand_id' => $brands['Nothing']->id,
                'category_id' => $categories['Android']->id,
                'purchase_price' => 78000,
                'selling_price' => 99000,
                'wholesale_price' => 92000,
                'description' => 'Nothing Phone 2a with Glyph interface, Dimensity 7200 Pro, 50MP dual camera.',
            ],

            // Oppo
            [
                'name' => 'Oppo Find X7 Ultra 256GB',
                'sku' => 'OPP-FX7U-256',
                'brand_id' => $brands['Oppo']->id,
                'category_id' => $categories['Android']->id,
                'purchase_price' => 280000,
                'selling_price' => 339000,
                'wholesale_price' => 320000,
                'description' => 'Oppo Find X7 Ultra with Hasselblad camera, Snapdragon 8 Gen 3, 50MP dual periscope.',
            ],

            // Vivo
            [
                'name' => 'Vivo X100 Pro 256GB',
                'sku' => 'VIV-X100P-256',
                'brand_id' => $brands['Vivo']->id,
                'category_id' => $categories['Android']->id,
                'purchase_price' => 235000,
                'selling_price' => 289000,
                'wholesale_price' => 275000,
                'description' => 'Vivo X100 Pro with ZEISS APO camera, Dimensity 9300, 5400mAh battery.',
            ],

            // Refurbished iPhones
            [
                'name' => 'iPhone 14 Pro Max 256GB (Refurbished)',
                'sku' => 'APL-IP14PM-256-R',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $categories['Refurbished']->id,
                'purchase_price' => 220000,
                'selling_price' => 275000,
                'wholesale_price' => 260000,
                'description' => 'Refurbished iPhone 14 Pro Max in excellent condition. A16 Bionic, 48MP camera, Dynamic Island.',
                'is_serialized' => true,
            ],
            [
                'name' => 'iPhone 13 Pro 128GB (Refurbished)',
                'sku' => 'APL-IP13P-128-R',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $categories['Refurbished']->id,
                'purchase_price' => 155000,
                'selling_price' => 199000,
                'wholesale_price' => 185000,
                'description' => 'Refurbished iPhone 13 Pro in good condition. A15 Bionic, 12MP Pro camera, 120Hz display.',
                'is_serialized' => true,
            ],
            [
                'name' => 'Samsung Galaxy S23 Ultra 256GB (Refurbished)',
                'sku' => 'SAM-S23U-256-R',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $categories['Refurbished']->id,
                'purchase_price' => 190000,
                'selling_price' => 245000,
                'wholesale_price' => 230000,
                'description' => 'Refurbished Samsung Galaxy S23 Ultra. Snapdragon 8 Gen 2, S Pen, 200MP camera.',
                'is_serialized' => true,
            ],

            // Accessories
            [
                'name' => 'AirPods Pro 2nd Gen (USB-C)',
                'sku' => 'APL-APP2-USBC',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $categories['Accessories']->id,
                'purchase_price' => 42000,
                'selling_price' => 55000,
                'wholesale_price' => 50000,
                'description' => 'Apple AirPods Pro 2nd generation with USB-C, Adaptive Audio, Active Noise Cancellation.',
                'unit' => 'pcs',
            ],
            [
                'name' => 'Samsung Galaxy Buds3 Pro',
                'sku' => 'SAM-GB3P',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $categories['Accessories']->id,
                'purchase_price' => 32000,
                'selling_price' => 42000,
                'wholesale_price' => 38000,
                'description' => 'Samsung Galaxy Buds3 Pro with AI noise cancellation, Hi-Fi 360 Audio, IP57.',
                'unit' => 'pcs',
            ],
            [
                'name' => 'Spigen Tough Armor Case (iPhone 15 Pro Max)',
                'sku' => 'ACC-SA-IP15PM',
                'category_id' => $categories['Accessories']->id,
                'purchase_price' => 3500,
                'selling_price' => 5500,
                'wholesale_price' => 4800,
                'description' => 'Spigen Tough Armor dual-layer case with kickstand for iPhone 15 Pro Max.',
                'unit' => 'pcs',
            ],
            [
                'name' => 'Anker Nano II 65W USB-C Charger',
                'sku' => 'ACC-ANK-65W',
                'category_id' => $categories['Accessories']->id,
                'purchase_price' => 5500,
                'selling_price' => 8500,
                'wholesale_price' => 7500,
                'description' => 'Anker Nano II 65W USB-C fast charger with GaN technology, compact design.',
                'unit' => 'pcs',
            ],

            // Tablets
            [
                'name' => 'iPad 10th Gen 64GB Wi-Fi',
                'sku' => 'APL-IP10-64',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $categories['Tablets']->id,
                'purchase_price' => 140000,
                'selling_price' => 175000,
                'wholesale_price' => 165000,
                'description' => 'Apple iPad 10th generation with A14 Bionic chip, 10.9-inch Liquid Retina display.',
            ],
            [
                'name' => 'iPad Air M2 128GB Wi-Fi',
                'sku' => 'APL-IPA-M2-128',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $categories['Tablets']->id,
                'purchase_price' => 210000,
                'selling_price' => 259000,
                'wholesale_price' => 245000,
                'description' => 'Apple iPad Air with M2 chip, 11-inch Liquid Retina display, Touch ID.',
            ],
            [
                'name' => 'Samsung Galaxy Tab S9 FE 128GB',
                'sku' => 'SAM-TS9FE-128',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $categories['Tablets']->id,
                'purchase_price' => 110000,
                'selling_price' => 140000,
                'wholesale_price' => 130000,
                'description' => 'Samsung Galaxy Tab S9 FE with S Pen, 10.9-inch display, IP68 water resistance.',
            ],
        ];

        $count = 0;
        foreach ($products as $data) {
            // Remove invalid key if present
            unset($data['brand_id_temp']);

            $data['slug'] = Str::slug($data['name']);
            $data['is_active'] = true;
            $data['unit'] = $data['unit'] ?? 'pcs';
            $data['is_serialized'] = $data['is_serialized'] ?? false;

            Product::updateOrCreate(
                ['sku' => $data['sku']],
                $data
            );
            $count++;
        }

        $this->command->info("{$count} sample products seeded successfully.");
    }
}
