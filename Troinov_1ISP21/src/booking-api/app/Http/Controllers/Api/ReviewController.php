<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Booking;

class ReviewController extends Controller
{
    /**
     * Список отзывов с фильтрацией, сортировкой и пагинацией
     */
    public function index(Request $request)
    {
        $query = Review::query();

        // Фильтрация по booking_id
        if ($request->has('booking_id')) {
            $query->where('booking_id', $request->booking_id);
        }

        // Фильтрация по ресурсам через бронирование
        if ($request->has('resource_id')) {
            $query->whereHas('booking', function($q) use ($request) {
                $q->where('resource_id', $request->resource_id);
            });
        }

        // Сортировка
        $sortField = $request->get('sort_field', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query->orderBy($sortField, $sortOrder);

        // Пагинация
        $perPage = $request->get('per_page', 10);
        $reviews = $query->with(['booking', 'booking.resource'])->paginate($perPage);

        return response()->json($reviews);
    }

    /**
     * Создать отзыв
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        if (auth()->id() !== $booking->user_id && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $review = Review::create([
            'booking_id' => $booking->id,
            'resource_id' => $booking->resource_id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json($review, 201);
    }

    /**
     * Показать один отзыв
     */
    public function show($id)
    {
        $review = Review::with(['booking', 'booking.resource'])->findOrFail($id);

        // Проверяем доступ
        if (auth()->id() !== $review->booking->user_id && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return response()->json($review);
    }

    /**
     * Обновить отзыв
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        // Проверка доступа
        if (auth()->id() !== $review->booking->user_id && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update($request->only(['rating', 'comment']));

        return response()->json($review);
    }

    /**
     * Удалить отзыв
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        // Проверка доступа
        if (auth()->id() !== $review->booking->user_id && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted']);
    }
}