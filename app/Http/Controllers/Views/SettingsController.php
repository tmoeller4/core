<?php

namespace Biigle\Http\Controllers\Views;

use Illuminate\Contracts\Auth\Guard;

class SettingsController extends Controller
{
    /**
     * Redirects to the profile settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->route('settings-profile');
    }

    /**
     * Shows the profile settings.
     *
     * @param Guard $auth
     * @return \Illuminate\Http\Response
     */
    public function profile(Guard $auth)
    {
        return view('settings.profile')
            ->withUser($auth->user())
            ->withSaved(session('saved'));
    }

    /**
     * Shows the account settings.
     *
     * @param Guard $auth
     * @return \Illuminate\Http\Response
     */
    public function account(Guard $auth)
    {
        return view('settings.account')
            ->withUser($auth->user())
            ->withSaved(session('saved'))
            ->withOrigin(session('_origin'));
    }

    /**
     * Shows the tokens settings.
     *
     * @param Guard $auth
     * @return \Illuminate\Http\Response
     */
    public function tokens(Guard $auth)
    {
        return view('settings.tokens')
            ->withUser($auth->user())
            ->withTokens($auth->user()->apiTokens()->orderBy('updated_at', 'desc')->get())
            ->withToken(session('token'))
            ->withDeleted(session('deleted'));
    }
}
