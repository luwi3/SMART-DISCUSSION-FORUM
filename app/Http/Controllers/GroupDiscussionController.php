<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GroupDiscussionController extends Controller
{
    public function create()
    {
        return view('groups.create');
    }
}