<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Menu;

class MenuController extends Controller
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
            'field.required' => 'Menu field name is required.'
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }else{
            $field = $request->input('field');
            $max_depth = $request->input('max_depth');
            $max_children = $request->input('max_children');

            $createMenu= new Menu;
            $createMenu->mn_field = $field;
            $createMenu->mn_depth = $max_depth ;
            $createMenu->mn_children = $max_children;
            $menuCreated = $createMenu->save();
            if($menuCreated) return response()->json($request->all(), 201);
            else return response()->json(["error"=>"Unable to create menu"], 400);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function show($menu)
    {
        if(empty($menu)) return response()->json(["error"=>"Invalid menu id"], 400);
        else{
            $menuDetails = Menu::select('mn_field','mn_depth','mn_children')->where([['mn_id', '=', $menu]])->first();
            if(!empty($menuDetails)) {
                $returnData = [];
                $returnData['field'] = $menuDetails->mn_field; 
                if(!is_null($menuDetails->mn_depth) && !is_null($menuDetails->mn_children) ){
                    $returnData['max_depth'] = $menuDetails->mn_depth; 
                    $returnData['max_children'] = $menuDetails->mn_children; 
                }
                return response()->json($returnData, 200);
            }else return response()->json(["error"=>"No record found for provided id"], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $menu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy($menu)
    {
        //
    }
}
