<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuestController extends Controller
{
    public function create()
    {
        return view('quests.create');
    }
}
