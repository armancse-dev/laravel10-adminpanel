<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = Hash::make('12345');
        $adminRecords = [
            ['id'=>2,'name'=>'nasima', 'type'=> 'subadmin', 'mobile'=>'01737078859', 'email'=>'nasimatpi@gmail.com', 'password'=>$password, 'image'=>'', 'status'=>1],
            ['id'=>3,'name'=>'riya', 'type'=> 'subadmin', 'mobile'=>'01765963902', 'email'=>'riya@gmail.com', 'password'=>$password, 'image'=>'', 'status'=>1],
        ];
        Admin::insert($adminRecords);
    }
}
