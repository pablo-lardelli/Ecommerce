<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    //use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Storage::deleteDirectory('products');
        Storage::makeDirectory('products');

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Pablo',
            'last_name' => 'Lardelli',
            'document_type' => '1',
            'document_number' => '37114811',
            'email' => 'pablo@mail.com',
            'phone' => '987654321',
            'password' => bcrypt('12345678')
        ]);

        User::factory(10)->create();

        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,

            FamilySeeder::class,
            OptionSeeder::class
        ]);

        Product::factory(100)->create();
    }
}
