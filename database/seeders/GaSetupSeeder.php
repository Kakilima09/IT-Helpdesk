<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ga\GaCategory;
use Spatie\Permission\Models\Role;

class GaSetupSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['code' => 'ATK', 'name' => 'Alat Tulis Kantor (ATK)'],
            ['code' => 'RTK', 'name' => 'Rumah Tangga Kantor (RTK)'],
        ];

        foreach ($categories as $cat) {
            GaCategory::firstOrCreate(
                ['code' => $cat['code']],
                ['name' => $cat['name'], 'status' => true]
            );
        }

        foreach (['ga_staff', 'ga_manager'] as $roleName) {
            if (!Role::where('name', $roleName)->where('guard_name', 'web')->exists()) {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
            }
        }
    }
}