<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\TransitTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransitTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $portugal = Country::where('name', 'like', '%Portugal%')->orWhere('name', 'like', '%Lisboa%')->first();

        $mozambique = Country::where('name', 'like', 'Mo%ambique%')->orWhere('coin', 'MT')->first();

        if (! $portugal || !$mozambique) {
            $this->command->warn('countries not found');
            return;
        }

        // Lisboa -> Maputo /EXPRESSO -> 48h

        TransitTime::firstOrCreate([
            'origin_country_id' => $portugal->id,
            'destination_country_id' => $mozambique->id,
            'service_type' => 'expresso',
        ], [
            'expected_hours' => 48,
            'departure_days' => [3, 5] // seg = 1, sex = 5,
        ]);

        TransitTime::firstOrCreate([
            'origin_country_id' => $portugal->id,
            'destination_country_id' => $mozambique->id,
            'service_type' => 'normal',
        ], [
            'expected_hours' => 96,
            'departure_days' => [3, 5],
        ]);
    }
}
