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
            'card_number' => 'required|string',
            'expiration' => 'required|string',
            'cvv' => 'required|string',
            'email' => 'required|email',
        ]);

        try {
            DB::beginTransaction();

            // Check seat availability
            $seats = Seat::whereIn('id', $request->seat_ids)
                ->where('available', true)
                ->lockForUpdate()
                ->get();

            if ($seats->count() !== count($request->seat_ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more seats are no longer available'
                ], 400);
            }

            // Create payment record
            $payment = Payment::create([
                'event_id' => $request->event_id,
                'amount' => $seats->sum('price'),
                'card_number' => $request->card_number,
                'expiration' => $request->expiration,
                'cvv' => $request->cvv,
                'email' => $request->email,
            ]);

            // Update seats to mark as unavailable
            Seat::whereIn('id', $request->seat_ids)->update([
                'available' => false,
                'payment_id' => $payment->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'payment_id' => $payment->id
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
