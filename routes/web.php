<?php



use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeedController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Test2Controller;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Chat\GroupController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Chat\MessageController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Category\BookController;
use App\Http\Controllers\Chat\ReactionController;
use App\Http\Controllers\Question\VoteController;
use App\Http\Controllers\User\UserHomeController;
use App\Http\Controllers\Category\JozveController;
use App\Http\Controllers\Chat\AttachmentController;
use App\Http\Controllers\Quiz\OnlineQuizController;
use App\Http\Controllers\Category\FreeCatController;
use App\Http\Controllers\Chat\ParticipantController;
use App\Http\Controllers\Chat\PrivateChatController;
use App\Http\Controllers\Desktop\QuizListController;
use App\Http\Controllers\Question\CommentController;
use App\Http\Controllers\SeedCategoryBookController;
use App\Http\Controllers\Auth\OTP\LoginOTPController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Category\FreeFileController;
use App\Http\Controllers\Chat\ConversationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Desktop\myProgressController;
use App\Http\Controllers\Question\BestReplyController;
use App\Http\Controllers\Admin\AdminQuestionController;
use App\Http\Controllers\Admin\AdminSiteInfoController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\OTP\RegisterOTPController;
use App\Http\Controllers\User\UserLearningNewController;
use App\Http\Controllers\Category\CategoryBookController;
use App\Http\Controllers\Category\CategoryExamController;
use App\Http\Controllers\Category\CategoryFreeController;
use App\Http\Controllers\Question\QuestionVoteController;
use App\Http\Controllers\Auth\DesktopChangeNameController;
use App\Http\Controllers\Category\CategoryJozveController;
use App\Http\Controllers\Desktop\DesktopStudentController;
use App\Http\Controllers\Profile\ProfileStudentController;
use App\Http\Controllers\Quiz\CreateQuizStudentController;
use App\Http\Controllers\Auth\OTP\LoginTwoFactorController;
use App\Http\Controllers\Upload\CkEditorUploaderController;
use App\Http\Controllers\Admin\Import\AdminImportController;
use App\Http\Controllers\Profile\UserRelationshipController;
use App\Http\Controllers\Question\QuestionCommentController;
use App\Http\Controllers\Auth\OTP\ResetPasswordOTPController;
use App\Http\Controllers\Category\CategoryGambeGamController;
use App\Http\Controllers\FreeQuestion\FreeQuestionController;
use App\Http\Controllers\Admin\Desktop\AdminDesktopController;
use App\Http\Controllers\Auth\OTP\ForgotPasswordOTPController;
use App\Http\Controllers\Question\QuestionBestReplyController;
use App\Http\Controllers\Admin\Import\AdminImportNewController;
use App\Http\Controllers\Admin\Import\DatabaseExportController;
use App\Http\Controllers\Admin\Category\AdminCategoryController;
use App\Http\Controllers\Admin\Import\AdminImportBookController;
use App\Http\Controllers\FreeQuestion\FreeQuestionNewController;
use App\Http\Controllers\Category\CategoryQuestionUserController;
use App\Http\Controllers\FreeQuestion\FreeQuestionEditController;
use App\Http\Controllers\FreeQuestion\FreeQuestionVoteController;
use App\Http\Controllers\Admin\AdminQuestionDescriptiveController;
use App\Http\Controllers\Admin\Import\AdminImportCategoryController;
use App\Http\Controllers\FreeQuestion\FreeQuestionCommentController;
use App\Http\Controllers\Quiz\QuizChooseCategoriesStudentController;
use App\Http\Controllers\Admin\Import\AdminImportKanoonSoalController;
use App\Http\Controllers\FreeQuestion\FreeQuestionBestReplyController;
use App\Http\Controllers\FreeQuestion\FreeQuestionCommentNewController;
use App\Http\Controllers\Admin\Category\AdminCategoryQuestionController;
use App\Http\Controllers\Auth\OTP\Desktop\DesktopSettingEmailController;
use App\Http\Controllers\FreeQuestion\FreeQuestionCommentEditController;
use App\Http\Controllers\FreeQuestion\FreeQuestionCommentVoteController;
use App\Http\Controllers\Auth\OTP\Desktop\DesktopSettingMobileController;
use App\Http\Controllers\Admin\Import\AdminImportKanoonCategoryController;
use App\Http\Controllers\Auth\OTP\Desktop\DesktopSettingTwoFactorController;
use App\Http\Controllers\Admin\Import\AdminImportGambeGamCategoryPadarsFirstLevelController;
use App\Http\Controllers\Admin\Import\AdminImportGambeGamCategoryPadarsForthLevelController;
use App\Http\Controllers\Admin\Import\AdminImportGambeGamCategoryPadarsThirdLevelController;
use App\Http\Controllers\Admin\Import\AdminImportGambeGamCategoryPadarsSecondLevelController;

