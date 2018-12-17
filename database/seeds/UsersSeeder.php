<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $rootUser = new User();
        
        $rootUser->email ="admin@serteza.com";
        $rootUser->name ="admin";
        $rootUser->rol = 1;
        $rootUser->password = Hash::make('admin');
        $rootUser->created_at = date('Y-m-d H:m:s');
        $rootUser->updated_at = date('Y-m-d H:m:s');
        $rootUser->save();

        $userAdmin = new User();
        $userAdmin->email ="gilcatzin@serteza.com";
        $userAdmin->name ="gilcatzin";
        $userAdmin->rol = 2;
        $userAdmin->password = Hash::make('12345678');
        $userAdmin->created_at = date('Y-m-d H:m:s');
        $userAdmin->updated_at = date('Y-m-d H:m:s');
        $userAdmin->save();

    }
}
