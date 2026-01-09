<?php

namespace App\Http\Controllers\Website;

use App\Models\Banner;
use App\Models\Brands;
use App\Models\City;
use App\Models\Content;
use App\Models\Country;
use App\Models\DaynamicFields;
use App\Models\Speakers;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

        $registerFields = DaynamicFields::with('attributeInput')
            ->where('status', 'active')
            ->orderBy('index_no')
            ->get();


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

        if (!$user) {
            return back()
                ->with('toast_error', 'User not found. Please register first.')
                ->withInput()
                ->with('open_login_modal', true);
        }

        Auth::login($user);

        return redirect()
            ->route('website.dashboard')
            ->with('toast_success', 'Login successful!');
    }



    public function register(Request $request)
    {
//        dd($request->all());
        $fields = DaynamicFields::where('status', 'active')->get();
        $rules = [];

        foreach ($fields as $field) {
            if ($field->is_required == 1) {

                if ($field->field_name === 'mobile_number') {
                    $rules['mobile_number'] = 'required|digits:10|unique:users,mobile';
                }
                elseif (str_contains($field->field_name, 'email')) {
                    $rules['email'] = 'required|email|unique:users,email';
                }
                else {
                    $rules[$field->field_name] = 'required';
                }
            }
        }

        try {
            $validated = $request->validate($rules);

        } catch (ValidationException $e) {

            $firstError = collect($e->errors())->first()[0];

            return back()
                ->with('toast_error', $firstError)
                ->withErrors($e->errors()) // optional (for old())
                ->withInput()
                ->with('open_register_modal', true);
        }
        $data = $request->except('_token');

        if (isset($data['mobile_number'])) {
            $data['mobile'] = $data['mobile_number'];
            unset($data['mobile_number']);
        }
//        dd($data);
        $user = new User();
        $user->fill($data);

        $user->type = 'doctor';
        $user->save();

        Auth::login($user);

        return redirect()
            ->route('website.dashboard')
            ->with('toast_success', 'Registration successful!');
    }


    public function countries()
    {
        return Country::select('id', 'name')
            ->orderBy('name')
            ->get();

    }

    public function states($countryId)
    {
       return State::select('id', 'name')
           ->where('country_id', $countryId)
           ->orderBy('name')
           ->get();
    }

    public function cities($stateId)
    {
        return City::select('id', 'name')
            ->where('state_id', $stateId)
            ->orderBy('name')
            ->get();

    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
