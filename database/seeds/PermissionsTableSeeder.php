<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Permission::create([
        	'name' => 'Xem danh sách tài khoàn',
        	'slug' => 'users.index',
        	'description' => 'Xem danh sách tài khoàn'
        ]);

        Permission::create([
        	'name' => 'Thêm tài khoàn',
        	'slug' => 'users.add',
        	'description' => 'Thêm tài khoàn'
        ]);

        Permission::create([
        	'name' => 'Sửa tài khoàn',
        	'slug' => 'users.edit',
        	'description' => 'Sửa tài khoàn'
        ]);

        Permission::create([
        	'name' => 'Xóa tài khoàn',
        	'slug' => 'users.delete',
        	'description' => 'Xóa tài khoàn'
        ]);
    }
}
