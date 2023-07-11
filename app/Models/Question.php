<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'options', 'correct_option'];

    protected $casts = [
        'options' => 'array'
    ];

    public function countCorrectAnswers($selectedOptions)
    {
        $correctAnswers = 0;
        
        // Contoh jawaban yang diinput
        $selectedOptions = [
            1 => 1,
            2 => 1,
            3 => 3,
            4 => 2,
            5 => 1,
            6 => 2,
            7 => 2,
            8 => 3,
            9 => 3,
            10 => 0
        ];

        foreach ($selectedOptions as $questionId => $selectedOption) {
            $question = Question::find($questionId);

            if ($question && $question->correct_option === $selectedOption) {
                $correctAnswers++;
            }
        }

        return $correctAnswers;
    }

    public static function searchByTitle($keyword, $limit)
    {
        return self::where('title', 'LIKE', '%' . $keyword . '%')->limit($limit)->get();
    }

    public static function orderByCorrectAnswersDesc()
    {
        $questions = self::all();

        $sortedQuestions = $questions->sortByDesc(function ($question) {
            $correctCount = 0;
            $selectedOptions = [
                1 => 1,
                2 => 1,
                3 => 3,
                4 => 2,
                5 => 1,
                6 => 2,
                7 => 2,
                8 => 3,
                9 => 3,
                10 => 0
            ];
            
            foreach ($selectedOptions as $questionId => $selectedOption) {
                $question = Question::find($questionId);

                if ($question && $question->correct_option === $selectedOption) {
                    $correctCount++;
                }
            }

            return $correctCount;
        });

        return $sortedQuestions;
    }
}
