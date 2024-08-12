<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Instructor James',
                'email' => 'instructorjames@gmail.com',
                'password' => bcrypt('password'), // password is 'password'
            ],
            [
                'name' => 'Student John',
                'email' => 'studentjohn@gmail.com',
                'password' => bcrypt('password'), // password is 'password'
            ],
        ];

        foreach ($users as $user) {
            \App\Models\User::create($user);
        }

        $instructor = \App\Models\User::where('email', 'instructorjames@gmail.com')->first();
        $instructor->assignRole('instructor');

        $student = \App\Models\User::where('email', 'studentjohn@gmail.com')->first();
        $student->assignRole('student');
    }
}
