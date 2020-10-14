<?php

namespace App\Http\Controllers;

use App\Tweet;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Class TimelineController
 * @package App\Http\Controllers
 */
class TimelineController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        /** @var Tweet[]|Collection $tweets */
        $timeline = $user->timeline;

        return view('timelines.index', compact('timeline'));
    }
}
