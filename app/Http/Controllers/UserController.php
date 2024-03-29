<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Middleware\Authenticate;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = User::with('role')->get();
            return response()->json($users, 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
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

        // Create the user
        $user = User::create($validatedData);

        // Prepare the response data
        $message = "{$user->firstName} {$user->lastName} was created successfully";
        $responseData = [
            'message' => $message,
            'user' => $user,
        ];

        // Return the created user as a response
        return response()->json($responseData, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try{
            // Validate the input data
            $validatedData = $request->validate([
                'firstName' => 'required|string',
                'lastName' => 'required|string',
                'username' => 'required|string',
                'role_id' => [
                    'required',
                    Rule::exists('roles', 'id'),
                ],
                // 'role_id' => 'required|exists:roles,id',
            ]);

            // Check if the password field is present in the request
            if ($request->has('password')) {
                // Hash the new password
                $validatedData['password'] = bcrypt($request->input('password'));
            }

            // Check if the role is being updated
            if ($user->role_id != $validatedData['role_id']) {
                // Check if the user being updated is currently an 'Admin'
                if ($user->role->name === 'Admin') {
                    // Check if there are other users with the 'Admin' role
                    $adminCount = User::where('role_id', $validatedData['role_id'])
                        ->where('id', '!=', $user->id)
                        ->count();

                    if ($adminCount > 1) {
                        return response()->json(['error' => 'Need to have at least one admin in the App!'], 400);
                    }
                }
            }

            // Update the user with the validated data
            $user->update($validatedData);

            // Load the role relationship
            $user->load('role');

            // Build the response message with the user's first name and last name
            $message = "{$user->firstName} {$user->lastName} was updated successfully";
            $responseData = [
                'message' => $message,
                'user' => new UserResource($user),
            ];
            
            // Return a response indicating the successful update
            return response()->json($responseData, 200);
        } catch (ValidationException $exception) {
            return response()->json(['error' => $exception->errors()], 400);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try {
            // Delete the user
            $user->delete();

            // Build the response message with the user's first name and last name
            $message = "{$user->firstName} {$user->lastName} was deleted successfully";

            // Return a response indicating the successful delete
            return response()->json(['message' => $message], 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception], 500);
        }
    }
}
