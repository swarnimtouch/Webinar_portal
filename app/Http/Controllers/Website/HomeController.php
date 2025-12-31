<?php

namespace App\Http\Controllers\Website;

use App\Models\Banner;
use App\Models\Brands;
use App\Models\Content;
use App\Models\DaynamicFields;
use App\Models\Speakers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HomeController
{
    public function index()
    {
        $banners = Banner::where('status', 'active')
            ->orderBy('id')
            ->get();

        $contents = Content::get()
            ->keyBy('slug');

        $speakers = Speakers::where('status', 'active')
            ->orderBy('id')
            ->get();

        $brands = Brands::where('status', 'active')
            ->orderBy('id')
            ->get();

        $loginFields = DaynamicFields::where('status', 'active')
            ->where('login_with', 1)
            ->orderBy('index_no')
            ->get();

        $registerFields = DaynamicFields::where('status', 'active')

            ->orderBy('index_no')
            ->get();
        // Prepare slider data for JavaScript
        $sliderData = $banners->map(function ($banner) {
            $data = [
                'type' => $banner->type ?? 'image',
                'src' => asset('storage/banners/' . $banner->filename),
            ];

            // If video, add poster image
            if ($data['type'] === 'video' && !empty($banner->poster)) {
                $data['poster'] = asset('storage/banners/' . $banner->poster);
            }

            return $data;
        });

        return view('Website.home', compact('banners','registerFields', 'contents', 'speakers', 'brands', 'loginFields', 'sliderData'));
    }
    public function login(Request $request)
    {
        $loginFields = DaynamicFields::where('status', 'active')
            ->where('login_with', 1)
            ->orderBy('index_no')
            ->get();

        // Dynamic validation
        $rules = [];
        foreach ($loginFields as $field) {
            if (str_contains($field->field_name, 'mobile')) {
                $rules[$field->field_name] = 'required|digits:10';
            } elseif (str_contains($field->field_name, 'email')) {
                $rules[$field->field_name] = 'required|email';
            } else {
                $rules[$field->field_name] = 'required';
            }
        }

        $validated = $request->validate($rules);

        // Map dynamic fields to DB
        $fieldMapping = [
            'mobile_number' => 'mobile',
            'alternative_mobile_number' => 'alternative_mobile',
        ];

        $query = User::query();

        foreach ($validated as $field => $value) {
            $dbField = $fieldMapping[$field] ?? $field;
            $query->where($dbField, $value);
        }

        $user = $query->first();

        // ❌ USER NOT FOUND → REGISTRATION
        if (!$user) {
            return redirect()->route('website.register')
                ->with('error', 'User not found. Please register first.');
        }

        // ✅ LOGIN
        Auth::login($user);

        return redirect()->route('dashboard1');
    }

    public function register(Request $request)
    {
        $fields = DaynamicFields::where('status', 'active')->get();

        $rules = [];

        foreach ($fields as $field) {

            // only required fields
            if ($field->is_required == 1) {

                if ($field->field_name === 'mobile_number') {
                    $rules['mobile'] = 'required|digits:10|unique:users,mobile';
                }
                elseif (str_contains($field->field_name, 'email')) {
                    $rules['email'] = 'required|email|unique:users,email';
                }
                else {
                    $rules[$field->field_name] = 'required';
                }

            }
        }

        $request->validate($rules);

        $user = new User();
        $user->fill($request->except('_token'));
        $user->type = 'doctor';
        $user->save();

        Auth::login($user);

        return redirect()->route('dashboard1');
    }


}
