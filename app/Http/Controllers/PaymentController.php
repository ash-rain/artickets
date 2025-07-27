<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'exists:seats,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $payments = [];
            foreach ($request->seat_ids as $seatId) {
                $seat = Seat::findOrFail($seatId);

                if ($seat->payment) {
                    return response()->json(['error' => "Seat $seatId is already taken"], 409);
                }

                $payment = Payment::create([
                    'seat_id' => $seatId,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ]);

                $payments[] = $payment;
            }

            DB::commit();
            return response()->json($payments, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
