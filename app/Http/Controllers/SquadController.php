<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Squad;
use App\Http\Middleware\Authenticate;
use App\Http\Requests\StoreSquadRequest;
use App\Http\Requests\UpdateSquadRequest;

class SquadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $squads = Squad::with('users')->get();
            $response = $squads->map(function ($squad) {
                return [
                    'id' => $squad->id,
                    'squadName' => $squad->squadName,
                    'reference' => $squad->reference,
                    'user_ids' => $squad->users->pluck('id')->toArray(),
                    'users' => $squad->users->toArray(),
                    'deleted_at' => $squad->deleted_at,
                    'created_at' => $squad->created_at,
                    'updated_at' => $squad->updated_at,
                ];
            });
        
            return response()->json($response, 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception], 500);
        }        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSquadRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSquadRequest $request)
    {
        // Validate and store the squad data
        $validatedData = $request->validate([
            'squadName' => 'required',
            'reference' => 'required',
        ]);

        $squad = Squad::create($validatedData);

        $user_ids = $request->input('user_id');
        $users = [];

        if ($user_ids) {
            $users = User::whereIn('id', $user_ids)->get();
            $squad->users()->attach($users);
        }

        $squad->load('users');

        $responseData = [
            'message' => "{$squad->squadName} was created successfully",
            'squadName' => $squad->squadName,
            'reference' => $squad->reference,
            'user_ids' => $squad->users->pluck('id')->toArray(),
            'users' => $squad->users->toArray(),
            'updated_at' => $squad->updated_at,
            'created_at' => $squad->created_at,
            'id' => $squad->id,
        ];

        return response()->json($responseData, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSquadRequest  $request
     * @param  \App\Models\Squad  $squad
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSquadRequest $request, Squad $squad)
    {
        try {
            // Validate the input data
            $validatedData = $request->validate([
                'squadName' => 'required',
                'reference' => 'required|string',
                'user_id' => 'array',
                'user_id.*' => 'exists:users,id',
            ]);
        
            // Update the squad with the validated data
            $squad->update($validatedData);
        
            // Link users to the squad if provided
            $user_ids = $request->input('user_id');
            if ($user_ids) {
                $users = User::whereIn('id', $user_ids)->get();
                $squad->users()->sync($users);
            } else {
                $squad->users()->detach();
            }
        
            // Fetch the updated squad data with the user IDs
            $squad = $squad->fresh('users');
        
            // Build the response data
            $response = [
                'squadName' => $squad->squadName,
                'reference' => $squad->reference,
                'user_ids' => $squad->users->pluck('id')->toArray(),
                'updated_at' => $squad->updated_at,
                'created_at' => $squad->created_at,
                'id' => $squad->id,
            ];
        
            // Build the response message with the squad's name
            $message = "{$squad->squadName} was updated successfully";
        
            return response()->json(['message' => $message, 'squad' => $response]);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception], 500);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Squad  $squad
     * @return \Illuminate\Http\Response
     */
    public function destroy(Squad $squad)
    {
        try {
            // Delete the squad
            $squad->delete();

            // Build the response message with the squad's name
            $message = "{$squad->squadName} was deleted successfully";

            // Return a response indicating the successful delete
            return response()->json(['message' => $message], 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception], 500);
        }
    }
}
