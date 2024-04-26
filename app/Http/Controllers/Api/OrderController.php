<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\OrderItem;
use App\OrderItemProtein;
use Illuminate\Support\Facades\Validator;

use KingFlamez\Rave\Facades\Rave as Flutterwave;
use BJTheCod3r\SmartSms\SmartSms;
use ManeOlawale\Laravel\Termii\Facades\Termii;








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
            'items' => 'required',
            'deliveryMethod' => 'required',
            'totalAmount' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }




        //prepare payment intent from fluuterwave
        $reference = Flutterwave::generateReference();


        //payment details
        $data = [
            'tx_ref' => $reference,
            'amount' => $request->input('totalAmount'),
            'currency' => 'NGN',
            'redirect_url' => 'https://cakesandpastries.ng/',
            'payment_options' => 'card,banktransfer',
            
            'customer' => [
                'email' => $request->input('email'),
                'phonenumber' => $request->input('phoneNumber')
            ],
            'customizations' => [
                'title' => 'Payment for ' . 'Cakes and Pastries Food Purchase',
                'description' => 'Payment for ' . 'Food Purchase'
            ]
        ];

        $payment = Flutterwave::initializePayment($data);
    
        // Prepare order details deploy
        $order = new Order();
        $order->payment_ref = $reference;
        $order->amount = $request->input('totalAmount');
        $order->deliveryFee = $request->input('location.price');
        $order->address = $request->input('address');
        $order->location = $request->input('location.name');
        $order->phone = $request->input('phoneNumber');
        $order->save();
    
        // Save order items
        foreach ($request->input('items') as $item) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->menu_id = $item['id'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->price = $item['price'];
            $orderItem->save();
    
            // Save order item proteins
            if (isset($item['protein'])) {
                foreach ($item['protein'] as $protein) {
                    $orderItemProtein = new OrderItemProtein();
                    $orderItemProtein->order_item_id = $orderItem->id;
                    $orderItemProtein->protein_id = $protein['id'];
                    $orderItemProtein->quantity = $protein['quantity'];
                    $orderItemProtein->price = $protein['price'];
                    $orderItemProtein->save();
                }
            }
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Checkout started successfully',
            'payment' => $payment
        ]);
    }







    public function order($id)
    {
        $order = Order::find($id);
        return response()->json(['data' => $order]);
    }

    //complete-order with payment referrence and update status to paid
    public function complete(Request $request)
    {
        $order = Order::where('payment_ref', $request->payment_ref)->first();
        if ($order) {
            $order->status = "paid";
            $order->save();
            // send notification of new order here


            return response()->json(['success' => "Payment confirmed"]);
        } else {
            return response()->json(['error' => 'Order not found'], 404);
        }
    }

    public function state(Request $request)
    {
        $order = Order::where('id', $request->id)->first();
        if ($order) {
            $order->state = "completed";
            $order->save();
            return response()->json(['success' => "Order Completed"]);
        } else {
            return response()->json(['error' => 'Order not found'], 404);
        }
    }


    public function confirm(Request $request)
    {

        //test the sms here
        $termii = new \Zeevx\LaraTermii\LaraTermii("TL0CyBMlQRA7c87RkXgttD2XYeMVUEQUCN8DSmz9VElmucAKHoR5Tlu1v7NR4k");


        $to = "2348096176758";
        $from = "CakesnP";
        $sms = "There's a new order! please login to process it.";
        $channel = "dnd";
        $media = false;
        $media_url = null;
        $media_caption = null;

        return $termii->sendMessage($to, $from, $sms, $channel, $media, $media_url, $media_caption);

        // end test

        $verified = Flutterwave::verifyWebhook();




        // if it is a charge event, verify and confirm it is a successful transaction
        //chief don't forget to check if verfied is true in the next line
        if ($request->event == 'charge.completed' && $request->data['status'] == 'successful') {

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

            // Termii::send('2348096176758', 'Hello World!');
            // return response()->json(['success' => "Payment already confirmed"], 200);





            $transfer = Flutterwave::transfers()->fetch($request->data['id']);

            $order = Order::where('payment_ref', $request->data['id'])->first();


            if ($transfer['data']['status'] === 'SUCCESSFUL') {
                // update transfer status to successful in your db




                if ($order) {
                    if ($order->status == "paid") {
                        return response()->json(['success' => "Payment already confirmed"], 200);
                    }
                    $order->status = "paid";
                    $order->save();

                    // send notification
                    $termii = new \Zeevx\LaraTermii\LaraTermii("TL0CyBMlQRA7c87RkXgttD2XYeMVUEQUCN8DSmz9VElmucAKHoR5Tlu1v7NR4k");

                    // $to = 8096176758;
                    $to = 2349034222932;
                    $from = "CapitalVote";
                    $sms = "There's a new order! please login to process it.";
                    $channel = "whatsapp";
                    $media = false;
                    $media_url = null;
                    $media_caption = null;

                    $termii->sendMessage($to, $from, $sms, $channel, $media, $media_url, $media_caption);
                    return $termii;
                    return response()->json(['success' => "Payment confirmed"], 200);
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
