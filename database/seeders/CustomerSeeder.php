<?php

namespace Database\Seeders;

use App\Models\ContactPlatform;
use App\Models\Customer;
use App\Models\CustomerContact;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        CustomerContact::query()->truncate();
        Customer::query()->truncate();
        Schema::enableForeignKeyConstraints();

        $platforms = ContactPlatform::query()
            ->where('is_active', true)
            ->get();

        $faker = fake();

        for ($i = 0; $i < 35; $i++) {
            $customer = Customer::query()->create([
                'full_name' => $faker->name(),
                'email' => $faker->boolean(85) ? $faker->unique()->safeEmail() : null,
                'city' => $faker->boolean(80) ? $faker->city() : null,
                'phone' => $faker->boolean(80) ? $faker->numerify('09########') : null,
            ]);

            $this->seedContactsForCustomer($customer, $platforms, $faker);
        }
    }

    private function seedContactsForCustomer(Customer $customer, Collection $platforms, \Faker\Generator $faker): void
    {
        if ($platforms->isEmpty() || $faker->boolean(20)) {
            return;
        }

        $selectedPlatforms = $platforms
            ->shuffle()
            ->take($faker->numberBetween(1, min(3, $platforms->count())))
            ->values();

        $primaryPlatformId = $selectedPlatforms->random()->id;

        foreach ($selectedPlatforms as $platform) {
            $customer->contactPlatforms()->create([
                'platform_id' => $platform->id,
                'contact_identifier' => $this->fakeIdentifierForPlatform($platform->slug, $faker),
                'is_primary' => $platform->id === $primaryPlatformId,
            ]);
        }
    }

    private function fakeIdentifierForPlatform(string $slug, \Faker\Generator $faker): string
    {
        return match ($slug) {
            'whatsapp' => $faker->numerify('5939########'),
            'telegram' => '@' . $faker->unique()->userName(),
            default => $faker->unique()->userName(),
        };
    }
}
