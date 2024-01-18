<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LanguagesController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TopicAreaController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ConfigurationController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\SystemController;




//Route::middleware('auth:sanctum')->group(function () {

Route::any('create-user', [UserController::class, 'saveUser']);
Route::any('get-all-users', [UserController::class, 'index']);
Route::any('delete-user/{id}',[UserController::class,'deleteUser']);
Route::any('edit-user',[UserController::class,'editUser']);
Route::any('update-user',[UserController::class,'updateUser']);
Route::any('get-invigilator',[UserController::class,'getInvigilator']);


Route::any('save-lang',[LanguagesController::class,'saveLanguage']);
Route::any('delete-lang/{id}',[LanguagesController::class,'deleteLanguage']);
Route::any('edit-lang',[LanguagesController::class,'editLanguage']);
Route::any('update-lang',[LanguagesController::class,'updateLanguage']);
Route::any('get-all-lang',[LanguagesController::class,'index']);
Route::any('get-all-lang-for-dropdown',[LanguagesController::class,'getAllLangForDropdown']);

Route::any('save-course',[CourseController::class,'saveCourse']);
Route::any('save-course-translation',[CourseController::class,'saveCourseTranslation']);
Route::any('delete-course/{id}',[CourseController::class,'deleteCourse']);
Route::any('update-course',[CourseController::class,'updateCourse']);
Route::any('get-all-courses',[CourseController::class,'index']);


Route::any('get-course-config/{id}',[CourseController::class,'getCourseConfig']);
Route::any('save-course-config',[CourseController::class,'saveCourseConfig']);



Route::any('save-topic-area',[TopicAreaController::class,'saveTopicArea']);
Route::any('get-all-topics',[TopicAreaController::class,'index']);
Route::any('delete-topic/{id}',[TopicAreaController::class,'deleteTopic']);
Route::any('update-topic',[TopicAreaController::class,'updateTopic']);
Route::any('save-topic-area-translation',[TopicAreaController::class,'saveTopicTranslation']);

Route::any('get-all-roles',[RoleController::class,'index']);
Route::any('save-role',[RoleController::class,'saveRole']);
Route::any('edit-role',[RoleController::class,'editRole']);
Route::any('update-role',[RoleController::class,'updateRole']);

Route::any('delete-role/{id}',[RoleController::class,'deleteRole']);
Route::any('get-all-permissions/{id}',[RoleController::class,'getAllPermissions']);
Route::any('save-role-permissions',[RoleController::class,'saveRolePermissions']);


Route::any('get-config',[ConfigurationController::class,'index']);
Route::any('save-config',[ConfigurationController::class,'saveConfig']);

Route::any('create-video-questions',[QuestionController::class,'createVideoQuestion']);
Route::any('create-questions',[QuestionController::class,'createQuestion']);
Route::any('get-data-question/{id}',[QuestionController::class,'getDataQuestion']);
Route::any('get-translation-question/{id}',[QuestionController::class,'getTranslationQuestion']);
Route::any('get-all-questions',[QuestionController::class,'index']);
Route::any('delete-question/{id}',[QuestionController::class,'deleteQuestion']);

Route::any('save-question-translation',[QuestionController::class,'saveQuestionTranslation']);

Route::any('edit-question',[QuestionController::class,'editQuestion']);
Route::any('update-question',[QuestionController::class,'updateQuestion']);

Route::any('save-branch',[BranchController::class,'saveBranch']);
Route::any('get-all-branches',[BranchController::class,'index']);
Route::any('get-branches-list',[BranchController::class,'branchesList']);
Route::any('delete-branch/{id}',[BranchController::class,'deleteBranch']);

Route::post('save-room',[RoomController::class,'saveRoom']);
Route::any('get-all-rooms',[RoomController::class,'index']);
Route::any('get-rooms-list',[RoomController::class,'roomsList']);
Route::any('delete-room/{id}',[RoomController::class,'deleteRoom']);

Route::any('system-list',[SystemController::class,'systemList']);
Route::post('system-create',[SystemController::class,'saveSystem']);
Route::any('delete-system/{id}',[SystemController::class,'deleteSystem']);




Route::any('count-course-audio-video-question',[CategoryController::class,'countCourseAudioVideoQuestion']);


//});

Route::any('login',[LoginController::class,'login']);
Route::any('verify_token', [LoginController::class, 'apiVerifyToken']);
Route::any('import-question', [QuestionController::class, 'importQuestion']);



