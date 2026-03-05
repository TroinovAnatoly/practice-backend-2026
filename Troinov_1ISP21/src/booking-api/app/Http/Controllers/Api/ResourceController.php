<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Resource::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(auth()->user()->role !== 'admin'){
            return response()->json(['message'=>'Forbidden'],403);
        }

        $validated = $request->validate([
            'name'=>'required|string',
            'capacity'=>'required|integer|min:1',
            'location'=>'required|string'
        ]);

        $resource = Resource::create($validated);

        return response()->json($resource,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $resource = Resource::findOrFail($id);

        $validated = $request->validate([
            'name'=>'string',
            'capacity'=>'integer|min:1',
            'location'=>'string'
        ]);

        $resource->update($validated);

        return $resource;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);
        $resource->delete();

        return response()->json(['message'=>'Deleted']);
    }
}
