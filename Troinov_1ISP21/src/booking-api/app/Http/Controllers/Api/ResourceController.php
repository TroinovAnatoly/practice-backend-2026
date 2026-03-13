<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resource;

class ResourceController extends Controller
{

    public function index()
    {
        $resources = Resource::all();
        return response()->json($resources);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'capacity' => 'required|integer',
            'floor' => 'required|integer',
            'has_projector' => 'required|boolean',
            'has_whiteboard' => 'required|boolean',
        ]);

        $resource = Resource::create($request->all());

        return response()->json($resource,201);
    }

    public function update(Request $request,$id)
    {
        $resource = Resource::findOrFail($id);

        $resource->update($request->all());

        return response()->json($resource);
    }

    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);

        $resource->delete();

        return response()->json([
            'message'=>'Resource deleted'
        ]);
    }

}