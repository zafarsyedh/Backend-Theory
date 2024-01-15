<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\PermissionModule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $course_module = PermissionModule::updateOrCreate(['title' => 'Course'], ['title' =>'Course'] );
        $topic_module = PermissionModule::updateOrCreate(['title' => 'Topic Area'], ['title' =>'Topic Area'] );
        $q_module = PermissionModule::updateOrCreate(['title' => 'Question'], ['title' =>'Question'] );
        $lang_module = PermissionModule::updateOrCreate(['title' => 'Languages'], ['title' =>'Languages'] );
        $user_module = PermissionModule::updateOrCreate(['title' => 'User Management'], ['title' =>'User Management'] );
        $role_module = PermissionModule::updateOrCreate(['title' => 'Roles'], ['title' =>'Roles'] );
        $config_module = PermissionModule::updateOrCreate(['title' => 'Configuration'], ['title' =>'Configuration'] );


        Permission::upsert([
            ['name' => 'course-view', 'module_id' =>$course_module->id, 'guard_name' => 'web'],
            ['name' => 'course-create', 'module_id' =>$course_module->id, 'guard_name' => 'web'],
            ['name' => 'course-edit', 'module_id' =>$course_module->id, 'guard_name' => 'web'],
            ['name' => 'course-delete', 'module_id' =>$course_module->id, 'guard_name' => 'web'],
            ['name' => 'course-translate', 'module_id' =>$course_module->id, 'guard_name' => 'web'],

            ['name' => 'topic-view', 'module_id' =>$topic_module->id, 'guard_name' => 'web'],
            ['name' => 'topic-create', 'module_id' =>$topic_module->id, 'guard_name' => 'web'],
            ['name' => 'topic-edit', 'module_id' =>$topic_module->id, 'guard_name' => 'web'],
            ['name' => 'topic-delete', 'module_id' =>$topic_module->id, 'guard_name' => 'web'],
            ['name' => 'topic-translate', 'module_id' =>$topic_module->id, 'guard_name' => 'web'],

            //Question
            ['name' => 'q-view', 'module_id' =>$q_module->id, 'guard_name' => 'web'],
            ['name' => 'q-create', 'module_id' =>$q_module->id, 'guard_name' => 'web'],
            ['name' => 'q-edit', 'module_id' =>$q_module->id, 'guard_name' => 'web'],
            ['name' => 'q-delete', 'module_id' =>$q_module->id, 'guard_name' => 'web'],
            ['name' => 'q-translate', 'module_id' =>$q_module->id, 'guard_name' => 'web'],


            //Config
            ['name' => 'config-create', 'module_id' =>$config_module->id, 'guard_name' => 'web'],


            //Users
            ['name' => 'user-view', 'module_id' =>$user_module->id, 'guard_name' => 'web'],
            ['name' => 'user-create', 'module_id' =>$user_module->id, 'guard_name' => 'web'],
            ['name' => 'user-edit', 'module_id' =>$user_module->id, 'guard_name' => 'web'],
            ['name' => 'user-delete', 'module_id' =>$user_module->id, 'guard_name' => 'web'],

            //language
            ['name' => 'language-view', 'module_id' =>$lang_module->id, 'guard_name' => 'web'],
            ['name' => 'language-create', 'module_id' =>$lang_module->id, 'guard_name' => 'web'],
            ['name' => 'language-edit', 'module_id' =>$lang_module->id, 'guard_name' => 'web'],
            ['name' => 'language-delete', 'module_id' =>$lang_module->id, 'guard_name' => 'web'],
            ['name' => 'language-translate', 'module_id' =>$lang_module->id, 'guard_name' => 'web'],

            //role
            ['name' => 'role-view', 'module_id' =>$role_module->id, 'guard_name' => 'web'],
            ['name' => 'role-create', 'module_id' =>$role_module->id, 'guard_name' => 'web'],
            ['name' => 'role-edit', 'module_id' =>$role_module->id, 'guard_name' => 'web'],
            ['name' => 'role-delete', 'module_id' =>$role_module->id, 'guard_name' => 'web'],

        ], ['name']);

    }
}
