<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\SeedController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\User\UserHomeController;
use App\Http\Controllers\Quiz\OnlineQuizController;
use App\Http\Controllers\Desktop\QuizListController;
use App\Http\Controllers\Admin\AdminQuestionController;
use App\Http\Controllers\User\UserLearningNewController;
use App\Http\Controllers\Desktop\DesktopStudentController;
use App\Http\Controllers\Quiz\CreateQuizStudentController;
use App\Http\Controllers\Quiz\QuizChooseCategoriesStudentController;
use App\Http\Controllers\User\Category\UserCategoryQuestionController;
use App\Http\Controllers\Admin\Category\AdminCategoryQuestionController;




//Quiz
Route::get('/quiz/chooseCategories/student', [QuizChooseCategoriesStudentController::class, 'chooseCategories'])->name('quiz.chooseCategories.student');
Route::post('/quiz/create/student', [CreateQuizStudentController::class, 'create'])->name('quiz.create.student');

Route::get('/quiz/online/{quiz}', [OnlineQuizController::class, 'onlineQuizInProgress'])->name('quiz.online.onlineQuizInProgress');
Route::post('/quiz/online/showAnswer', [OnlineQuizController::class, 'showAnswer'])->name('quiz.online.showAnswer');
Route::post('/quiz/online/nextQuestion', [OnlineQuizController::class, 'nextQuestion'])->name('quiz.online.nextQuestion');
Route::post('/quiz/online/prevQuestion', [OnlineQuizController::class, 'prevQuestion'])->name('quiz.online.prevQuestion');

Route::get('/quiz/result/{quiz}', [OnlineQuizController::class, 'saveOnlineQuizDataAndShowResult'])->name('quiz.online.saveOnlineQuizDataAndShowResult');



Route::get('/desktop/student', [DesktopStudentController::class, 'index'])->name('desktop.student.index');


Route::get('/desktop/quizList/{user}', [QuizListController::class, 'quizList'])->name('desktop.quizList');

Route::get('/desktop/myProgress/{user}', [DesktopStudentController::class, 'myProgress'])->name('desktop.myProgress');

Route::post('/desktop/getChartResult/{user}', [DesktopStudentController::class, 'getChartResult'])->name('desktop.getChartResult');







Route::get('/', [UserHomeController::class, 'index']);
Route::post('/categoryQuestion/randomFreeQuestion', [UserCategoryQuestionController::class, 'getRandomFreeQuestion'])->name('user.categoryQuestion.randomFreeQuestion.get');





Route::get('/categoryQuestion/index/{currentCategory}', [UserCategoryQuestionController::class, 'index'])->name('user.categoryQuestion.index');
Route::post('/categoryQuestion/add_category_to_user', [UserCategoryQuestionController::class, 'addCategoryToUser'])->name('user.categoryQuestion.add_category_to_user');
Route::post('/categoryQuestion/remove_category_from_user', [UserCategoryQuestionController::class, 'removeCategoryFromUser'])->name('user.categoryQuestion.remove_category_from_user');


// user profile


//user learning






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

