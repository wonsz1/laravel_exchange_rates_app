<?php

namespace Database\Seeders;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function __construct(private DatabaseManager $db)
    {
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'symbol' => 'USD',
                'name' => 'United States Dollar',
                'iso_code' => 'USD',
                'is_active' => true
            ],
            [
                'symbol' => 'EUR',
                'name' => 'Euro',
                'iso_code' => 'EUR',
                'is_active' => true
            ],
            [
                'symbol' => 'PLN',
                'name' => 'Polish Złoty',
                'iso_code' => 'PLN',
                'is_active' => true
            ]
        ];

        foreach ($currencies as $currency) {
            $this->db->table('currencies')->insert($currency);
        }
    }
}
