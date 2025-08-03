<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comment;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Http\Controllers\Controller;


class AdminQuestionController extends Controller
{

    public function index(CategoryQuestion $category)
    {      
        $path = $category->path();
        $questions = Question::test()->where("category_question_id", $category->id)->get();
        return view('admin.question.index', compact('questions', 'path'));
    }
    public function show(Question $question)
    {
        // dd($comments);
        return view('admin.question.show', compact('question'));
    }

    public function create()
    {
        $categories = CategoryQuestion::all();
        return view('admin.question.test.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $categoryQuestion = CategoryQuestion::find($request->categorySelect);


        $question = new Question();
        $question->category_question_id = $categoryQuestion->id;
        $question->front = $request->editorFront;
        $question->back = $request->editorBack;
        $question->p1 = $request->editorP1;
        $question->p2 = $request->editorP2;
        $question->p3 = $request->editorP3;
        $question->p4 = $request->editorP4;
        $question->answer = $request->answer;
        $question->percentage = $request->percentage;
        $question->type = "test";
        $question->save();
        return back()->with("categorySelect", $request->categorySelect);
    }

    public function edit(Question $question)
    {
        $categories = CategoryQuestion::all();
        return view('admin.question.test.edit', compact('question', 'categories'));
    }

    public function update(Request $request, Question $question)
    {
        $categoryQuestion = CategoryQuestion::find($request->categorySelect);
        $question->category_question_id = $categoryQuestion->id;
        $question->front = $request->editorFront;
        $question->back = $request->editorBack;
        $question->p1 = $request->editorP1;
        $question->p2 = $request->editorP2;
        $question->p3 = $request->editorP3;
        $question->p4 = $request->editorP4;
        $question->answer = $request->answer;
        $question->percentage = $request->percentage;
        $question->save();
        return back()->with("success", "سوال با موفقیت ثبت شد");
    }

    public function delete(Request $request, Question $question)
    {
        $question->delete();
        return back();
    }

    public function updatelevel(Question $question, $percentage)
    {
        $question->percentage = $percentage;
        $question->save();
        dd($question, $percentage, 'changed');
    }
}

