<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 4,
                'name' => 'hesho',
                'email' => 'hesham3@example.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$.PBuM/kTUzNPxEQnl6X3nujhWX2ZvZ2/PHdTXDbYwn0xVUYmqnKGK',
                'remember_token' => '',
                'created_at' => '2025-04-27 01:50:54',
                'updated_at' => '2025-07-23 13:19:39',
                'role' => 'admin',
                'role_id' => '4',
                'phone' => '01124116368',
                'address' => 'Maadi cornich, El mohandseen towers Num 5 flat Num 22
El mohandseen towers Num 5 flat Num 22',
                'last_login_at' => '2025-07-23 13:19:39',
                'is_active' => 1,
                'expires_at' => NULL,
                'theme_color' => '#34495E',
            ),
            1 => 
            array (
                'id' => 8,
                'name' => 'ahmed',
                'email' => 'ahmEEed@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$k5uDSt0OxD2oOlh/mr1BZukGwxj6ibp9wyOS.l94e8p3MYRshIqXi',
                'remember_token' => '',
                'created_at' => '2025-05-01 09:28:48',
                'updated_at' => '2025-06-27 12:25:34',
                'role' => 'delivery_agent',
                'role_id' => '2',
                'phone' => '01124116368',
                'address' => 'Maadi cornich, El mohandseen towers Num 5 flat Num 22',
                'last_login_at' => '2025-06-27 12:25:34',
                'is_active' => 1,
                'expires_at' => NULL,
                'theme_color' => '#2C3E50',
            ),
            2 => 
            array (
                'id' => 9,
                'name' => 'hesham',
                'email' => 'hesham@gmai.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$2XI67f8tY4F4SSw8M3XcMe/WK8ClCv66dgINvWe1CcX1.YIKkksUi',
                'remember_token' => '',
                'created_at' => '2025-05-01 09:31:34',
                'updated_at' => '2025-05-08 23:22:40',
                'role' => 'delivery_agent',
                'role_id' => '2',
                'phone' => '12345678',
                'address' => 'Maadi cornich, El mohandseen towers Num 5 flat Num 22',
                'last_login_at' => '2025-05-08 23:22:40',
                'is_active' => 1,
                'expires_at' => NULL,
                'theme_color' => '#2C3E50',
            ),
            3 => 
            array (
                'id' => 10,
                'name' => 'nour',
                'email' => 'nour@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$RM5JXSZAcwP27orIijWKbuclBIJlLk4d2PzZGawuNRSjfeJDmJIgC',
                'remember_token' => '',
                'created_at' => '2025-05-01 09:48:07',
                'updated_at' => '2025-06-27 12:26:33',
                'role' => 'moderator',
                'role_id' => '3',
                'phone' => '01124116368',
                'address' => 'Maadi cornich, El mohandseen towers Num 5 flat Num 22',
                'last_login_at' => '2025-06-27 12:26:33',
                'is_active' => 1,
                'expires_at' => NULL,
                'theme_color' => '#2C3E50',
            ),
            4 => 
            array (
                'id' => 12,
                'name' => 'belal',
                'email' => 'hesho@r.F',
                'email_verified_at' => NULL,
                'password' => '$2y$12$r/Y0RRiTmolR9O/YOCnY1eBYutWT7N3nqphBqRyx/lTeGLjN7FSC2',
                'remember_token' => NULL,
                'created_at' => '2025-05-06 05:28:34',
                'updated_at' => '2025-06-27 12:27:40',
                'role' => 'accountant',
                'role_id' => '5',
                'phone' => '01124116368',
                'address' => 'Maadi cornich, El mohandseen towers Num 5 flat Num 22',
                'last_login_at' => '2025-06-27 12:27:40',
                'is_active' => 1,
                'expires_at' => NULL,
                'theme_color' => '#2C3E50',
            ),
            5 => 
            array (
                'id' => 18,
                'name' => 'hend',
                'email' => 'hend@g.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$2DcuVAw3JaWO19ZezBo3c.m92NUd.bXUKOESxgE7Vd6pSxMigy1Re',
                'remember_token' => NULL,
                'created_at' => '2025-05-08 13:15:48',
                'updated_at' => '2025-05-08 22:31:23',
                'role' => 'delivery_agent',
                'role_id' => '2',
                'phone' => '01124116368',
                'address' => 'Maadi cornich, El mohandseen towers Num 5 flat Num 22',
                'last_login_at' => '2025-05-08 22:31:23',
                'is_active' => 0,
                'expires_at' => NULL,
                'theme_color' => '#2C3E50',
            ),
            6 => 
            array (
                'id' => 27,
                'name' => 'hany1',
                'email' => 'hend@r.com1',
                'email_verified_at' => NULL,
                'password' => '$2y$12$NBzTyPtuognjbq5RruQZc.F8UDb3Gw8lllW5pYKuj91oMk3uwvchW',
                'remember_token' => NULL,
                'created_at' => '2025-05-08 17:01:11',
                'updated_at' => '2025-05-08 17:32:57',
                'role' => 'admin',
                'role_id' => '4',
                'phone' => '0112411jhgffgh6368',
                'address' => 'Maadi cornich, El mohandseen towers Num 5 flat Num 22',
                'last_login_at' => NULL,
                'is_active' => 0,
                'expires_at' => '2025-05-09 17:32:57',
                'theme_color' => '#2C3E50',
            ),
            7 => 
            array (
                'id' => 28,
                'name' => 'يوسف',
                'email' => 'hend@t.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$8.VHK8sVbB1/oOVmICDdvOz59LiUfj3BiecaF3LHrN3aavEJCn5Kq',
                'remember_token' => NULL,
                'created_at' => '2025-05-08 22:30:27',
                'updated_at' => '2025-05-08 22:30:27',
                'role' => 'delivery_agent',
                'role_id' => '2',
                'phone' => '01124116368',
                'address' => 'Maadi cornich, El mohandseen towers Num 5 flat Num 22',
                'last_login_at' => NULL,
                'is_active' => 1,
                'expires_at' => '2025-05-11 22:30:27',
                'theme_color' => '#2C3E50',
            ),
            8 => 
            array (
                'id' => 31,
                'name' => 'reham',
                'email' => 'reham@f.co',
                'email_verified_at' => NULL,
                'password' => '$2y$12$ynLIUnNH9Iva7iDlNMa5cu0a/rDWSc4S7C1LKGxgW6jSFgWkaYieW',
                'remember_token' => NULL,
                'created_at' => '2025-05-25 17:10:05',
                'updated_at' => '2025-05-25 17:24:48',
                'role' => 'admin',
                'role_id' => '4',
                'phone' => '1124116368',
                'address' => 'badrashin, giza',
                'last_login_at' => '2025-05-25 17:24:19',
                'is_active' => 1,
                'expires_at' => '2125-05-22 17:24:48',
                'theme_color' => '#2C3E50',
            ),
            9 => 
            array (
                'id' => 32,
                'name' => 'اختبار_استرجاع',
                'email' => 'test_restore@example.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$MjWs9kxEiwuOhseveuowt.4S25dZeL7TkVgiGlv3.JTvrLPDsNvWi',
                'remember_token' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'role' => NULL,
                'role_id' => NULL,
                'phone' => NULL,
                'address' => NULL,
                'last_login_at' => NULL,
                'is_active' => 1,
                'expires_at' => NULL,
                'theme_color' => '#2C3E50',
            ),
        ));
        
        
    }
}