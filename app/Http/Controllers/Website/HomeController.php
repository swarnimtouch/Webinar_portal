<?php

namespace App\Http\Controllers\Website;

use App\Models\Banner;
use App\Models\Brands;
use App\Models\City;
use App\Models\Content;
use App\Models\Country;
use App\Models\DynamicFields;
use App\Models\Speakers;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        $hasCountryField = $fields->contains('field_name', 'country');
        $hasStateField = $fields->contains('field_name', 'state');
        $defaultCountry = Country::whereRaw('LOWER(name) = ?', ['india'])->first();
        $defaultState = $defaultCountry
            ? State::where('country_id', $defaultCountry->id)
                ->whereRaw('LOWER(name) = ?', ['gujarat'])->first()
            : null;
        return view('website.home', [
            'banners' => Banner::Active()->where('event_id', $event->id)->get()->pluck('slider_data'),
            'register_fields' => $fields,
            'login_fields' => $fields->where('login_with', 1)->values(),
            'contents' => Content::all()->keyBy('slug'),
            'speakers' => Speakers::where('event_id', $event->id)->Active()->get(),
            'brands' => Brands::where('event_id', $event->id)->Active()->get(),
            'countries' => Country::select('id', 'name')->orderBy('name')->get(),
            'initial_states' => $defaultCountry
                ? State::select('id', 'name')->where('country_id', $defaultCountry->id)->orderBy('name')->get()
                : collect(),
            'initial_cities' => !$hasStateField && $defaultState
                ? City::select('id', 'name')->where('state_id', $defaultState->id)->orderBy('name')->get()
                : collect(),
            'has_country_field' => $hasCountryField,
            'has_state_field' => $hasStateField,
            'default_country' => $defaultCountry,
            'default_state' => $defaultState,
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

        $query = User::where('event_id', $event->id);

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
        $request->session()->regenerate();

        return response()->json([
            'status' => true,
            'message' => 'Login successful'
        ], 200);
    }

    public function register(Request $request)
    {
        $event = app('event');
        $fields = DynamicFields::Active()->where('event_id', $event->id)->get();

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

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'type' => 'validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('_token');

        // India/Gujarat are only dependency defaults used to populate child
        // dropdowns. A location value is persisted only when that field is active.
        foreach (['country', 'state', 'city'] as $locationField) {
            if (!$fields->contains('field_name', $locationField)) {
                unset($data[$locationField]);
            }
        }

        if (isset($data['mobile_number'])) {
            $data['mobile'] = $data['mobile_number'];
            unset($data['mobile_number']);
        }

        if (!empty($data['first_name']) || !empty($data['last_name'])) {
            $data['name'] = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = new User();
        $user->fill($data);
        $user->type = 'doctor';
        $user->event_id = $event->id;
        $user->save();

        Auth::guard('web')->login($user);

        $request->session()->regenerate();

        return response()->json([
            'status' => true,
            'message' => 'Registration successful'
        ]);
    }

    public function countries()
    {
        return Country::select('id', 'name')
            ->orderBy('name')
            ->get();

    }

    public function states(Request $request)
    {
        $countryId = $request->route('country');
        return State::select('id', 'name')
            ->where('country_id', $countryId)
            ->orderBy('name')
            ->get();
    }

    public function cities(Request $request)
    {
        $stateId = $request->route('state');
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

        return redirect(event_route('home'));
    }
}
