<?php

namespace App\Http\Controllers\Website;

use App\Models\Banner;
use App\Models\Brands;
use App\Models\City;
use App\Models\Content;
use App\Models\Country;
use App\Models\DynamicFields;
use App\Models\Events;
use App\Models\Speakers;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class HomeController
{
    public function index()
    {
        $event = app('event');
        $fields = DynamicFields::with('attribute_data')
            ->where('event_id', $event->id)
            ->Active()
            ->orderBy('index_no')
            ->get();

        return view('website.home', [
            'banners' => Banner::Active()->where('event_id', $event->id)->get()->pluck('slider_data'),
            'register_fields' => $fields,
            'login_fields' => $fields->where('login_with', 1)->values(),
            'contents' => Content::all()->keyBy('slug'),
            'speakers' => Speakers::where('event_id', $event->id)->Active()->get(),
            'brands' => Brands::where('event_id', $event->id)->Active()->get(),
            'title' => 'Home',
        ]);
    }

    public function login(Request $request)
    {
        $event = app('event');

        $loginFields = DynamicFields::active()
            ->where('event_id', $event->id)
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

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'type' => 'validation',
                'errors' => $validator->errors()
            ], 412);
        }

        $validated = $validator->validated();

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

        if (!$user || $user->type !== 'doctor') {
            return response()->json([
                'status' => false,
                'type' => 'auth',
                'message' => 'Invalid credentials'
            ], 401);
        }

        Auth::guard('web')->login($user);

        return response()->json([
            'status' => true,
            'message' => 'Login successful'
        ], 200);
    }


    public function register(Request $request)
    {
        $fields = DynamicFields::where('status', 'active')->get();
        $rules = [];

        foreach ($fields as $field) {
            if ($field->is_required == 1) {

                if ($field->field_name === 'mobile_number') {
                    $rules['mobile_number'] = 'required|digits:10|unique:users,mobile';
                } elseif (str_contains($field->field_name, 'email')) {
                    $rules['email'] = 'required|email|unique:users,email';
                } else {
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
                ->withErrors($e->errors())
                ->withInput()
                ->with('open_register_modal', true);
        }
        $data = $request->except('_token');

        if (isset($data['mobile_number'])) {
            $data['mobile'] = $data['mobile_number'];
            unset($data['mobile_number']);
        }
        if (!empty($data['first_name']) || !empty($data['last_name'])) {
            $data['name'] = trim(
                ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')
            );

        }
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
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home', ['slug' => $request->route('slug')]);
    }
}
