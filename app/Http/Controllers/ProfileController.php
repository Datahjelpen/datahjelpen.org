<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use Storage;
use \Illuminate\Http\Request;
use \Illuminate\Support\Facades\Validator;

use \App\Image;
use \App\Profile;

class ProfileController extends Controller
{
    public function index()
    {
        $profile = Auth::user()->profile;

        if ($profile == null) {
            return redirect()->route('profile.create');
        }

        return view('profile.index', compact('profile'));
    }

    public function create()
    {
        if (Auth::user()->profile != null) return redirect()->route('profile');

        return view('profile.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->profile != null) return redirect()->route('profile');
        $request->url = str_slug($request->url);

        $this->validate($request, [
            'url'           => 'required|string|unique:profiles|min:1',
            'name_first'    => 'nullable|string',
            'name_last'     => 'nullable|string',
            'name_display'  => 'nullable|string',
            'title'         => 'nullable|string',
            'email_display' => 'nullable|string',
            'description'   => 'nullable|string'
        ]);

        $profile = new Profile;
        $profile->url = $request->url;
        $profile->name_first = $request->name_first;
        $profile->name_last = $request->name_last;
        $profile->name_display = $request->name_display;
        $profile->title = $request->title;
        $profile->email_display = $request->email_display;
        $profile->description = $request->description;
        $profile->user()->associate(Auth::user()->id);

        if ($request->file('image') != null) {
            $image = new Image;
            $image->url = $request->file('image')->store('profile_avatars');
            // $image->size_bytes = $request->file('image')->size;
            // $image->alt_tag = $request->file('image')->originalName;
            $image->size_bytes = '1';
            $image->alt_tag = 'image';
            $image->size_width = '1';
            $image->size_height = '1';
            // $image->title_tag
            // $image->description
            $image->size_name = 'full';
            $image->user()->associate(Auth::user()->id);
            $image->save();

            $profile->image()->associate($image->id);
        }

        $profile->save();


        Session::flash('alert-success', __('validation.succeeded.create', ['name' => $request->name_display]));
        return redirect()->route('profile');
    }

    public function show($profile)
    {
        $profile = Profile::where('id', $profile)->orWhere('url', $profile)->firstOrFail();

        // Make sure profile image has an url
        if (isset($profile->image->id)) {
            if ($profile->image->id != null) {
                $profile->image->url = Storage::url($profile->image->url);
            }
        }

        return view('profile.show', compact('profile'));
    }

    public function edit_mine()
    {
        $profile = Auth::user()->profile;

        return view('profile.edit', compact('profile'));
    }

    public function edit(Profile $profile)
    {
        return view('profile.edit', compact('profile'));
    }

    public function update(Request $request, Profile $profile)
    {
        $request->url = str_slug($request->url);

        $validator = Validator::make($request->all(), [
            'url'          => 'required|string|min:1|unique:profiles,url,'.$profile->id,
            'name_first'    => 'nullable|string',
            'name_last'     => 'nullable|string',
            'name_display'  => 'nullable|string',
            'title'         => 'nullable|string',
            'email_display' => 'nullable|string',
            'description'   => 'nullable|string'
        ]);

        if ($validator->fails()) {
            Session::flash('alert-danger', __('validation.failed.update', ['name' => $profile->name_display]));
            return redirect()->route('profile.edit', $profile->id)->withErrors($validator)->withInput();
        }

        if ($request->image_remove) {
            $profile->image()->dissociate();
        }

        $profile->url = $request->url;
        $profile->name_first = $request->name_first;
        $profile->name_last = $request->name_last;
        $profile->name_display = $request->name_display;
        $profile->title = $request->title;
        $profile->email_display = $request->email_display;
        $profile->description = $request->description;

        if ($request->file('image') != null) {
            $image = new Image;
            $image->url = $request->file('image')->store('profile_avatars');
            // $image->url = Storage::url($image->url);
            // $image->size_bytes = $request->file('image')->size;
            // $image->alt_tag = $request->file('image')->originalName;
            $image->size_bytes = '1';
            $image->alt_tag = 'image';
            $image->size_width = '1';
            $image->size_height = '1';
            // $image->title_tag
            // $image->description
            $image->size_name = 'full';
            $image->user()->associate(Auth::user()->id);
            $image->save();

            $profile->image()->associate($image->id);
        }
        $profile->save();

        Session::flash('alert-success', __('validation.succeeded.update', ['name' => $profile->name_display]));

        return redirect()->route('profile');
    }

    public function destroy(Profile $profile)
    {
        $profile->delete();

        Session::flash('alert-success', __('validation.succeeded.delete', ['name' => $profile->name_display]));
        return back();
    }
}
