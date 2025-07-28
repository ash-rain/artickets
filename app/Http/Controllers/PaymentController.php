<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'exists:seats,id',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Check seat availability
            $seats = Seat::whereIn('id', $request->seat_ids)
                ->lockForUpdate()
                ->get();

            if ($seats->count() !== count($request->seat_ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more seats are no longer available'
                ], 400);
            }

            // Create payment records
            foreach ($seats as $seat) {
                if ($seat->payment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'One or more seats are already booked'
                    ], 400);
                }

                Payment::create([
                    'seat_id' => $seat->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
