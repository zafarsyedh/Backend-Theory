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
//Route::any('edit-lang',[LanguagesController::class,'editLanguage']);
Route::any('update-course',[CourseController::class,'updateCourse']);
Route::any('get-all-courses',[CourseController::class,'index']);
//Route::any('get-all-lang-for-dropdown',[LanguagesController::class,'getAllLangForDropdown']);

Route::any('get-course-config/{id}',[CourseController::class,'getCourseConfig']);
Route::any('save-course-config',[CourseController::class,'saveCourseConfig']);


Route::any('get-all-cat',[CategoryController::class,'index']);
Route::any('save-cat',[CategoryController::class,'saveCategory']);
Route::any('delete-cat',[CategoryController::class,'deleteCategory']);
Route::any('edit-cat',[CategoryController::class,'editCategory']);
Route::any('update-cat',[CategoryController::class,'updateCategory']);
Route::any('get-course-dropdown',[CategoryController::class,'getCourseDropdownList']);
//Route::any('save-course-config',[CategoryController::class,'saveCourseConfig']);
//Route::any('get-course-config-info',[CategoryController::class,'getCourseConfigInfo']);


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

Route::any('get-all-students',[StudentController::class,'index']);
Route::any('save-student',[StudentController::class,'create']);
Route::any('delete-student',[StudentController::class,'deleteStudent']);
Route::any('edit-student',[StudentController::class,'editStudent']);
Route::any('update-student',[StudentController::class,'updateStudent']);
Route::any('get-std-dropdown',[StudentController::class,'getAllStdDropdown']);


Route::any('get-config',[ConfigurationController::class,'index']);
Route::any('save-config',[ConfigurationController::class,'saveConfig']);

Route::any('save-exam',[ExamController::class,'saveExam']);
Route::any('get-exam-list',[ExamController::class,'index']);
Route::any('edit-exam',[ExamController::class,'editExam']);
Route::any('update-exam',[ExamController::class,'updateExam']);
Route::any('delete-exam',[ExamController::class,'deleteExam']);
Route::any('is-exam-schedule',[ExamController::class,'isExamSchedule']);

Route::any('create-video-questions',[QuestionController::class,'createVideoQuestion']);
Route::any('create-questions',[QuestionController::class,'createQuestion']);
Route::any('get-data-question/{id}',[QuestionController::class,'getDataQuestion']);
Route::any('get-translation-question/{id}',[QuestionController::class,'getTranslationQuestion']);
Route::any('get-all-questions',[QuestionController::class,'index']);
Route::any('delete-question/{id}',[QuestionController::class,'deleteQuestion']);

Route::any('save-question-translation',[QuestionController::class,'saveQuestionTranslation']);



Route::any('edit-question',[QuestionController::class,'editQuestion']);
Route::any('update-question',[QuestionController::class,'updateQuestion']);
Route::any('get-practice-question',[QuestionController::class,'getPracticeQuestion']);
Route::any('get-video-question',[QuestionController::class,'getVideoQuestion']);
Route::any('save-practice-exam',[QuestionController::class,'savePracticeExam']);
Route::any('save-attempt',[QuestionController::class,'attempt']);
Route::any('get-actual-exam-question',[QuestionController::class,'getActualExamQuestion']);
Route::any('save-actual-exam',[QuestionController::class,'handleSaveActualExam']);
Route::any('get-questions-for-exam',[QuestionController::class,'getQuestionsForExam']);
Route::any('save-solved-questions',[QuestionController::class,'saveSolvedQuestionsForExam']);
Route::any('actual-exam-solved',[QuestionController::class,'actualExamSolved']);

Route::any('get-practice-result',[ResultController::class,'getPracticeResult']);
Route::any('get-practice-result-for-admin-report',[ResultController::class,'practiceResultForAdminReport']);
Route::any('get-exam-result',[ResultController::class,'getExamResult']);
Route::any('print-exam-result',[ResultController::class,'printExamResult']);
Route::any('get-admin-exam-result',[ResultController::class,'getExamResultForAdmin']);
Route::any('get-result-detail',[ResultController::class,'getResultDetail']);
Route::any('admin-dashboard-states',[DashboardController::class,'adminDashboardStates']);
Route::any('get-std-exam-info',[ExamController::class,'getStdExamInfo']);

Route::any('count-course-audio-video-question',[CategoryController::class,'countCourseAudioVideoQuestion']);

//});

Route::any('std-login',[StudentController::class,'login']);
Route::any('login',[LoginController::class,'login']);
Route::any('verify_token', [LoginController::class, 'apiVerifyToken']);
Route::any('import-question', [QuestionController::class, 'importQuestion']);