Route::get('/', [UserHomeController::class, 'index'])->name('home');



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
    
    Route::get('/desktop/setting/changeName', [DesktopChangeNameController::class, 'changeNameForm'])->name('desktop.setting.changeNameForm');
    Route::post('/desktop/setting/changeName', [DesktopChangeNameController::class, 'changeName'])->name('desktop.setting.changeName');

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



//categoryQuestion
Route::post('/category/categoryQuestion/user/randomFreeQuestion', [CategoryQuestionUserController::class, 'getRandomFreeQuestion'])->name('category.categoryQuestion.user.randomFreeQuestion.get');
Route::get('/category/categoryQuestion/user/index/{currentCategory}', [CategoryQuestionUserController::class, 'index'])->name('category.categoryQuestion.user.index');
Route::post('/category/categoryQuestion/user/add_category_to_user', [CategoryQuestionUserController::class, 'addCategoryToUser'])->middleware('auth')->name('category.categoryQuestion.user.add_category_to_user');
Route::post('/category/categoryQuestion/user/remove_category_from_user', [CategoryQuestionUserController::class, 'removeCategoryFromUser'])->middleware('auth')->name('category.categoryQuestion.user.remove_category_from_user');


//Quiz
Route::get('/quiz/chooseCategories/student', [QuizChooseCategoriesStudentController::class, 'chooseCategories'])->middleware('auth')->name('quiz.chooseCategories.student');
Route::post('/quiz/chooseCategories/getChildren', [QuizChooseCategoriesStudentController::class, 'getChildren'])->middleware('auth')->name('quiz.chooseCategories.getChildren');
Route::post('/quiz/create/student', [CreateQuizStudentController::class, 'create'])->middleware('auth')->name('quiz.create.student');
Route::get('/quiz/chooseCategories/student/clearCache', [QuizChooseCategoriesStudentController::class, 'clearCache'])->middleware('auth')->name('quiz.chooseCategories.student.clearCache');
Route::get('/quiz/online/{quiz}', [OnlineQuizController::class, 'onlineQuizInProgress'])->middleware('auth')->name('quiz.online.onlineQuizInProgress');
Route::post('/quiz/online/showAnswer', [OnlineQuizController::class, 'showAnswer'])->middleware('auth')->name('quiz.online.showAnswer');
Route::post('/quiz/online/nextQuestion', [OnlineQuizController::class, 'nextQuestion'])->middleware('auth')->name('quiz.online.nextQuestion');
Route::post('/quiz/online/prevQuestion', [OnlineQuizController::class, 'prevQuestion'])->middleware('auth')->name('quiz.online.prevQuestion');
Route::get('/quiz/result/{quiz}', [OnlineQuizController::class, 'saveOnlineQuizDataAndShowResult'])->middleware('auth')->name('quiz.online.saveOnlineQuizDataAndShowResult');

//desktop
Route::get('/desktop/student', [DesktopStudentController::class, 'index'])->middleware('auth')->name('desktop.student.index');
Route::get('/desktop/quizList/{user}', [QuizListController::class, 'quizList'])->middleware('auth')->name('desktop.quizList');
Route::get('/desktop/myProgress/{user}', [myProgressController::class, 'myProgress'])->middleware('auth')->name('desktop.myProgress');
Route::post('/desktop/getChartResult', [myProgressController::class, 'getChartResult'])->middleware('auth')->name('desktop.getChartResult');
Route::get('/desktop/setting/setting', [DesktopStudentController::class, 'setting'])->middleware('auth')->name('desktop.setting.setting');

