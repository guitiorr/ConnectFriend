<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $validated = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'hobbies' => ['required', 'array', 'min:3'], // Validate hobbies as an array with at least 3 items
            'hobbies.*' => ['string', 'distinct'], // Ensure each hobby is a distinct string
            'phone' => ['required', 'string', 'regex:/^\d{10,15}$/'], // Validate phone as digits only (10-15 digits)
            'gender' => ['required', 'string', 'in:male,female'], // Validate gender as either 'male' or 'female'
        ])->validate();

        return User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'hobbies' => json_encode($validated['hobbies']), // Store hobbies as JSON
            'phone' => $validated['phone'],
            'gender' => $validated['gender'], // Include gender
        ]);
    }


    public function showRegistrationForm()
    {
        // Generate a random registration price between 100,000 and 125,000
        $price = rand(100000, 125000);

        // Pass the price to the registration view
        return view('auth.register', compact('price'));
    }
}
