<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\SeedController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\User\UserHomeController;
use App\Http\Controllers\User\UserLearningNewController;
use App\Http\Controllers\User\UserQuestionController;
use App\Http\Controllers\Admin\AdminQuestionController;
use App\Http\Controllers\User\Profile\UserProfileController;
use App\Http\Controllers\User\Category\UserCategoryQuestionController;
use App\Http\Controllers\Admin\Category\AdminCategoryQuestionController;



Route::get('/', [UserHomeController::class, 'index']);
Route::post('/categoryQuestion/randomFreeQuestion', [UserCategoryQuestionController::class, 'getRandomFreeQuestion'])->name('user.categoryQuestion.randomFreeQuestion.get');





Route::get('/categoryQuestion/index/{currentCategory}', [UserCategoryQuestionController::class, 'index'])->name('user.categoryQuestion.index');
Route::post('/categoryQuestion/add_category_to_user', [UserCategoryQuestionController::class, 'addCategoryToUser'])->name('user.categoryQuestion.add_category_to_user');
Route::post('/categoryQuestion/remove_category_from_user', [UserCategoryQuestionController::class, 'removeCategoryFromUser'])->name('user.categoryQuestion.remove_category_from_user');


// user profile
Route::get('/profile', [UserProfileController::class, 'index'])->name('profile.index');
Route::get('/profile/chooseCategoryForLearning', [UserProfileController::class, 'chooseCategoryForLearning'])->name('user.profile.new.chooseCategoryForLearning');
Route::get('/profile/quizList', [UserProfileController::class, 'quizList'])->name('user.profile.quizList');
Route::get('/profile/myProgress', [UserProfileController::class, 'myProgress'])->name('user.profile.myProgress');
Route::post('/profile/getChartResult', [UserProfileController::class, 'getChartResult'])->name('user.profile.getChartResult');


//user learning
Route::post('/learning/start', [UserLearningNewController::class, 'start'])->name('user.learning.new.start');

Route::get('/learning/onlineQuizInProgress/{quiz}', [UserLearningNewController::class, 'onlineQuizInProgress'])->name('user.learning.onlineQuizInProgress');
Route::post('/learning/quizInProgress/showAnswer', [UserLearningNewController::class, 'showAnswer'])->name('user.learning.quizInProgress.showAnswer');
Route::post('/learning/quizInProgress/nextQuestion', [UserLearningNewController::class, 'nextQuestion'])->name('user.learning.quizInProgress.nextQuestion');
Route::post('/learning/quizInProgress/prevQuestion', [UserLearningNewController::class, 'prevQuestion'])->name('user.learning.quizInProgress.prevQuestion');
Route::get('/learning/saveQuizDataAndShowResult/{quiz}', [UserLearningNewController::class, 'saveQuizDataAndShowResult'])->name('learning.saveQuizDataAndShowResult');





// admin category question

Route::get('/admin/category/question/list/{category}', [AdminCategoryQuestionController::class, 'index'])->name('admin.category.question.index');
Route::get('/admin/category/question/create', [AdminCategoryQuestionController::class, 'create'])->name('admin.category.question.create');
Route::post('/admin/category/question/create', [AdminCategoryQuestionController::class, 'store'])->name('admin.category.question.store');
Route::get('/admin/category/question/edit/{currentCategory}', [AdminCategoryQuestionController::class, 'edit'])->name('admin.category.question.edit');
Route::post('/admin/category/question/update/{currentCategory}', [AdminCategoryQuestionController::class, 'update'])->name('admin.category.question.update');
Route::get('/admin/category/question/delete/{currentCategory}', [AdminCategoryQuestionController::class, 'delete'])->name('admin.category.question.delete');

// admin question

Route::get('/admin/question/create', [AdminQuestionController::class, 'create'])->name('admin.question.create');
Route::post('/admin/question/create', [AdminCategoryQuestionController::class, 'store'])->name('admin.question.store');




//Seeder
Route::get('/seeder/index', [SeedController::class, 'index']);
Route::get('/seeder/User', [SeedController::class, 'createUser']);
Route::get('/seeder/CategoryQuestion', [SeedController::class, 'createCategoryQuestion']);
Route::get('/seeder/Question', [SeedController::class, 'createQuestion']);
Route::get('/seeder/assignCategoryToUser', [SeedController::class, 'assignCategoryToUser']);


//Test
Route::get('/test/index', [TestController::class, 'index']);
Route::get('/learning/test/{userAnswer}/{questionId}', [UserLearningNewController::class, 'changeQuestionAndUserCategoryQuestion']);

