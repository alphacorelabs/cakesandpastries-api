<?php

namespace App\Http\Controllers\Api;

use App\Location;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::all();
        return response()->json(['data' => $locations]);
}

// create function to store data
    public function store(Request $request)
    {
        // make name unique
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:locations',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
                }
                // store data
                
        $location = Location::create($request->all());
        return response()->json(['data' => $location]);     


}

// create function to delete data
public function destroy($id)
{   
    $location = Location::find($id);
    $location->delete();
    return response()->json(['data' => $location]);


}
}
