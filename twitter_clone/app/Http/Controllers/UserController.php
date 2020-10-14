<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * フォローする
     *
     * @param Request $request
     * @param User $followee
     * @return RedirectResponse
     */
    public function follow(Request $request, User $followee): RedirectResponse
    {
        $request->user()->followees()->attach($followee->id);
        return back()->with('フォーローしました。');
    }

    /**
     * フォローを解除する
     *
     * @param Request $request
     * @param User $followee
     * @return RedirectResponse
     */
    public function unfollow(Request $request, User $followee): RedirectResponse
    {
        if (!$request->user()->hasFollowee($followee)) {
            return back()->with('フォーローしていません。');
        }
        if ($request->user()->followees()->detach($followee->id)) {
            return back()->with('フォーロー解除しました。');
        }
        return back()->withErrors('フォーロー解除に失敗しました。');
    }
}
