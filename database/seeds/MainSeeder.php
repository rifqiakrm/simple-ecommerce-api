<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Reward;
use App\Models\Role;
use App\User;
use Illuminate\Database\Seeder;

class MainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::transaction(function () {
            $this->generate();
        });
    }

    public function generate()
    {
        $user = User::create([
            'name' => 'User Biasa',
            'email' => 'user@gmail.com',
            'password' => Hash::make('12345678'),
            'balance' => 0,
            'points' => 0,
        ]);

        Role::create([
            'user_id' => $user->id,
            'role' => strtolower('user'),
        ]);
        $user = User::create([
            'name' => 'Merchant Maju Mundur',
            'email' => 'merchant@gmail.com',
            'password' => Hash::make('12345678'),
            'balance' => 0,
            'points' => 0,
        ]);

        Role::create([
            'user_id' => $user->id,
            'role' => strtolower('merchant'),
        ]);

        $ct = Category::create([
            'name' => 'Game',
        ]);

        $product = Product::create([
            'merchant_id' => $user->id,
            'code' => 'NAT001',
            'name' => 'Nier Automata',
            'image' => null,
            'price' => 262500,
        ]);

        ProductCategory::create([
            'product_id' => $product->id,
            'category_id' => $ct->id,
        ]);

        $product = Product::create([
            'merchant_id' => $user->id,
            'code' => 'SPD001',
            'name' => 'Spiderman',
            'image' => null,
            'price' => 126000,
        ]);

        ProductCategory::create([
            'product_id' => $product->id,
            'category_id' => $ct->id,
        ]);

        Category::create([
            'name' => 'Baju Muslim',
        ]);

        Reward::create([
            'name' => 'Reward A',
            'price' => 20,
        ]);

        Reward::create([
            'name' => 'Reward B',
            'price' => 40,
        ]);
    }
}
