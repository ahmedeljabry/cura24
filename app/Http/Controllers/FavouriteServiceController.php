<?php

namespace App\Http\Controllers;

use App\FavouriteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create($service_id)
    {
        $user = Auth::guard('web')->user();
        if ($user->user_type != 1) {
            abort(403, 'Only buyers can add favourites.');
        }

        // Prevent duplicates (already enforced in DB, but good to check before attempting)
        $alreadyExists = FavouriteService::where('user_id', $user->id)
            ->where('service_id', $service_id)
            ->exists();

        if ($alreadyExists) {
            return back()->with('info', 'Service is already in your favourites.');
        }

        // Add to favourites
        FavouriteService::create([
            'user_id' => $user->id,
            'service_id' => $service_id,
        ]);

        return response()->json(['type' => 'success', 'message' => 'Service added to your favourites.']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(FavouriteService $favouriteService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FavouriteService $favouriteService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FavouriteService $favouriteService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $user = Auth::guard('web')->user();

        if ($user->user_type != 1) {
            return response()->json(['type' => 'error', 'message' => 'Only buyers can remove favourites.'], 403);
        }

        $service_id = $request->input('service_id');

        $favourite = FavouriteService::where('user_id', $user->id)
            ->where('service_id', $service_id)
            ->first();

        if (!$favourite) {
            return response()->json(['type' => 'info', 'message' => 'Service not found in your favourites.'], 404);
        }

        $favourite->delete();

        return response()->json(['type' => 'success', 'message' => 'Service removed from your favourites.']);
    }
}