//question comment            
Route::post('/question/comment/newComments', [QuestionCommentController::class, 'newComments'])->middleware('auth')->name('question.comment.newComments');
Route::post('/question/comment/vote', [QuestionVoteController::class, 'vote'])->middleware('auth')->name('question.comment.vote');
Route::post('/question/comment/best-reply', [QuestionBestReplyController::class, 'setBestReply'])->middleware('auth')->name('question.comment.best-reply');
Route::post('/question/comment/fetchComments', [QuestionCommentController::class, 'fetchComments'])->name('question.comment.fetchComments');

//freeQuestion
Route::get('/freeQuestion/index', [FreeQuestionController::class, 'index'])->name('freeQuestion.index');
Route::post('/freeQuestion/fetchFreeQuestions', [FreeQuestionController::class, 'fetchFreeQuestions'])->name('freeQuestion.fetchFreeQuestions');
Route::post('/freeQuestion/newQuestion', [FreeQuestionNewController::class, 'newQuestion'])->middleware('auth')->name('freeQuestion.newQuestion');
Route::post('/freeQuestion/freeQuestion/vote', [FreeQuestionVoteController::class, 'vote'])->middleware('auth')->name('freeQuestion.freeQuestion.vote');
Route::post('/freeQuestion/{question}/update', [FreeQuestionEditController::class, 'update'])->middleware('auth')->name('freeQuestion.edit.update');
Route::delete('/free-question/{question}', [FreeQuestionEditController::class, 'destroy'])->middleware('only.user.one')->name('freeQuestion.edit.destroy');

//freeQuestion comment            
Route::get('/freeQuestion/show/{id}', [FreeQuestionCommentController::class, 'show'])->name('freeQuestion.show');
Route::post('/freeQuestion/fetchComments', [FreeQuestionCommentController::class, 'fetchComments'])->name('freeQuestion.fetchComments');
Route::post('/freeQuestion/comment/newComments', [FreeQuestionCommentNewController::class, 'newComment'])->middleware('auth')->name('freeQuestion.comment.newComment');
Route::post('/freeQuestion/freeQuestion/comment/vote', [FreeQuestionCommentVoteController::class, 'vote'])->middleware('auth')->name('freeQuestion.freeQuestion.comment.vote');
Route::post('/freeQuestion/best-reply', [FreeQuestionBestReplyController::class, 'setBestReply'])->middleware('auth')->name('freeQuestion.best-reply');
Route::post('/freeQuestion/comment/{comment}/update', [FreeQuestionCommentEditController::class, 'update'])->middleware('auth')->name('freeQuestion.comment.update');
Route::delete('/freeQuestion/comment/{comment}', [FreeQuestionCommentEditController::class, 'destroy'])->middleware('only.user.one')->name('freeQuestion.comment.destroy');


//upload Images
Route::post('/upload-image', [CkEditorUploaderController::class, 'upload'])->middleware('auth')->name('ckeditor.upload');
Route::get('/ckeditor/file/{any}', [CkEditorUploaderController::class, 'urlMaker'])->where('any', '.*')->name('ckeditor.urlMaker');


// madrese books
Route::get('/categories/categoryBook/index', [CategoryBookController::class, 'index'])->name('category.categoryBook.index');
Route::get('/categories/categoryBook/children/{parentId}', [CategoryBookController::class, 'getChildren'])->name('category.categoryBook.getChildren');
Route::match(['get', 'post'], '/categories/categoryBook/getBooks', [CategoryBookController::class, 'getBooks'])->name('category.categoryBook.getBooks'); 
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// gam be gam
Route::get('/categories/categoryGamBeGam/index', [CategoryGambeGamController::class, 'index'])->name('category.categoryGamBeGam.index');
Route::match(['get', 'post'], '/categories/categoryGambeGam/gams', [CategoryGambeGamController::class, 'getGambeGams'])->name('category.categoryGambeGam.getGambeGams');
Route::get('/categories/categoryGambeGam/children/{parentId}', [CategoryGambeGamController::class, 'getChildren'])->name('category.getGambeGams.getChildren');
 
//exam   

Route::get('/categories/categoryExam/index', [CategoryExamController::class, 'index'])->name('category.categoryExam.index');
Route::match(['get', 'post'], '/categories/categoryExam/exams', [CategoryExamController::class, 'getExam'])->name('category.categoryExam.getExam');
Route::get('/categories/categoryExam/children/{parentId}', [CategoryExamController::class, 'getChildren'])->name('category.categoryExam.getChildren');

