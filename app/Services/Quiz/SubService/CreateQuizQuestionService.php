<?php
namespace App\Services\Quiz\SubService;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\DB;
use App\Services\Traits\ActorTrait;
use Illuminate\Support\Facades\Auth;

class CreateQuizQuestionService
{
    use ActorTrait;

    private array $currentLevels = [];
    private array $targetLevels = [];
    private array $categoryWeights = [];
    private int $totalWeight = 0;
    private array $categoriesId = [];
    private $selectedQuestions;
    private int $testCount;


    public function __construct(Request $request)
    {
        $this->categoriesId = $request->categorySelected ?? [];
        $this->selectedQuestions = collect();
        $this->testCount = $request->testCount ?? 10;
    }

    public function createQuestionsForQuiz()
    {
        $this->categoriesId = $this->expandToLeafCategories();       // 1. Expand to leaf
        $this->hydrateCategoryLevels();                              // 2. Load user progress data
        $this->calculateCategoryWeights();                           // 3. Score-based weighting
        $this->selectQuestions();  
        // dd($this->selectedQuestions->pluck('category_question_id'));                                  // 4. Pick questions
        return $this->selectedQuestions;
    }

    private function expandToLeafCategories(): array
    {
        $userId = $this->getUser()->id;
    
        $selectedCategories = CategoryQuestion::query()
            ->whereIn('id', $this->categoriesId)
            ->orWhere(function ($query) {
                $query->whereNested(function ($q) {
                    foreach ($this->categoriesId as $id) {
                        $category = CategoryQuestion::select('_lft', '_rgt')->find($id);
                        if ($category) {
                            $q->orWhere(function ($inner) use ($category) {
                                $inner->where('_lft', '>', $category->_lft)
                                      ->where('_rgt', '<', $category->_rgt);
                            });
                        }
                    }
                });
            })
            ->withCount('children')
            ->get();
    
        // Filter for leaf categories only
        $leafCategoryIds = $selectedCategories
            ->filter(fn($cat) => $cat->children_count === 0)
            ->pluck('id')
            ->unique()
            ->values()
            ->toArray();
        // Keep only those leaf categories the user has in the pivot table
        return DB::table('user_category_question')
            ->where('user_id', $userId)
            ->whereIn('category_question_id', $leafCategoryIds)
            ->where('is_active', 1)
            ->pluck('category_question_id')
            ->toArray();
    }

    private function hydrateCategoryLevels()
    {
        $userId = $this->getUser()->id;

        $levels = DB::table('user_category_question')
            ->where('user_id', $userId)
            ->whereIn('category_question_id', $this->categoriesId)
            ->get();

        foreach ($this->categoriesId as $categoryId) {
            $row = $levels->firstWhere('category_question_id', $categoryId);
            $this->currentLevels[$categoryId] = $row->level ?? 0;
            $this->targetLevels[$categoryId] = $row->target_level ?? 100;
        }
    }



    private function calculateCategoryWeights(): void
    {
        $this->totalWeight = 0;

        $categories = CategoryQuestion::whereIn('id', $this->categoriesId)
            ->select('id', 'question_count')
            ->get()
            ->keyBy('id');

        foreach ($this->categoriesId as $categoryId) {
            $progressGap = max(0, $this->targetLevels[$categoryId] - $this->currentLevels[$categoryId]);
            $questionCount = max(1, $categories[$categoryId]->question_count ?? 0);
            $weight = $progressGap * $questionCount;

            $this->categoryWeights[$categoryId] = $weight;
            $this->totalWeight += $weight;
        }

        if ($this->totalWeight === 0) {
            $count = count($this->categoryWeights);
            foreach ($this->categoryWeights as $categoryId => $_) {
                $this->categoryWeights[$categoryId] = 1 / $count;
            }
            $this->totalWeight = 1;
        } else {
            foreach ($this->categoryWeights as $categoryId => $weight) {
                $this->categoryWeights[$categoryId] = $weight / $this->totalWeight;
            }
        }
    }

    private function selectQuestions(): void
    {
        $sumLevels = 0;

        // Step 1: Allocate base with floor
        $initialAlloc = [];
        $fractionals = [];
        $allocated = 0;

        foreach ($this->categoryWeights as $categoryId => $weight) {
            $raw = $weight * $this->testCount;
            $floor = floor($raw);
            $initialAlloc[$categoryId] = $floor;
            $fractionals[$categoryId] = $raw - $floor;
            $allocated += $floor;
        }

        // Step 2: Fill remaining with highest decimals
        $remaining = $this->testCount - $allocated;
        arsort($fractionals);
        foreach (array_keys($fractionals) as $categoryId) {
            if ($remaining <= 0) break;
            $initialAlloc[$categoryId]++;
            $remaining--;
        }

        // Step 3: Fetch questions
        foreach ($initialAlloc as $categoryId => $numQuestions) {
            $category = CategoryQuestion::find($categoryId);
            if (!$category || $numQuestions <= 0) continue;

            $currentLevel = $this->currentLevels[$categoryId] ?? 0;
            $sumLevels += $currentLevel;

            $questions = $category->questions()
                ->test()
                ->active()
                ->orderByRaw('ABS(percentage - ? + (RAND() * 10))', [$currentLevel])
                ->limit($numQuestions)
                ->inRandomOrder()
                ->get();

            $this->selectedQuestions = $this->selectedQuestions->merge($questions);
        }

        // Step 4: Fill leftovers if any
        $remaining = $this->testCount - $this->selectedQuestions->count();
        if ($remaining > 0 && count($this->categoryWeights)) {
            $avgLevel = $sumLevels / count($this->categoryWeights);
            $backupQuestions = Question::whereIn("category_question_id", $this->categoriesId)
                ->test()
                ->active()
                ->whereNotIn("id", $this->selectedQuestions->pluck("id"))
                ->orderByRaw('ABS(percentage - ?)', [$avgLevel])
                ->limit($remaining)
                ->inRandomOrder()
                ->get();

            $this->selectedQuestions = $this->selectedQuestions->merge($backupQuestions);
        }
    }

}