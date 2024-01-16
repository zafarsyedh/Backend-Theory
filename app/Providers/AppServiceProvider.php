<?php

namespace App\Providers;

use App\Repo\BranchClass;
use App\Repo\CategoryClass;
use App\Repo\ConfigurationClass;
use App\Repo\CourseClass;
use App\Repo\ExamClass;
use App\Repo\Interfaces\BranchInterface;
use App\Repo\Interfaces\CategoryInterface;
use App\Repo\Interfaces\ConfigurationInterface;
use App\Repo\Interfaces\CourseInterface;
use App\Repo\Interfaces\ExamInterface;
use App\Repo\Interfaces\LanguageInterface;
use App\Repo\Interfaces\QuestionInterface;
use App\Repo\Interfaces\ResultInterface;
use App\Repo\Interfaces\RoleInterface;
use App\Repo\Interfaces\StudentInterface;
use App\Repo\Interfaces\TopicAreaInterface;
use App\Repo\Interfaces\UserInterface;
use App\Repo\LanguageClass;
use App\Repo\QuestionClass;
use App\Repo\ResultClass;
use App\Repo\RoleClass;
use App\Repo\StudentClass;
use App\Repo\TopicAreaClass;
use App\Repo\UserClass;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(LanguageInterface::class,LanguageClass::class);
        $this->app->bind(CategoryInterface::class,CategoryClass::class);
        $this->app->bind(UserInterface::class,UserClass::class);
        $this->app->bind(TopicAreaInterface::class,TopicAreaClass::class);
        $this->app->bind(RoleInterface::class,RoleClass::class);
        $this->app->bind(ConfigurationInterface::class,ConfigurationClass::class);
        $this->app->bind(StudentInterface::class,StudentClass::class);
        $this->app->bind(ExamInterface::class,ExamClass::class);
        $this->app->bind(QuestionInterface::class,QuestionClass::class);
        $this->app->bind(ResultInterface::class,ResultClass::class);
        $this->app->bind(CourseInterface::class,CourseClass::class);
        $this->app->bind(BranchInterface::class,BranchClass::class);

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
