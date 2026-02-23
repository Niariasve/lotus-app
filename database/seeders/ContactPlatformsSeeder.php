<?php

namespace Database\Seeders;

use App\Models\ContactPlatform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactPlatformsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactPlatform::insert([
            ['name' => 'Instagram', 'slug' => 'instagram'],
            ['name' => 'WhatsApp', 'slug' => 'whatsapp'],
            ['name' => 'Facebook', 'slug' => 'facebook'],
            ['name' => 'Telegram', 'slug' => 'telegram'],
        ]);
    }
}
