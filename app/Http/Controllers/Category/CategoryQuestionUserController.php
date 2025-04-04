<?php
namespace App\Http\Controllers\Category;


use App\Models\Tag;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Traits\ActorControllerTrait;
use App\Services\CategoryQuestion\CategoriesQuestionService;


class CategoryQuestionUserController extends Controller
{


   

    private $categoriesQuestionService;
    use ActorControllerTrait;


    public function __construct(CategoriesQuestionService $categoriesQuestionService)
    {
        $this->categoriesQuestionService = $categoriesQuestionService;
    }
    public function index(CategoryQuestion $currentCategory, Request $request)
    {                   
        $directCats = $this->categoriesQuestionService->getDirectcats($currentCategory);   
        $ancestor = $this->categoriesQuestionService->getAncestor($currentCategory);   

        // $question = Question::find(123);
        // dd($question->front);

        return view('category.categoryQuestion.user.index', compact('currentCategory', 'directCats', 'ancestor'));
    }

    public function getRandomFreeQuestion()
    {       
        return $this->categoriesQuestionService->getRandomFreeQuestion();
    }

    public function addCategoryToUser()
    {
        $this->categoriesQuestionService->addCategoryToUser();
        return true;
    }

    public function removeCategoryFromUser()
    {
        $this->categoriesQuestionService->removeCategoryFromUser();
        return true;
    }

    public function createCoustionCountForTable()
    {
        $categories = CategoryQuestion::all();
        foreach ($categories as $category) {
            if($category->id %100 == 0)
            {
                dump($category->id);
            }
            $category->question_count = $category->allQuestionCount();
            $category->save();            
        }
    }

    public function addQuestionCategoryToTagTable()
    {
        Tag::truncate();
        $mainCategory = CategoryQuestion::find(1);
        $firstLevelDirectCats = $this->categoriesQuestionService->getDirectcats($mainCategory);
        $firstLevelDirectCats;
        foreach ($firstLevelDirectCats as $firstLevelDirectCat) {
            $secondtLevelDirectCats = $this->categoriesQuestionService->getDirectcats($firstLevelDirectCat);
            foreach ($secondtLevelDirectCats as $category) {
                $ancestorString = $firstLevelDirectCat->name . '-' . $category->name ;                          
                $tag  = new Tag();
                $tag->id = $category->id;
                $tag->name = $ancestorString;
                $tag->slug =$this->makeSlug($ancestorString); 
            
                $tag->bronz1 = CategoryQuestion::SCORE_BRONZ1 * max($category->question_count, 50);
                $tag->bronz2 = CategoryQuestion::SCORE_BRONZ2 * max($category->question_count, 50);
                $tag->bronz3 = CategoryQuestion::SCORE_BRONZ3 * max($category->question_count, 50);            
                $tag->silver1 = CategoryQuestion::SCORE_SILVER1 * max($category->question_count, 50);
                $tag->silver2 = CategoryQuestion::SCORE_SILVER2 * max($category->question_count, 50);
                $tag->silver3 = CategoryQuestion::SCORE_SILVER3 * max($category->question_count, 50);
                $tag->gold1 = CategoryQuestion::SCORE_GOLD1 * max($category->question_count, 50);
                $tag->gold2 = CategoryQuestion::SCORE_GOLD2 * max($category->question_count, 50);
                $tag->gold3 = CategoryQuestion::SCORE_GOLD3 * max($category->question_count, 50);
                $tag->platinum1 = CategoryQuestion::SCORE_PLATINUM1 * max($category->question_count, 50);
                $tag->platinum2 = CategoryQuestion::SCORE_PLATINUM2 * max($category->question_count, 50);
                $tag->platinum3 = CategoryQuestion::SCORE_PLATINUM3 * max($category->question_count, 50);
                $tag->dimond1 = CategoryQuestion::SCORE_DIMOND1 * max($category->question_count, 50);
                $tag->dimond2 = CategoryQuestion::SCORE_DIMOND2 * max($category->question_count, 50);
                $tag->dimond3 = CategoryQuestion::SCORE_DIMOND3 * max($category->question_count, 50);
                $tag->legendary1 = CategoryQuestion::SCORE_LEGENDARY1 * max($category->question_count, 50);
                $tag->legendary2 = CategoryQuestion::SCORE_LEGENDARY2 * max($category->question_count, 50);
                $tag->legendary3 = CategoryQuestion::SCORE_LEGENDARY3 * max($category->question_count, 50);                                                                
                    
                $tag->save();
            }
        }                    
    }

    function makeSlug($string) {
        // Convert Persian spaces to hyphens
        $string = str_replace([' ', '‌'], '-', $string); 
    
        // Remove special characters (except hyphens)
        $string = preg_replace('/[^\p{L}\p{N}-]/u', '', $string);
    
        // Convert to lowercase
        return mb_strtolower($string, 'UTF-8');
    }

    public function addTagIdToQuestions()
    {
        $mainCategory = CategoryQuestion::find(1);
        $firstLevelDirectCats = $this->categoriesQuestionService->getDirectcats($mainCategory);
        $firstLevelDirectCats;
        foreach ($firstLevelDirectCats as $firstLevelDirectCat) {
            $secondtLevelDirectCats = $this->categoriesQuestionService->getDirectcats($firstLevelDirectCat);
            foreach ($secondtLevelDirectCats as $category) {
                $catsId = $this->categoriesQuestionService->getDescendantsAndSelfIds($category);

                $tagId = Tag::find($category->id)->id;
                dump($tagId);

                DB::table('questions')->whereIn('category_question_id', $catsId)->update(['tag_id' => $tagId]);                
            }
        }
    }
    
}
