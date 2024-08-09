<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocPrefixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //projects
        DB::table('_docPrefix')->insert([
            'docType' => 'purchases',
            'prefix' => 'PO',
        ]);
        //purchase_requistion
        DB::table('_docPrefix')->insert([
            'docType' => 'sales',
            'prefix' => 'SO',
        ]);
        //pemesanan booking
        DB::table('_docPrefix')->insert([
            'docType' => 'bookings',
            'prefix' => 'BO',
        ]);

        //membership
        DB::table('_docPrefix')->insert([
            'docType' => 'memberships',
            'prefix' => 'M',
        ]);

        //default satuan
        DB::table('units')->insert([
            'code' => 'PCS',
            'name' => 'PIECES',
            'status' => 1,
            'status_EI' => 0,
            'user_created' => '1',
            'created_at' => '2022-02-13',
        ]);
        //default satuan
        DB::table('units')->insert([
            'code' => 'PKG',
            'name' => 'PACKAGES',
            'status' => 1,
            'status_EI' => 0,
            'user_created' => '1',
            'created_at' => '2022-02-13',
        ]);
        //default satuan
        DB::table('units')->insert([
            'code' => 'SET',
            'name' => 'SET',
            'status' => 1,
            'status_EI' => 0,
            'user_created' => '1',
            'created_at' => '2022-02-13',
        ]);

        DB::table('categories')->insert([
            'code' => 'BO',
            'name' => 'BOOKING',
            'description' => '',
            'as_parent' => 1,
            'parent' => null,
            'status_EI' => 0,
            'user_created' => '1',
            'created_at' => '2022-02-13',
        ]);
        DB::table('categories')->insert([
            'code' => 'SO',
            'name' => 'SALES',
            'description' => '',
            'as_parent' => 1,
            'parent' => null,
            'status_EI' => 0,
            'user_created' => '1',
            'created_at' => '2022-02-13',
        ]);

        DB::table('categories')->insert([
            'code' => 'PO',
            'name' => 'PURCHASE',
            'description' => '',
            'as_parent' => 1,
            'parent' => null,
            'status_EI' => 0,
            'user_created' => '1',
            'created_at' => '2022-02-13',
        ]);
        // sub sales categori
        DB::table('categories')->insert([
            'code' => 'FOOD_SALES',
            'name' => 'FOOD_SALES',
            'description' => '',
            'as_parent' => 1,
            'parent' => 2,
            'status_EI' => 0,
            'user_created' => '1',
            'created_at' => '2022-02-13',
        ]);
        DB::table('categories')->insert([
            'code' => 'DRINK_SALES',
            'name' => 'DRINK_SALES',
            'description' => '',
            'as_parent' => 1,
            'parent' => 2,
            'status_EI' => 0,
            'user_created' => '1',
            'created_at' => '2022-02-13',
        ]);
        DB::table('categories')->insert([
            'code' => 'OTHER_SALES',
            'name' => 'OTHER_SALES',
            'description' => '',
            'as_parent' => 1,
            'parent' => 2,
            'status_EI' => 0,
            'user_created' => '1',
            'created_at' => '2022-02-13',
        ]);
        DB::table('categories')->insert([
            'code' => 'SERVICE_SALES',
            'name' => 'SERVICE_SALES',
            'description' => '',
            'as_parent' => 1,
            'parent' => 2,
            'status_EI' => 0,
            'user_created' => '1',
            'created_at' => '2022-02-13',
        ]);


        // sub purchase categor

        DB::table('categories')->insert([
            'code' => 'BAHAN_PURCHASE',
            'name' => 'BAHAN_PURCHASE',
            'description' => '',
            'as_parent' => 1,
            'parent' => 3,
            'status_EI' => 0,
            'user_created' => '1',
            'created_at' => '2022-02-13',
        ]);
        DB::table('categories')->insert([
            'code' => 'OTHER_PURCHASE',
            'name' => 'OTHER_PURCHASE',
            'description' => '',
            'as_parent' => 1,
            'parent' => 3,
            'status_EI' => 0,
            'user_created' => '1',
            'created_at' => '2022-02-13',
        ]);
        DB::table('categories')->insert([
            'code' => 'SERVICE_PURCHASE',
            'name' => 'SERVICE_PURCHASE',
            'description' => '',
            'as_parent' => 1,
            'parent' => 3,
            'status_EI' => 0,
            'user_created' => '1',
            'created_at' => '2022-02-13',
        ]);
    }
}
