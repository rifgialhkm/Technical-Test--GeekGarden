<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionCollection;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ?? null;
        $keyword = $request->keyword;

        if ($keyword) {
            return response()->json(new QuestionCollection(Question::searchByTitle($keyword, $limit)), Response::HTTP_OK);
        } else {
            return response()->json(new QuestionCollection(Question::limit($limit)->get()), Response::HTTP_OK);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
            'options' => 'required|array',
            'correct_option' => 'required|integer|min:0|max:' . (count($request->options) - 1),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid params passed',
                'error' => $validator->errors()
            ], 400);
        }

        $question = Question::create([
            'title' => $request->title,
            'content' => $request->content,
            'options' => $request->options,
            'correct_option' => $request->correct_option
        ]);

        return new QuestionResource($question);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        if (!$question) {
            return response()->json(['message' => 'Pertanyaan tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        return new QuestionResource($question);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string',
            'content' => 'sometimes|required|string',
            'options' => 'sometimes|required|array',
            'correct_option' => 'sometimes|required|integer|min:0|max:' . (count($request->options ?? $question->options) - 1),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid params passed',
                'error' => $validator->errors()
            ], 400);
        }

        $question->update($request->only([
            'title',
            'content',
            'options',
            'correct_option'
        ]));

        return new QuestionResource($question);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data deleted succesfully'
        ], Response::HTTP_OK);
    }
}
