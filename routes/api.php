<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LanguagesController;
use App\Http\Controllers\Api\TopicAreaController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ConfigurationController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Api\StudentController;


//public urls
Route::get('get-config',[ConfigurationController::class,'index']);
Route::post('login',[LoginController::class,'login']);
Route::get('verify_token', [LoginController::class, 'apiVerifyToken']);
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');

//Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::post('create-user', [UserController::class, 'saveUser']);
    Route::get('get-all-users', [UserController::class, 'index']);
    Route::delete('delete-user/{id}',[UserController::class,'deleteUser']);

    Route::post('save-lang',[LanguagesController::class,'saveLanguage']);
    Route::get('get-all-lang',[LanguagesController::class,'index']);
    Route::get('get-all-lang-for-dropdown',[LanguagesController::class,'getAllLangForDropdown']);
    Route::delete('delete-lang/{id}',[LanguagesController::class,'deleteLanguage']);

    Route::post('save-course',[CourseController::class,'saveCourse']);
    Route::post('save-course-translation',[CourseController::class,'saveCourseTranslation']);
    Route::get('get-all-courses',[CourseController::class,'index']);
    Route::delete('delete-course/{id}',[CourseController::class,'deleteCourse']);

    Route::get('get-course-config/{id}',[CourseController::class,'getCourseConfig']);
    Route::post('save-course-config',[CourseController::class,'saveCourseConfig']);

    Route::post('save-topic-area',[TopicAreaController::class,'saveTopicArea']);
    Route::get('get-all-topics',[TopicAreaController::class,'index']);
    Route::any('save-topic-area-translation',[TopicAreaController::class,'saveTopicTranslation']);
    Route::delete('delete-topic/{id}',[TopicAreaController::class,'deleteTopic']);

    Route::post('save-config',[ConfigurationController::class,'saveConfig']);

    Route::post('import-question', [QuestionController::class, 'importQuestion']);
    Route::post('create-questions',[QuestionController::class,'createQuestion']);
    Route::get('get-data-question/{id}',[QuestionController::class,'getDataQuestion']);
    Route::get('get-all-questions',[QuestionController::class,'index']);
    Route::delete('delete-question/{id}',[QuestionController::class,'deleteQuestion']);
    Route::post('/remove-q-asset',[QuestionController::class,'removeQAsset']);


    Route::get('get-translation-question/{id}',[QuestionController::class,'getTranslationQuestion']);
    Route::post('save-question-translation',[QuestionController::class,'saveQuestionTranslation']);

    Route::post('save-branch',[BranchController::class,'saveBranch']);
    Route::get('get-all-branches',[BranchController::class,'index']);
    Route::get('get-branches-list',[BranchController::class,'branchesList']);
    Route::delete('delete-branch/{id}',[BranchController::class,'deleteBranch']);

    Route::post('save-room',[RoomController::class,'saveRoom']);
    Route::get('get-all-rooms',[RoomController::class,'index']);
    Route::get('get-rooms-list',[RoomController::class,'roomsList']);
    Route::delete('delete-room/{id}',[RoomController::class,'deleteRoom']);
    Route::get('get-branch-rooms/{branchId}',[RoomController::class,'getBranchRooms']);

    Route::post('system-create',[SystemController::class,'saveSystem']);
    Route::delete('delete-system/{id}',[SystemController::class,'deleteSystem']);
    Route::get('get-room-systems/{id}',[SystemController::class,'getRoomWiseSystems']);


    Route::post('save-role',[RoleController::class,'saveRole']);
    Route::get('get-all-roles',[RoleController::class,'index']);
    Route::delete('delete-role/{id}',[RoleController::class,'deleteRole']);


    Route::get('get-all-permissions/{id}',[RoleController::class,'getAllPermissions']);
    Route::post('save-role-permissions',[RoleController::class,'saveRolePermissions']);


    // Exam
    Route::post('get-result-detail',[ExamController::class,'getResults']);
    Route::any('restart-exam/{id}',[ExamController::class,'restartExam']);
    Route::any('exit-exam/{id}',[ExamController::class,'exitExam']);
    Route::get('get-schedule-exam-list',[ExamController::class,'getScheduleExamList']);
    Route::get('get-running-exam',[ExamController::class,'getRunningExam']);
    Route::post('store-schedule-exam',[StudentController::class,'saveScheduleExam']);
    Route::post('update-schedule-exam',[ExamController::class,'updateScheduleExam']);
    Route::delete('delete-exam/{id}',[ExamController::class,'deleteExam']);

//});

Route::any('get-all-results',[ExamController::class,'getAllResults']);
Route::any('get-practice-result',[ExamController::class,'getPracticeResult']);
Route::get('get-student-result',[ExamController::class,'getStudentResult']);
Route::get('get-sms-logs',[ExamController::class,'getLogs']);


       Route::get('check-system-ip/{systemIp}',[SystemController::class,'checkSystemIp']);
        // Student Area
        Route::any('get-exam-questions',[ExamController::class,'getQuestionsForExam']);
        Route::post('save-exam-questions',[ExamController::class,'saveQuestionsForExam']);
        Route::post('save-practice-questions',[ExamController::class,'savePracticeQuestions']);
        Route::post('exam-system-status-update',[ExamController::class,'examSystemStatusUpdate']);

        Route::get('system-list',[SystemController::class,'systemList']);
        Route::get('get-bdc-std',[StudentController::class,'getBdcStd']);
        Route::post('check-practice-type',[ExamController::class,'checkPracticeType']);
        Route::any('send-sms',[ExamController::class,'sendResultEmailAndSms']);
        Route::any('store-result-pdf',[ExamController::class,'storeResultPdf']);












