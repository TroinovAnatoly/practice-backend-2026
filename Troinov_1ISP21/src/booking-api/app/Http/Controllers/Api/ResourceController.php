<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resource;

class ResourceController extends Controller
{

    public function index(Request $request) {
        $query = Resource::query();

        // Фильтры
        if ($request->has('floor')) {
            $query->where('floor', $request->floor);
        }
        if ($request->has('has_projector')) {
            $query->where('has_projector', $request->has_projector);
        }
        if ($request->has('has_whiteboard')) {
            $query->where('has_whiteboard', $request->has_whiteboard);
        }
        if ($request->has('min_capacity')) {
            $query->where('capacity', '>=', $request->min_capacity);
        }

        // Поиск по имени или описанию
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        // Сортировка
        $sortBy = $request->get('sort_by', 'name'); // имя по умолчанию
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Пагинация
        $resources = $query->paginate(10);

        // Добавление среднего рейтинга
        $resources->getCollection()->transform(function($resource){
            $resource->average_rating = $resource->reviews()->avg('rating');
            return $resource;
        });

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