//jozve

Route::get('/categories/categoryJozve/index', [CategoryJozveController::class, 'index'])->name('category.categoryJozve.index');
Route::match(['get', 'post'], '/categories/categoryJozve/jozves', [CategoryJozveController::class, 'getJozve'])->name('category.categoryJozve.getJozve');
Route::get('/categories/categoryJozve/children/{parentId}', [CategoryJozveController::class, 'getChildren'])->name('category.categoryJozve.getChildren');
Route::post('/jozves', [JozveController::class, 'store'])->middleware('auth')->name('jozves.store');
Route::get('/jozve/download/{jozve}', [JozveController::class, 'download'])->name('jozve.download');

//freeCat

Route::get('/categories/categoryFree/index', [CategoryFreeController::class, 'index'])->name('category.categoryFree.index');
Route::match(['get', 'post'], '/categories/categoryFree/freeFile', [CategoryFreeController::class, 'getFreeFile'])->name('category.categoryFree.getFreeFile');
Route::get('/categories/categoryFree/children/{parentId}', [CategoryFreeController::class, 'getChildren'])->name('category.categoryFree.getChildren');
Route::post('/freeFile', [FreeFileController::class, 'store'])->middleware('auth')->name('freeFile.store');
Route::get('/freeFile/download/{freeFile}', [FreeFileController::class, 'download'])->name('freeFile.download');



//chat
Broadcast::routes(['middleware' => ['auth']]);

Route::prefix('chat')->name('chat.')->middleware('auth')->group(function () {

    // General Chat Page
    Route::get('/', [ChatController::class, 'index'])->name('index'); 
    Route::get('create', [ChatController::class, 'create'])->name('create');  
    Route::get('search-entities', [ChatController::class, 'searchEntities'])->name('search-entities');  
    Route::post('start-conversation', [ChatController::class, 'startConversation'])->name('startConversation');
    Route::post('toggle-ban-user', [ChatController::class, 'toggleBanUser'])->name('toggle-ban-user');
    
    Route::post('start-private-from-profile', [PrivateChatController::class, 'start'])->name('start-private-from-profile');
    Route::get('saved-messages', [PrivateChatController::class, 'savedMessages'])->name('saved-messages');



    Route::get('groups/create-group', [GroupController::class, 'create'])->name('groups.create');
    Route::post('groups/create-group', [GroupController::class, 'store'])->name('groups.store');
    Route::get('groups/{conversation}/add-users', [GroupController::class, 'addUsersForm'])->name('groups.add-users');
    Route::post('groups/{conversation}/add-users', [GroupController::class, 'addUsers'])->name('groups.add-users.store');
    Route::get('groups/search-users', [GroupController::class, 'searchUsers'])->name('groups.search-users');

    Route::get('groups/{conversation}/search-users-after-creation-form', [GroupController::class, 'searchUsersAfterCreationForm'])->name('groups.search-users-after-creation-form');
    Route::get('groups/{conversation}/search-users-after-creation', [GroupController::class, 'searchUsersAfterCreation'])->name('groups.search-users-after-creation');



    Route::get('groups/{conversation}/info', [GroupController::class, 'info'])->name('groups.info');
    Route::post('groups/{conversation}/update-info', [GroupController::class, 'updateInfo'])->name('groups.updateInfo');

    
    // Participants
    Route::post('conversations/{conversation}/join', [ParticipantController::class, 'join'])->name('conversation.participants.join');  
    
    // Manage Participants Page
    Route::get('conversations/{conversation}/manage-users', [ParticipantController::class, 'manage'])->name('participants.manage');
    Route::get('conversations/{conversation}/search-participants', [ParticipantController::class, 'searchParticipants'])->name('participants.search');
    // Promote / Demote Admin
    Route::post('conversations/{conversation}/promote/{user}', [ParticipantController::class, 'promote'])->name('participants.promote');
    Route::post('conversations/{conversation}/demote/{user}', [ParticipantController::class, 'demote'])->name('participants.demote');

    // Mute / Unmute
    Route::post('conversations/{conversation}/mute/{user}', [ParticipantController::class, 'mute'])->name('participants.mute');
    Route::post('conversations/{conversation}/unmute/{user}', [ParticipantController::class, 'unmute'])->name('participants.unmute');

    // Ban / Unban
    Route::post('conversations/{conversation}/ban/{user}', [ParticipantController::class, 'ban'])->name('participants.ban');
    Route::post('conversations/{conversation}/unban/{user}', [ParticipantController::class, 'unban'])->name('participants.unban');
    
    // Messages
    Route::get('{slug}', [MessageController::class, 'accessBySlug'])->name('slug.slug.access');
    Route::get('conversations/{conversation}/messages', [MessageController::class, 'index'])->name('messages.index'); 
    Route::get('conversations/{conversation}/getMessages', [MessageController::class, 'getMessages'])->name('messages.getMessages'); 
    Route::post('conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');  
    Route::put('messages/{message}', [MessageController::class, 'update'])->name('messages.update');
    Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');  
    Route::post('/conversations/{conversation}/read', [MessageController::class, 'markAsRead']);




    // Attachments
    // Route::post('messages/{message}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download'); 
    Route::get('/attachments/{attachment}/view', [AttachmentController::class, 'serveAttachment'])->name('attachments.view'); // Give it a name for easy URL generation

    // Reactions
    Route::post('messages/{message}/reactions', [ReactionController::class, 'store'])->name('reactions.store');
    Route::get('messages/{message}/reactions', [ReactionController::class, 'index'])->name('reactions.index');
});


















