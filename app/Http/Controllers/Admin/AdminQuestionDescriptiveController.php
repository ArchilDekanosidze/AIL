<?php

namespace App\Http\Controllers\Admin;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Http\Controllers\Controller;


class AdminQuestionDescriptiveController extends Controller
{

    public function create()
    {
        $categories = CategoryQuestion::all();
        return view('admin.question.descriptive.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $categoryQuestion = CategoryQuestion::find($request->categorySelect);


        $question = new Question();
        $question->category_question_id = $categoryQuestion->id;
        $question->front = $request->editorFront;
        $question->back = $request->editorBack;
        $question->percentage = $request->percentage;
        $question->type = "descriptive";
        $question->save();
        return back()->with("categorySelect", $request->categorySelect);
    }

    public function edit(Question $question)
    {
        $categories = CategoryQuestion::all();
        return view('admin.question.descriptive.edit', compact('question', 'categories'));
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

}

