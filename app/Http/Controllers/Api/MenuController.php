<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Menu;
use App\Order;
use App\Protein;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function index()
    {
        $menu = Menu::with('category')->orderBy('created_at', 'asc')->get();
        return response()->json(['data' => $menu]);
    }

    public function protein()
    {
        $protein = Protein::where('isAvailable', 1)->get();
        return response()->json(['data' => $protein]);
    }

    
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'name' => 'required|unique:menus',
            'price' => 'required',
            'image' => 'required',
            'measure' => 'required',
            'isAvailable' => 'required',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 400);
        }

        $menu = Menu::create($request->only([
            'category_id',
            'name',
            'price',
            'image',
            'measure',
            'isAvailable'
        ]));
        
        $menu->save();

        $response = Menu::with('category')->where('id', $menu->id)->get();
        return response()->json(['data' => $response]);

    }

    //add protein

    public function proteinAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:proteins',
            'price' => 'required',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 400);
        }

        $menu = Protein::create($request->only([
            'name',
            'price',
        ]));
        
        $menu->save();

        $response = Protein::where('id', $menu->id)->get();
        return response()->json(['data' => $response]);

    }

    // delete protien

    public function proteinDelete($id)
    {
        $category = Protein::find($id);
        $category->delete();

        return response()->json('Protein successfully removed');
    }

    //function to show menu item by id
    public function show($id)
    {
        $menu = Menu::find($id);
        return response()->json(['data' => $menu]);
    }
    
     //function to get total orders count where status is delivered
    public function proccessedOrders(){
        $processedOrders = Order::where('state', 'completed')->count();
        return response()->json(['data' => $processedOrders]);
    }


    //function to get total sales in order table where status is delivered
    public function totalSales(){
        $totalSales = Order::where('status', 'paid')->sum('amount');
        return response()->json(['data' => $totalSales]);
    }

    //function to get pending orders count
    public function pendingOrders(){
        $pendingOrders = Order::where('status', 'pending')->count();
        return response()->json(['data' => $pendingOrders]);
    }
   

    
        public function update(Request $request, $id)
{
    $menu = Menu::where('id', $id)->first();

    $fieldsToUpdate = [];

    if (!is_null($request->category_id)) {
        $fieldsToUpdate['category_id'] = $request->category_id;
    }

    if (!is_null($request->name)) {
        $fieldsToUpdate['name'] = $request->name;
    }

    if (!is_null($request->price)) {
        $fieldsToUpdate['price'] = $request->price;
    }

    if (!is_null($request->image)) {
        $fieldsToUpdate['image'] = $request->image;
    }

    if (!is_null($request->isAvailable)) {
        $fieldsToUpdate['isAvailable'] = $request->isAvailable;
    }

    $menu->update($fieldsToUpdate);

    return response()->json(['data' => $menu, 'message' => 'Menu item successfully updated']);
}

    
    public function destroy($id)
    {
        $menu = Menu::find($id);
        $menu->delete();

        return response()->json('Menu Item successfully removed');
    }
}
