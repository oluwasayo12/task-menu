<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Item;

class ItemController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'field' => 'required|min:3'
        ];
        $message = [
            'field.required' => 'Item field name is required.'
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $field = $request->input('field');
            $createItem= new Item;
            $createItem->it_field = $field;
            $itemCreated = $createItem->save();
            if ($itemCreated) {
                return response()->json($request->all(), 201);
            } else {
                return response()->json(["error"=>"Unable to create item"], 400);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $item
     * @return \Illuminate\Http\Response
     */
    public function show($item)
    {
        if (empty($item)) {
            return response()->json(["error"=>"Invalid item id"], 400);
        } else {
            $itemDetails = Item::select('it_field')->where([['it_id', '=', $item]])->first();
            if (!empty($itemDetails)) {
                $returnData = [];
                $returnData['field'] = $itemDetails->it_field;
                return response()->json($returnData, 200);
            } else {
                return response()->json(["error"=>"No record found for provided id"], 400);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $item)
    {
        if (empty($item)) {
            return response()->json(["error"=>"Invalid item id"], 400);
        } else {
            $field = $request->input('field');

            $updateDetails = [
                'it_field' => $field
            ];
            $itemUpdate = Item::where('it_id', $item)->update($updateDetails);
            if ($itemUpdate) {
                $returnData = [];
                $returnData['field'] = $field;
                return response()->json($returnData, 200);
            } else {
                return response()->json(["error"=>"Unable to update record"], 400);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($item)
    {
        if (empty($item)) {
            return response()->json(["error"=>"Invalid item id"], 400);
        } else {
            $deleteItem = Item::where([['it_id', '=', $item]])->delete();
            if ($deleteItem) {
                return response()->noContent();
            } else {
                return response()->json(["error"=>"Unable to delete record"], 400);
            }
        }
    }
}
