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
Route::get('/category/question/{category}', [UserCategoryQuestionController::class, 'index'])->name('category.question.index');

Route::get('/question/random', [UserQuestionController::class, 'getRandomQuestion'])->name('question.random.get');
Route::get('/question/add_category_to_user', [UserQuestionController::class, 'add_category_to_user'])->name('question.add_category_to_user');
Route::get('/question/remove_category_from_user', [UserQuestionController::class, 'remove_category_from_user'])->name('question.remove_category_from_user');


// user profile
Route::get('/profile', [UserProfileController::class, 'index'])->name('profile.index');


//user learning
Route::get('/learning/chooseCategory', [UserLearningNewController::class, 'chooseCategory'])->name('user.learning.new.chooseCategory');
Route::post('/learning/start', [UserLearningNewController::class, 'start'])->name('user.learning.new.start');
Route::get('/learning/setting', [UserLearningNewController::class, 'setting'])->name('user.learning.new.setting');




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

