<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{

    public function index(Request $request)
    {
        $query = Booking::with('resource')->where('user_id', auth()->id());

        if ($request->has('resource_id')) {
            $query->where('resource_id', $request->resource_id);
        }

        if ($request->has('date')) {
            $query->whereDate('start_time', $request->date);
        }

        $bookings = $query->orderBy('start_time', 'asc')->paginate(10);
        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        $user = auth()->user();

        // запрет бронирования в прошлом
        if ($request->start_time < now()) {
            return response()->json([
                'message' => 'Cannot book in the past'
            ],400);
        }

        // проверка конфликта времени
        $conflict = Booking::where('resource_id',$request->resource_id)
            ->where(function($q) use ($request){
                $q->whereBetween('start_time',[$request->start_time,$request->end_time])
                  ->orWhereBetween('end_time',[$request->start_time,$request->end_time]);
            })
            ->exists();

        if($conflict){

            Log::warning('Booking rejected due to conflict',[
                'user_id'=>auth()->id(),
                'resource_id'=>$request->resource_id
            ]);

            return response()->json([
                'message'=>'Time slot already booked'
            ],409);
        }

        $booking = Booking::create([
            'user_id'=>$user->id,
            'resource_id'=>$request->resource_id,
            'start_time'=>$request->start_time,
            'end_time'=>$request->end_time,
            'status'=>'active'
        ]);

        Log::info('Booking created',[
            'user_id'=>$user->id,
            'resource_id'=>$request->resource_id,
            'start'=>$request->start_time
        ]);

        return response()->json($booking,201);
    }

    public function show($id)
    {
        $booking = Booking::findOrFail($id);

        if(auth()->user()->id !== $booking->user_id && auth()->user()->role !== 'admin'){
            return response()->json([
                'message'=>'Access denied'
            ],403);
        }

        return response()->json($booking);
    }

    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);

        if($booking->user_id !== auth()->id()){
            return response()->json([
                'message'=>'You cannot cancel this booking'
            ],403);
        }

        if(now() > $booking->start_time){
            return response()->json([
                'message'=>'Booking already started'
            ],400);
        }

        $booking->status = 'cancelled';
        $booking->save();

        return response()->json([
            'message'=>'Booking cancelled'
        ]);
    }

}