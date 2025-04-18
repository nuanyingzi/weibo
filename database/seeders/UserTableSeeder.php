<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(100)->create();

        $user = User::find(3);
        $user->name = '小天狼星';
        $user->email = 'zhongtao1024@gmail.com';
        $user->is_admin = true;
        $user->save();
    }
}
