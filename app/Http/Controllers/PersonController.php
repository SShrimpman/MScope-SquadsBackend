<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;

class PersonController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('firstName', 'lastName', 'password');

        // Find the person using the provided firstName and lastName
        $person = Person::where('firstName', $credentials['firstName'])
            ->where('lastName', $credentials['lastName'])
            ->first();

        // If a person is found and the password is correct
        if ($person && Hash::check($credentials['password'], $person->password)) {
            Auth::login($person);

            // Authentication successful
            $token = $person->createToken('authToken')->plainTextToken;
            return response()->json(['token' => $token]);
        }

        // Authentication failed
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            return response()->json(Person::all(), 200);
        } catch (\Exception $exception){
            return response()->json(['error'=>$exception],500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePersonRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePersonRequest $request)
    {
        // Validate the input data
        $validatedData = $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|string',
            'role_id' => [
                'required',
                Rule::exists('roles', 'id'),
            ],
        ]);

        // Hash the password if it is provided
        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        // Create the person
        $person = Person::create($validatedData);

        // Return the created person as a response
        return response()->json($person, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        try {
            return response()->json($person,200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePersonRequest  $request
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePersonRequest $request, Person $person)
    {
        try{
            // Validate the input data
            $validatedData = $request->validate([
                'firstName' => 'required|string',
                'lastName' => 'required|string',
                'username' => 'required|string',
                'role_id' => 'required|exists:roles,id',
            ]);

            // Check if the password field is present in the request
            if ($request->has('password')) {
                // Hash the new password
                $validatedData['password'] = bcrypt($request->input('password'));
            }

            // Update the person with the validated data
            $person->update($validatedData);

            // Build the response message with the person's first name and last name
            $message = "{$person->firstName} {$person->lastName} was updated successfully";
            
            // Return a response indicating the successful update
            return response()->json(['message' => $message]);
        } catch(\Exception $exception) {
            return response()->json(['error' => $exception], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function destroy(Person $person)
    {
        try {
            // Delete the person
            $person->delete();

            // Build the response message with the person's first name and last name
            $message = "{$person->firstName} {$person->lastName} was deleted successfully";

            // Return a response indicating the successful delete
            return response()->json(['message' => $message], 205);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception], 500);
        }
    }
}
