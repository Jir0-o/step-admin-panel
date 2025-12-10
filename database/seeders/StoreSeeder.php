<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    public function run()
    {
        Store::updateOrCreate(
            [
                'name' => 'Akota',
            ],
            [
                'base_url'       => 'http://akota.breachsoft.com/public/api/manager/data/summary',
                'login_api_url'  => 'http://akota.breachsoft.com/public/api/backoffice/login',
                'user_email'     => 'abusid@gmail.com',
                'user_password'  => Hash::make('01577027037'), 
            ]
        );
    }
}
