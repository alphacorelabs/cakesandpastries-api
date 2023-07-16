<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;

use Illuminate\Support\Facades\Validator;

use KingFlamez\Rave\Facades\Rave as Flutterwave;
use BJTheCod3r\SmartSms\SmartSms;








class OrderController extends Controller
{
    public function index()
{
    $orders = Order::with('users')->orderBy('created_at', 'desc')->get();
    return response()->json(['data' => $orders]);
}


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_ref' => 'required|unique:orders',
            'user_id' => 'required',
            'amount' => 'required',
            'items' => 'required',
            'address' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'deliveryFee' => 'required',
            'location' => 'required',
            'protein' => 'required'
        ]);
        
        if ($validator->fails()) {    
            return response()->json($validator->messages(), 400);
        }
        
        $url = 'https://api.paystack.co/transaction/verify/'.$request->payment_ref;
        
        //open connection
        // $ch = curl_init();
        //set request parameters 
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer ".env('PAYSTACK_SECRET_KEY').""]);
        
        //send request
        // $req = curl_exec($ch);
        //close connection
        // curl_close($ch);
        //declare an array that will contain the result
        // $result = array();

        // if($req){
        //     $result = json_decode($req, true);
        // }

        // if (array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success')) {
        //     //Save order details
        
        // 08096176758

            $order = Order::create($request->only([
                'payment_ref', 'user_id', 'amount', 'items', 'address', 'name', 'phone', 'deliveryFee', 'location', 'protein'
            ]));

            $order->save();

            return response()->json(['message' => 'Checkout successful. Your order will be processed as soon as possible']);

        // }
        // else{
        //     return response()->json(['message' => 'Invalid transaction. Please try again later'], 400);
        // }
    }

    public function order($id){
        $order = Order::find($id);
        return response()->json(['data' => $order]);
    }

    //complete-order with payment referrence and update status to paid
    public function complete(Request $request){
        $order = Order::where('payment_ref', $request->payment_ref)->first();
        if ($order) {
            $order->status = "paid";
            $order->save();
            // send notification of new order here


            return response()->json(['success' => "Payment confirmed"]);
        }
        else{
            return response()->json(['error' => 'Order not found'], 404);
        }

    }

    public function state(Request $request){
        $order = Order::where('id', $request->id)->first();
        if ($order) {
            $order->state = "completed";
            $order->save();
            return response()->json(['success' => "Order Completed"]);
        }
        else{
            return response()->json(['error' => 'Order not found'], 404);
        }

    }


    public function confirm(Request $request){

        $verified = Flutterwave::verifyWebhook();
        
       
        
        
        // if it is a charge event, verify and confirm it is a successful transaction
        //chief don't forget to check if verfied is true in the next line
    if ( $request->event == 'charge.completed' && $request->data['status'] == 'successful') {

        $termii = new \Zeevx\LaraTermii\LaraTermii("TL0CyBMlQRA7c87RkXgttD2XYeMVUEQUCN8DSmz9VElmucAKHoR5Tlu1v7NR4k");

        // $to = 8096176758;
        $to = 9034222932;
        $from = "CapitalVote";
        $sms = "There's a new order! please login to process it.";
        $channel = "generic";
        $media = false;
        $media_url = null;
        $media_caption = null;
        
        return  $termii->sendMessage($to, $from, $sms, $channel, $media, $media_url, $media_caption);
        
        
        $verificationData = Flutterwave::verifyTransaction($request->data['id']);

        return response()->json(['data' => $verificationData]);

        

        if ($verificationData['status'] === 'success') {



        // process for successful charge
        return response()->json(['success' => "verification stastus is sucessful"]);
        $order = Order::where('payment_ref', $request->data['id'])->first();

            if ($order) {
                $order->status = "paid";
                $order->save();
                
                return response()->json(['success' => "Payment confirmed"]);
            } else {
                return response()->json(['error' => 'Order not found'], 404);
            }


        }

    }

    // if it is a transfer event, verify and confirm it is a successful transfer
    if ($request->event == 'transfer.completed') {

        $termii = new \Zeevx\LaraTermii\LaraTermii("TL0CyBMlQRA7c87RkXgttD2XYeMVUEQUCN8DSmz9VElmucAKHoR5Tlu1v7NR4k");

                // $to = 8096176758;
                $to = 9034222932;
                $from = "CapitalVote";
                $sms = "There's a new order! please login to process it.";
                $channel = "generic";
                $media = false;
                $media_url = null;
                $media_caption = null;
                
              $send = $termii->sendMessage($to, $from, $sms, $channel, $media, $media_url, $media_caption);
              return $send;

              
        $transfer = Flutterwave::transfers()->fetch($request->data['id']);
        
        $order = Order::where('payment_ref', $request->data['id'])->first();
             

        if($transfer['data']['status'] === 'SUCCESSFUL') {
            // update transfer status to successful in your db
           
            


            if ($order) {
                if ($order->status == "paid"){
                    return response()->json(['success' => "Payment already confirmed"], 200);;
                }
                $order->status = "paid";
                $order->save();

                // send notification
                $termii = new \Zeevx\LaraTermii\LaraTermii("TL0CyBMlQRA7c87RkXgttD2XYeMVUEQUCN8DSmz9VElmucAKHoR5Tlu1v7NR4k");

                // $to = 8096176758;
                $to = 9034222932;
                $from = "CapitalVote";
                $sms = "There's a new order! please login to process it.";
                $channel = "generic";
                $media = false;
                $media_url = null;
                $media_caption = null;
                
             $termii->sendMessage($to, $from, $sms, $channel, $media, $media_url, $media_caption);
               return $termii; 
                return response()->json(['success' => "Payment confirmed"]);
            } else {
                return response()->json(['error' => 'Order not found'], 404);
            }
        } else if ($transfer['data']['status'] === 'FAILED') {
                return;
            // update transfer status to failed in your db
            // revert customer balance back
        } else if ($transfer['data']['status'] === 'PENDING') {
            return;
            // update transfer status to pending in your db
        }

    }
  








    // $order = Order::where('payment_ref', $request->payment_ref)->first();
    
    // if ($order) {
    //     $order->status = "paid";
    //     $order->save();
        
    //     return response()->json(['success' => "Payment confirmed"]);
    // } else {
    //     return response()->json(['error' => 'Order not found'], 404);
    // }

        return response()->json(['success' => "done"]);
}


    
    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
