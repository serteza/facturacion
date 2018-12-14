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
        $userAdmin = new User();
        
        $userAdmin->email ="admin@serteza.com";
        $userAdmin->name ="admin";
        $userAdmin->rol = 1;
        $userAdmin->password = Hash::make('admin');
        $userAdmin->created_at = date('Y-m-d H:m:s');
        $userAdmin->updated_at = date('Y-m-d H:m:s');
        $userAdmin->save();

    }
}
