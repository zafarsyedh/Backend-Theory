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
        $branch_module = PermissionModule::updateOrCreate(['title' => 'Branch'], ['title' =>'Branch'] );
        $exam_module = PermissionModule::updateOrCreate(['title' => 'Exam'], ['title' =>'Exam'] );
        $room_module = PermissionModule::updateOrCreate(['title' => 'Room'], ['title' =>'Room'] );
        $system_module = PermissionModule::updateOrCreate(['title' => 'System'], ['title' =>'System'] );
        $lang_module = PermissionModule::updateOrCreate(['title' => 'Languages'], ['title' =>'Languages'] );
        $user_module = PermissionModule::updateOrCreate(['title' => 'User Management'], ['title' =>'User Management'] );
        $role_module = PermissionModule::updateOrCreate(['title' => 'Roles'], ['title' =>'Roles'] );
        $config_module = PermissionModule::updateOrCreate(['title' => 'Configuration'], ['title' =>'Configuration'] );
        $exam_module = PermissionModule::updateOrCreate(['title' => 'Schedule Exam'], ['title' =>'Schedule Exam'] );
        $running_exam = PermissionModule::updateOrCreate(['title' => 'Running Exam'], ['title' =>'Running Exam'] );
        $exam_result = PermissionModule::updateOrCreate(['title' => 'Exam Result'], ['title' =>'Exam Result'] );
        $practice_result = PermissionModule::updateOrCreate(['title' => 'Practice Result'], ['title' =>'Practice Result'] );


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

            //exam
            ['name' => 'exam-view', 'module_id' =>$exam_module->id, 'guard_name' => 'web'],
            ['name' => 'exam-create', 'module_id' =>$exam_module->id, 'guard_name' => 'web'],
            ['name' => 'exam-edit', 'module_id' =>$exam_module->id, 'guard_name' => 'web'],
            ['name' => 'exam-delete', 'module_id' =>$exam_module->id, 'guard_name' => 'web'],

            //Branch
            ['name' => 'branch-view', 'module_id' =>$branch_module->id, 'guard_name' => 'web'],
            ['name' => 'branch-create', 'module_id' =>$branch_module->id, 'guard_name' => 'web'],
            ['name' => 'branch-edit', 'module_id' =>$branch_module->id, 'guard_name' => 'web'],
            ['name' => 'branch-delete', 'module_id' =>$branch_module->id, 'guard_name' => 'web'],

            //Room
            ['name' => 'room-view', 'module_id' =>$room_module->id, 'guard_name' => 'web'],
            ['name' => 'room-create', 'module_id' =>$room_module->id, 'guard_name' => 'web'],
            ['name' => 'room-edit', 'module_id' =>$room_module->id, 'guard_name' => 'web'],
            ['name' => 'room-delete', 'module_id' =>$room_module->id, 'guard_name' => 'web'],

            //System
            ['name' => 'system-view', 'module_id' =>$system_module->id, 'guard_name' => 'web'],
            ['name' => 'system-create', 'module_id' =>$system_module->id, 'guard_name' => 'web'],
            ['name' => 'system-edit', 'module_id' =>$system_module->id, 'guard_name' => 'web'],
            ['name' => 'system-delete', 'module_id' =>$system_module->id, 'guard_name' => 'web'],


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


            //Schedule Exam
            ['name' => 'exam-view', 'module_id' =>$exam_module->id, 'guard_name' => 'web'],
            ['name' => 'exam-create', 'module_id' =>$exam_module->id, 'guard_name' => 'web'],
            ['name' => 'exam-edit', 'module_id' =>$exam_module->id, 'guard_name' => 'web'],
            ['name' => 'exam-delete', 'module_id' =>$exam_module->id, 'guard_name' => 'web'],

            //Running Exam
            ['name' => 'running-exam-view', 'module_id' =>$running_exam->id, 'guard_name' => 'web'],

            // Exam Result
            ['name' => 'exam-result-view', 'module_id' =>$exam_result->id, 'guard_name' => 'web'],

            // Practice Result
            ['name' => 'practice-result-view', 'module_id' =>$practice_result->id, 'guard_name' => 'web'],


        ], ['name']);

    }
}