Route::get('/profile/student/{user}', [ProfileStudentController::class, 'index'])->middleware('auth')->name('profile.student.index');
Route::post('/profile/avatar', [ProfileStudentController::class, 'uploadAvatar'])->name('profile.avatar.upload');

Route::middleware('auth')->prefix('supervising/')->name('supervising.')->group(function () {
    Route::post('send', [UserRelationshipController::class, 'sendRequest'])->name('send');
    Route::post('accept/{id}', [UserRelationshipController::class, 'accept'])->name('accept');
    Route::post('decline/{id}', [UserRelationshipController::class, 'decline'])->name('decline');
    Route::delete('cancel/{id}', [UserRelationshipController::class, 'cancel'])->name('cancel');
    Route::delete('remove/{id}', [UserRelationshipController::class, 'removeRelationship'])->name('remove');
});







Route::middleware('auth', 'only.user.one')->group(function () {


    //admin Panel

    Route::get('/admin', [AdminDesktopController::class, 'index'])->name('admin.home');


    // admin category question`
    // 
    Route::get('/admin/category/categoryQuestion/list/{category}', [AdminCategoryQuestionController::class, 'index'])->name('admin.category.categoryQuestion.index');
    Route::get('/admin/category/categoryQuestion/create', [AdminCategoryQuestionController::class, 'create'])->name('admin.category.categoryQuestion.create');
    Route::post('/admin/category/categoryQuestion/create', [AdminCategoryQuestionController::class, 'store'])->name('admin.category.categoryQuestion.store');

    Route::get('/admin/category/categoryQuestion/createSubCat/{categorySelect}', [AdminCategoryQuestionController::class, 'createSubCat'])->name('admin.category.categoryQuestion.createSubCat');

    Route::get('/admin/category/categoryQuestion/edit/{currentCategory}', [AdminCategoryQuestionController::class, 'edit'])->name('admin.category.categoryQuestion.edit');
    Route::post('/admin/category/categoryQuestion/update/{currentCategory}', [AdminCategoryQuestionController::class, 'update'])->name('admin.category.categoryQuestion.update');
    Route::get('/admin/category/categoryQuestion/delete/{currentCategory}', [AdminCategoryQuestionController::class, 'delete'])->name('admin.category.categoryQuestion.delete');

    // admin question test

    Route::get('/admin/question/list/{category}', [AdminQuestionController::class, 'index'])->name('admin.question.index');
    Route::get('/admin/question/show/{question}', [AdminQuestionController::class, 'show'])->name('admin.question.show');
    Route::get('/admin/question/deActiveQuestion', [AdminQuestionController::class, 'deActiveQuestion'])->name('admin.question.deActiveQuestion');
    Route::get('/admin/question/create', [AdminQuestionController::class, 'create'])->name('admin.question.create');
    Route::post('/admin/question/create', [AdminQuestionController::class, 'store'])->name('admin.question.store');
    Route::get('/admin/question/edit/{question}', [AdminQuestionController::class, 'edit'])->name('admin.question.edit');
    Route::post('/admin/question/update/{question}', [AdminQuestionController::class, 'update'])->name('admin.question.update');
    Route::get('/admin/question/delete/{question}', [AdminQuestionController::class, 'delete'])->name('admin.question.delete');
    Route::get('/admin/question/updatelevel/{question}/{percentage}', [AdminQuestionController::class, 'updatelevel'])->name('admin.question.updatelevel');
    Route::post('/admin/question/updatelevel', [AdminQuestionController::class, 'updatelevelPost'])->name('admin.question.updatelevel.post');


    // admin question descriptive

    Route::get('/admin/question/descriptive/create', [AdminQuestionDescriptiveController::class, 'create'])->name('admin.question.descriptive.create');
    Route::post('/admin/question/descriptive/create', [AdminQuestionDescriptiveController::class, 'store'])->name('admin.question.descriptive.store');
    Route::get('/admin/question/descriptive/edit/{question}', [AdminQuestionDescriptiveController::class, 'edit'])->name('admin.question.descriptive.edit');
    Route::post('/admin/question/descriptive/update/{question}', [AdminQuestionDescriptiveController::class, 'update'])->name('admin.question.descriptive.update');



    // admin category

    Route::get('/admin/category/list/{category}', [AdminCategoryController::class, 'index'])->name('admin.category.category.index');
    Route::get('/admin/category/create', [AdminCategoryController::class, 'create'])->name('admin.category.category.create');
    Route::post('/admin/category/create', [AdminCategoryController::class, 'store'])->name('admin.category.category.store');
    Route::get('/admin/category/category/createSubCat/{categorySelect}', [AdminCategoryController::class, 'createSubCat'])->name('admin.category.category.createSubCat');
    Route::get('/admin/category/category/edit/{currentCategory}', [AdminCategoryController::class, 'edit'])->name('admin.category.category.edit');
    Route::post('/admin/category/category/update/{currentCategory}', [AdminCategoryController::class, 'update'])->name('admin.category.category.update');
    Route::get('/admin/category/category/delete/{currentCategory}', [AdminCategoryController::class, 'delete'])->name('admin.category.category.delete');



    // admin site Info

    Route::get('/admin/site/users', [AdminSiteInfoController::class, 'users'])->name('admin.site.users');

    //Seeder
    Route::get('/seeder/index', [SeedController::class, 'index']);
    Route::get('/seeder/User', [SeedController::class, 'createUser']);
    Route::get('/seeder/CategoryQuestion', [SeedController::class, 'createCategoryQuestion']);
    Route::get('/seeder/Question', [SeedController::class, 'createQuestion']);
    Route::get('/seeder/assignCategoryToUser', [SeedController::class, 'assignCategoryToUser']);
    Route::get('/seeder/createComment', [SeedController::class, 'createComment']);


    Route::get('/seeder/category', [SeedCategoryBookController::class, 'index']);
    Route::get('/seeder/addThreecategory/{category}', [SeedCategoryBookController::class, 'addThreecategory']);


    //Test
    Route::get('/test/index', [TestController::class, 'index']);
    Route::get('/learning/test/{userAnswer}/{questionId}', [UserLearningNewController::class, 'changeQuestionAndUserCategoryQuestion']);

    Route::get('/test/emailTest', [TestController::class, 'emailTest']);
    Route::get('/test/smsTest', [TestController::class, 'smsTest']);

    Route::get('/test/logout', [TestController::class, 'logout']);

    Route::get('/test/addCatToUserMinChange', [TestController::class, 'addCatToUserMinChange']);

    Route::get('/test/catQuestionsCount', [TestController::class, 'catQuestionsCount']);

    Route::get('/test/remoteDB', [TestController::class, 'remoteDB']);
    Route::get('/test/myProgress', [TestController::class, 'myProgress']);
    Route::get('/loginAs/{id}', [TestController::class, 'loginAs']);

    Route::get('/test/findTheListId', [TestController::class, 'findTheListId']);
    Route::get('/test/removeDuplicatedQuestions', [TestController::class, 'removeDuplicatedQuestions']);
    Route::get('/test/transferImages', [TestController::class, 'transferImages']);

    Route::get('/test/createJozveCategory', [TestController::class, 'createJozveCategory']);
    Route::get('/test/createFreeCategory', [TestController::class, 'createFreeCategory']);



    Route::get('/test-auth', function () {
        return Auth::check() ? 'Logged in' : 'Not logged in';
    });


    Route::get('/test/removeQuestionsFromCatAndSetQuestionTableIdForIncreament', [TestController::class, 'removeQuestionsFromCatAndSetQuestionTableIdForIncreament']);





    Route::get('/test/updateUserBadge', [TestController::class, 'updateUserBadge']);
    // Route::get('/test/upload1', [TestController::class, 'upload1']);
    // Route::get('/test/upload2', [TestController::class, 'upload2']);

    Route::get('/test/dinvazendegi', [TestController::class, 'dinvazendegi']);




    Route::get('/test/getCategoriesLevel', [Test2Controller::class, 'getCategoriesLevel']);























    // Route::post('/ckeditor/upload', [TestController::class, 'upload'])->name('ckeditor.upload');
    
    //Import

    Route::get('/import/category', [AdminImportCategoryController::class, 'index']); // in moheme marhale 1

    Route::get('/import/fromText', [AdminImportController::class, 'import']);  // in moheme   // marhale 2
    Route::get('/import/downloadImages', [AdminImportController::class, 'downloadImages']);  // in ham moheme marhale 3
    Route::get('/import/transfer', [AdminImportController::class, 'transfer']); // in ham moheme marhale 4
    Route::get('/import/saveQuestionsTextes', [AdminImportController::class, 'saveQuestionsTextes']); // in vase gozashte hast
        

    Route::get('/import/downloadImagesFromJson', [AdminImportController::class, 'downloadImagesFromJson']);  // in vase gozashte hast


    Route::get('/export/chunck', [DatabaseExportController::class, 'exportDatabase']); // in vase gozashte hast




    //Import new
    Route::get('/import/beforeUpload', [AdminImportNewController::class, 'beforeUpload'])->name('category.categoryQuestion.beforeUpload');  // in moheme marhale 5
    Route::get('/import/addQuestionCategoryToTagTable', [AdminImportNewController::class, 'addQuestionCategoryToTagTable'])->name('category.categoryQuestion.addQuestionCategoryToTagTable'); // in vase gozashte hast
    Route::get('/import/addTagIdToQuestions', [AdminImportNewController::class, 'addTagIdToQuestions'])->name('category.categoryQuestion.addTagIdToQuestions'); // in vase gozashte hast
    Route::get('/import/createCoustionCountForTable', [AdminImportNewController::class, 'createCoustionCountForTable'])->name('category.categoryQuestion.createCoustionCountForTable'); // in vase gozashte hast

    // import book
    Route::get('/import/book', [AdminImportBookController::class, 'import'])->name('category.book.import');
    
    // import kanoon
    Route::get('/import/kanoon/nemooneSoal/category', [AdminImportKanoonCategoryController::class, 'categoryImport'])->name('kanoon.nemooneSoal.category.import');
    Route::get('/import/kanoon/nemooneSoal/soal', [AdminImportKanoonSoalController::class, 'soalImport'])->name('kanoon.nemooneSoal.soal.import');
    Route::get('/import/kanoon/nemooneSoal/saveHtml', [AdminImportKanoonSoalController::class, 'saveHtml'])->name('kanoon.nemooneSoal.soal.saveHtml');

    // import GamBeGam

    Route::get('/import/gambegam/padars/level1', [AdminImportGambeGamCategoryPadarsFirstLevelController::class, 'categoryImport'])->name('category.gambegam.padars.1.import');
    Route::get('/import/gambegam/padars/level2', [AdminImportGambeGamCategoryPadarsSecondLevelController::class, 'categoryImport'])->name('category.gambegam.padars.2.import');
    Route::get('/import/gambegam/padars/level3', [AdminImportGambeGamCategoryPadarsThirdLevelController::class, 'categoryImport'])->name('category.gambegam.padars.3.import');
    Route::get('/import/gambegam/padars/level4', [AdminImportGambeGamCategoryPadarsForthLevelController::class, 'categoryImport'])->name('category.gambegam.padars.4.import');
});
