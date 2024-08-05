<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cours;
use App\Models\Order_items;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PaymentController;
class OrderController extends Controller
{
    public function create(Request $request){
        // return response()->json('Test');
        $request->validate([
            'cours_id' => 'required',
            'quantity' => 'required',
            // 'status' => 'required|in:pending,completed,cancelled',
        ]);
        // return response()->json('Test2');

        $current_course = Cours::find($request->cours_id);
        // dd($current_course);

        $order = Order::create([
            // 'cours_id' => $cours_id,
            'user_id' => Auth::user()->id,
            'total_price' => $current_course->price,

            // 'status' => 'pending',
        ]);

        $order->save();

        $current_order_item = Order_items::create([
            'order_id' => $order->id,
            'cours_id' => $current_course->id,
            'quantity' => 1,
        ]);
        $current_order_item->save();


        $payment = PaymentController::createPayment($order, $current_course->id);


        return $payment;
    }
}
