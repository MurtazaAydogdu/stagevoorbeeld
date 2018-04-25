<?php

use Illuminate\Database\Seeder;

class StateTableSeeder extends Seeder {

    /**
     * run the database seeds
     * 
     * @return void
     */
    public function run() {
        DB::table('states')->insert([
            'name' => 'Settled',
            'description' => 'The transaction is settled'
        ]);

        DB::table('states')->insert([
            'name' => 'Paid',
            'description' => 'The transaction is paid'
        ]);

        DB::table('states')->insert([
            'name' => 'Open',
            'description' => 'The transaction is open'
        ]);


        DB::table('states')->insert([
            'name' => 'Pending',
            'description' => 'The transaction is pending'
        ]);

        DB::table('states')->insert([
            'name' => 'Cancelled',
            'description' => 'The transaction is cancelled'
        ]);

        DB::table('states')->insert([
            'name' => 'Expired',
            'description' => 'The transaction is expired'
        ]);

        DB::table('states')->insert([
            'name' => 'Refunded',
            'description' => 'The transaction is refunded'
        ]);

        DB::table('states')->insert([
            'name' => 'Chargeback',
            'description' => 'The transaction is chargeback'
        ]);

        DB::table('states')->insert([
            'name' => 'Failed',
            'description' => 'The transaction is failed'
        ]);
    }

}