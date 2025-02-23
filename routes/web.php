<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\SeedController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\UserHomeController;
use App\Http\Controllers\Quiz\OnlineQuizController;
use App\Http\Controllers\Desktop\QuizListController;
use App\Http\Controllers\Auth\OTP\LoginOTPController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Desktop\myProgressController;
use App\Http\Controllers\Admin\AdminQuestionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\OTP\RegisterOTPController;
use App\Http\Controllers\User\UserLearningNewController;
use App\Http\Controllers\Desktop\DesktopStudentController;
use App\Http\Controllers\Quiz\CreateQuizStudentController;
use App\Http\Controllers\Auth\OTP\LoginTwoFactorController;
use App\Http\Controllers\Auth\OTP\ResetPasswordOTPController;
use App\Http\Controllers\Auth\OTP\ForgotPasswordOTPController;
use App\Http\Controllers\Quiz\QuizChooseCategoriesStudentController;
use App\Http\Controllers\User\Category\UserCategoryQuestionController;
use App\Http\Controllers\Admin\Category\AdminCategoryQuestionController;
use App\Http\Controllers\Auth\OTP\Desktop\DesktopSettingEmailController;
use App\Http\Controllers\Auth\OTP\Desktop\DesktopSettingMobileController;
use App\Http\Controllers\Auth\OTP\Desktop\DesktopSettingTwoFactorController;









Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [LoginController::class, 'ShowloginForm'])->name('login.form'); 
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('otp/login/two-factor/code', [LoginTwoFactorController::class, 'showEnterCodeForm'])->name('otp.login.two.factor.code.form');
    Route::post('otp/login/two-factor/code', [LoginTwoFactorController::class, 'confirmCode'])->name('otp.login.two.factor.code');
    Route::get('otp/login/two-factor/resend', [LoginTwoFactorController::class, 'resend'])->name('otp.login.two.factor.resend');
    Route::get('otp/login', [LoginOTPController::class, 'showOTPForm'])->name('otp.login.form');
    Route::post('otp/login', [LoginOTPController::class, 'sendToken'])->name('otp.login.send.token');
    Route::get('otp/login/code', [LoginOTPController::class, 'showEnterCodeForm'])->name('otp.login.code.form');    
    Route::post('otp/login/code', [LoginOTPController::class, 'confirmCode'])->name('otp.login.code');
    Route::get('otp/login/resend', [LoginOTPController::class, 'resend'])->name('otp.login.resend');    
    Route::get('register', [RegisterController::class, 'ShowRegisterationForm'])->name('register.form');
    Route::post('register', [RegisterController::class, 'Register'])->name('register');
    Route::get('otp/register', [RegisterOTPController::class, 'showOTPForm'])->name('otp.register.form');
    Route::post('otp/register', [RegisterOTPController::class, 'sendToken'])->name('otp.register.send.token');
    Route::get('otp/register/code', [RegisterOTPController::class, 'showEnterCodeForm'])->name('otp.register.code.form');
    Route::post('otp/register/code', [RegisterOTPController::class, 'confirmCode'])->name('otp.register.code');
    Route::get('otp/register/resend', [RegisterOTPController::class, 'resend'])->name('otp.register.resend');
    Route::get('redirect/{provider}', [SocialController::class, 'RredirectToProvider'])->name('login.provider.redirect');
    Route::get('{provider}/callback', [SocialController::class, 'providerCallback'])->name('login.provider.callback');
    Route::get('password/forget', [ForgotPasswordController::class, 'showForgetForm'])->name('password.forget.form');
    Route::post('password/forget', [ForgotPasswordController::class, 'sendResetLink'])->name('password.forget');
    Route::get('otp/password/forget', [ForgotPasswordOTPController::class, 'showOTPForm'])->name('otp.password.forget.form');
    Route::post('otp/password/forget', [ForgotPasswordOTPController::class, 'sendToken'])->name('otp.password.send.token');
    Route::get('password/reset', [ResetPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');
    Route::get('otp/password/reset', [ResetPasswordOTPController::class, 'showEnterCodeForm'])->name('otp.password.code.form');
    Route::post('otp/password/reset', [ResetPasswordOTPController::class, 'confirmCode'])->name('otp.password.code');
    Route::get('otp/password/resend', [ResetPasswordOTPController::class, 'resend'])->name('otp.password.resend');
    Route::get('email/send-verification', [VerificationController::class, 'send'])->name('email.send.verification');
    Route::get('email/verify', [VerificationController::class, 'verify'])->name('email.verify');
    Route::get('otp/desktop/setting/two-factor/toggle', [DesktopSettingTwoFactorController::class, 'showToggleForm'])->name('otp.desktop.setting.two.factor.toggle.form');
    Route::get('otp/desktop/setting/two-factor/activateByEmail', [DesktopSettingTwoFactorController::class, 'sendTokenForEmail'])->name('otp.desktop.setting.two.factor.sendTokenForEmail');
    Route::get('otp/desktop/setting/two-factor/activateByMobile', [DesktopSettingTwoFactorController::class, 'sendTokenForMobile'])->name('otp.desktop.setting.two.factor.sendTokenForMobile');
    Route::get('otp/desktop/setting/two-factor/code', [DesktopSettingTwoFactorController::class, 'showEnterCodeForm'])->name('otp.desktop.setting.two.factor.code.form');
    Route::post('otp/desktop/setting/two-factor/code', [DesktopSettingTwoFactorController::class, 'confirmCode'])->name('otp.desktop.setting.two.factor.code');
    Route::get('otp/desktop/setting/two-factor/resend', [DesktopSettingTwoFactorController::class, 'resend'])->name('otp.desktop.setting.two.factor.resend');
    Route::get('otp/desktop/setting/two-factor/deactivate', [DesktopSettingTwoFactorController::class, 'deactivate'])->name('otp.desktop.setting.two.factor.deactivate');
    Route::get('otp/desktop/setting/mobile', [DesktopSettingMobileController::class, 'showOTPForm'])->name('otp.desktop.setting.mobile.form');
    Route::post('otp/desktop/setting/mobile', [DesktopSettingMobileController::class, 'add'])->name('otp.desktop.setting.mobile');
    Route::get('otp/desktop/setting/mobile/code', [DesktopSettingMobileController::class, 'showEnterCodeForm'])->name('otp.desktop.setting.mobile.code.form');
    Route::post('otp/desktop/setting/mobile/code', [DesktopSettingMobileController::class, 'confirmCode'])->name('otp.desktop.setting.mobile.code');
    Route::get('otp/desktop/setting/mobile/resend', [DesktopSettingMobileController::class, 'resend'])->name('otp.desktop.setting.mobile.resend');    
    Route::get('otp/desktop/setting/email', [DesktopSettingEmailController::class, 'showOTPForm'])->name('otp.desktop.setting.email.form');
    Route::post('otp/desktop/setting/email', [DesktopSettingEmailController::class, 'add'])->name('otp.desktop.setting.email');
    Route::get('otp/desktop/setting/email/code', [DesktopSettingEmailController::class, 'showEnterCodeForm'])->name('otp.desktop.setting.email.code.form');
    Route::post('otp/desktop/setting/email/code', [DesktopSettingEmailController::class, 'confirmCode'])->name('otp.desktop.setting.email.code');
    Route::get('otp/desktop/setting/email/resend', [DesktopSettingEmailController::class, 'resend'])->name('otp.desktop.setting.email.resend');
});





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
Route::get('/desktop/myProgress/{user}', [myProgressController::class, 'myProgress'])->name('desktop.myProgress');
Route::post('/desktop/getChartResult', [myProgressController::class, 'getChartResult'])->name('desktop.getChartResult');

Route::get('/desktop/setting/setting', [DesktopStudentController::class, 'setting'])->name('desktop.setting.setting');






Route::get('/', [UserHomeController::class, 'index'])->name('home');
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

Route::get('/test/emailTest', [TestController::class, 'emailTest']);
Route::get('/test/smsTest', [TestController::class, 'smsTest']);

Route::get('/test/logout', [TestController::class, 'logout']);
