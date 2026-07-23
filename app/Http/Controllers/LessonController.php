<?php

namespace App\Http\Controllers;

class LessonController extends Controller
{
    public function index()
    {
        return view('lessons.index');
    }
}
