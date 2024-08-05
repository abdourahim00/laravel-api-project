<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payments;
use Illuminate\Support\Facades\Http;
use App\Models\Order_items;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Cours;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;



class PaymentController extends Controller
{
    public function index(){
        $payment = Payments::all();

        return response()->success('Liste des paiements', ['payment' => $payment]);
    }

    public static function createPayment($order, $cours_id){
        function post($url, $data = [], $header = [])
        {
            $strPostField = http_build_query($data);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $strPostField);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($header, [
                'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
                'Content-Length: ' . mb_strlen($strPostField)
            ]));

            return curl_exec($ch);
        }

        function generateUniqueRefCommand($îd){
            $uniqueSuffix = Str::random(8);
            $uniqueRefCommand = 'REF-' . $îd . '-' . $uniqueSuffix;
            return $uniqueRefCommand;
        }
        // dd(Cours::find($order->cours_id));
        // dd($order);
        $postFields = [
            "item_name" => Cours::find($cours_id)->title,
            // DB::table("orders")
            // ->join("order_items", "orders.id", "=", "order_items.order_id")
            // ->join("cours", "cours.id", "=", "order_items.cours_id")
            // ->select("cours.title")
            // ->get(),
            "item_price" => $order->total_price,
            "currency" => "xof",
            "ref_command" => "",
            "command_name" => "order_$order->id",
            "env" => env("PAYTECH_PAYMENT_MODE"),
            "custom_field" => "",
            "success_url" => env("PAYTECH_SUCCESS_URL"),
            "cancel_url" => env("PAYTECH_CANCEL_URL"),
            "ipn_url" => env("PAYTECH_IPN_URL"),
            // "ipn_url" => "https://webhook.site/039b8915-d2ca-48b7-b393-5f52252968f3",
        ];
        $payment = Payments::create([
            'order_id' => $order->id,
            'amount' => $order->total_price,
            'payment_method' => 'Paytech',
            'payment_status' => 'unpaid',
        ]);
        $payment->save();

        $postFields["ref_command"] = generateUniqueRefCommand($payment->id);
        $postFields["custom_field"] = json_encode([
            "cours_id" => $cours_id,
            "order_id" => $order->id,
            "payment_id" => $payment->id
        ]);
        // dd($postFields);


        // $paytech = Http::withHeaders([
        //     'API_KEY' => 'Bearer ' . env('PAYTECH_API_KEY'),
        //     'API_SECRET' => env('PAYTECH_API_SECRET'),
        //     'Content-Type' => 'application/json'
        // ])->post('https://paytech.sn/api/payment/request-payment', $postFields);

        $jsonResponse = post('https://paytech.sn/api/payment/request-payment', $postFields, [
            "API_KEY: ".env('PAYTECH_API_KEY'),
            "API_SECRET: ".env('PAYTECH_API_SECRET')
        ]);

        // dd($jsonResponse);

        return json_decode($jsonResponse);
    }


    public function handleIPN(Request $request) {
        // Log the incoming request for debugging purposes
        // Log::info('IPN Request:', $request->all());

        $type_event = $request->type_event;
        $custom_field = json_decode($request->custom_field);

        $ref_command = $request->ref_command;
        $item_name = $request->item_name;
        $item_price = $request->item_price;
        $devise = $request->devise;
        $command_name = $request->command_name;
        $env = $request->env;
        $token = $request->token;
        $api_key_sha256 = $request->api_key_sha256;
        $api_secret_sha256 = $request->api_secret_sha256;

        $my_api_key = env('PAYTECH_API_KEY');
        $my_api_secret = env('PAYTECH_API_SECRET');

        if (hash('sha256', $my_api_secret) === $api_secret_sha256 && hash('sha256', $my_api_key) === $api_key_sha256) {
            // From PayTech
            if ($type_event == "sale_complete") {
                $payment = Payments::where('id', $custom_field->payment_id)->first();

                if ($payment) {
                    $payment->payment_status = "paid";
                    $payment->save();
                }

                $order = Order::where('id', $custom_field->order_id)->first();

                if ($order) {
                    $order->status = "completed";
                    $order->save();
                }

                return response()->json(['message' => 'IPN handled successfully'], 200);
            }
        } else {
            // Not from PayTech
            Log::warning('Unhandled type_event:', ['type_event' => $type_event]);
            return response()->json(['error' => 'Unhandled type_event'], 400);
        }
    }


}
