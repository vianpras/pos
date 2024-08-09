<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users_acls')->insert([
            'id' => null,
            'name' => 'adminstrator',
            'application' => 'crudie',
            'company' => 'crudie',
            'master' => 'crudie',
            'purchase' => 'crudie',
            'sales' => 'crudie',
            'log_apps' => 'crudie',
            'dashboard' => 'crudie',

            'master_acl' => 'crudie',
            'master_category' => 'crudie',
            'master_item' => 'crudie',
            'master_sales_category' => 'crudie',
            'master_unit' => 'crudie',
            'master_user' => 'crudie',
            'master_docPrefix' => 'crudie',

            'purchase_order' => 'crudie',
            'transaction_purchase' => 'crudie',
            'sales_order' => 'crudie',
            'transaction_sales' => 'crudie',
            'membership' => 'crudie',
            'booking' => 'crudie',

            'purchase_report' => 'crudie',
            'sales_report' => 'crudie',
            'overall_report' => 'crudie',

            'member_profile' => 'crudie',
            'member_booking' => 'crudie',

        ]);

        DB::table('users_acls')->insert([
            'id' => null,
            'name' => 'Super User',
            'application' => 'crudie',
            'company' => 'crudie',
            'master' => 'crudie',
            'purchase' => 'crudie',
            'sales' => 'crudie',
            'log_apps' => 'crudie',
            'dashboard' => 'crudie',
            'master_acl' => 'crudie',
            'master_category' => 'crudie',
            'master_item' => 'crudie',
            'master_sales_category' => 'crudie',
            'master_unit' => 'crudie',
            'master_user' => 'crudie',
            'master_docPrefix' => 'crudie',
            'purchase_order' => 'crudie',
            'transaction_purchase' => 'crudie',
            'sales_order' => 'crudie',
            'transaction_sales' => 'crudie',
            'membership' => 'crudie',
            'booking' => 'crudie',
            'purchase_report' => 'crudie',
            'sales_report' => 'crudie',
            'overall_report' => 'crudie',
            'member_profile' => 'crudie',
            'member_booking' => 'crudie',
        ]);
        DB::table('users_acls')->where('id',2)->update(['id'=>0]);
        DB::table('users')->insert([
            'id'=> null,
            'name'=> 'administrator',
            'username'=> 'admin',
            'email'=> 'admin@coba.com',
            'mobile'=> '6281336367798',
            'password'=>  Hash::make('tidaktahu'),
            'device_key'=> '',
            'status'=> '1',
            'failed_login'=> '0',
            'last_login'=> null,
            'email_verified_at'=> null,
            'sudo'=> '1',
            'users_acls_id'=> '1',

        ]);
        DB::table('sales_categories')->insert([
            'id' => null,
            'name' => 'Umum',
            'mark_up' => 0,
            'description' => null,
            'user_created' => 1,
            'user_updated' => null,
            'status' => 1,
            'status_EI' => 0,
            'created_at' => '2021-05-12 15:58:32',
            'updated_at' => null,

        ]);
        DB::table('sales_categories')->insert([
            'id' => null,
            'name' => 'Grab Food',
            'mark_up' => 10,
            'description' => null,
            'user_created' => 1,
            'user_updated' => null,
            'status' => 1,
            'status_EI' => 0,
            'created_at' => '2021-05-12 15:58:32',
            'updated_at' => null,

        ]);
        DB::table('sales_categories')->insert([
            'id' => null,
            'name' => 'Go Food',
            'mark_up' => 10,
            'description' => null,
            'user_created' => 1,
            'user_updated' => null,
            'status' => 1,
            'status_EI' => 0,
            'created_at' => '2021-05-12 15:58:32',
            'updated_at' => null,

        ]);
        DB::table('sales_categories')->insert([
            'id' => null,
            'name' => 'Shopee Food',
            'mark_up' => 10,
            'description' => null,
            'user_created' => 1,
            'user_updated' => null,
            'status' => 1,
            'status_EI' => 0,
            'created_at' => '2021-05-12 15:58:32',
            'updated_at' => null,

        ]);

    }
}
