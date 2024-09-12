<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $countries = [
            ['name' => 'United States', 'code' => 'US'],
            ['name' => 'Canada', 'code' => 'CA'],
            ['name' => 'United Kingdom', 'code' => 'GB'],
            ['name' => 'Australia', 'code' => 'AU'],
            ['name' => 'Germany', 'code' => 'DE'],
            ['name' => 'France', 'code' => 'FR'],
            ['name' => 'India', 'code' => 'IN'],
            ['name' => 'China', 'code' => 'CN'],
            ['name' => 'Japan', 'code' => 'JP'],
            ['name' => 'Brazil', 'code' => 'BR'],
            ['name' => 'Russia', 'code' => 'RU'],
            ['name' => 'South Africa', 'code' => 'ZA'],
            ['name' => 'Mexico', 'code' => 'MX'],
            ['name' => 'Argentina', 'code' => 'AR'],
            ['name' => 'Italy', 'code' => 'IT'],
            ['name' => 'Spain', 'code' => 'ES'],
            ['name' => 'Netherlands', 'code' => 'NL'],
            ['name' => 'Sweden', 'code' => 'SE'],
            ['name' => 'Norway', 'code' => 'NO'],
            ['name' => 'Denmark', 'code' => 'DK'],
            ['name' => 'Finland', 'code' => 'FI'],
            ['name' => 'Switzerland', 'code' => 'CH'],
            ['name' => 'Belgium', 'code' => 'BE'],
            ['name' => 'Austria', 'code' => 'AT'],
            ['name' => 'New Zealand', 'code' => 'NZ'],
            ['name' => 'South Korea', 'code' => 'KR'],
            ['name' => 'Singapore', 'code' => 'SG'],
            ['name' => 'Hong Kong', 'code' => 'HK'],
            ['name' => 'Malaysia', 'code' => 'MY'],
            ['name' => 'Indonesia', 'code' => 'ID'],
            ['name' => 'Thailand', 'code' => 'TH'],
            ['name' => 'Philippines', 'code' => 'PH'],
            ['name' => 'Saudi Arabia', 'code' => 'SA'],
            ['name' => 'United Arab Emirates', 'code' => 'AE'],
            ['name' => 'Turkey', 'code' => 'TR'],
            ['name' => 'Egypt', 'code' => 'EG'],
            ['name' => 'Nigeria', 'code' => 'NG'],
            ['name' => 'Kenya', 'code' => 'KE'],
            ['name' => 'Israel', 'code' => 'IL'],
            ['name' => 'Greece', 'code' => 'GR'],
            ['name' => 'Portugal', 'code' => 'PT'],
            ['name' => 'Poland', 'code' => 'PL'],
            ['name' => 'Czech Republic', 'code' => 'CZ'],
            ['name' => 'Hungary', 'code' => 'HU'],
            ['name' => 'Romania', 'code' => 'RO'],
            ['name' => 'Ukraine', 'code' => 'UA'],
            ['name' => 'Colombia', 'code' => 'CO'],
            ['name' => 'Peru', 'code' => 'PE'],
            ['name' => 'Chile', 'code' => 'CL'],
            ['name' => 'Venezuela', 'code' => 'VE'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}