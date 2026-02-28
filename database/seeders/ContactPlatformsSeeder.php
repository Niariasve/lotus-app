<?php

namespace Database\Seeders;

use App\Models\ContactPlatform;
use Illuminate\Database\Seeder;

class ContactPlatformsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactPlatform::query()->upsert(
            [
                ['name' => 'Instagram', 'slug' => 'instagram', 'is_active' => true],
                ['name' => 'WhatsApp', 'slug' => 'whatsapp', 'is_active' => true],
                ['name' => 'Facebook', 'slug' => 'facebook', 'is_active' => true],
                ['name' => 'Telegram', 'slug' => 'telegram', 'is_active' => true],
            ],
            ['slug'],
            ['name', 'is_active']
        );
    }
}
