<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;

class MenuItemController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    private function savechildren($mainMenu, $parent, $childrenData)
    {
        $no = 0;

        if (is_array($childrenData)) {
            foreach ($childrenData as $childKey => $childValue) {
                $childField = $childValue['field'];
                
                $createItem= new Item;
                $createItem->it_mn_id = $mainMenu;
                $createItem->it_parent_id = $parent;
                $createItem->it_field = $childField;
                $itemCreated = $createItem->save();
                if ($itemCreated) {
                    $no++;
                }

                if (empty($childValue['children'])) {
                    continue;
                }
                $this->saveChildren($mainMenu, $parent, $childValue['children']);
            }
        }
        if ($no > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function store(Request $request, $menu)
    {
        $totalField = 0;
        foreach ($request->all() as $key => $value) {
            if (empty($value['field'])) {
                continue;
            }
                    
            $fieldValues = $value['field'];
            $createItem= new Item;
            $createItem->it_mn_id = $menu;
            $createItem->it_field = $fieldValues;
            $itemCreated = $createItem->save();
            if ($itemCreated) {
                $totalField++;
            }
            $lastId = $createItem->it_id;

            if (empty($value['children'])) {
                continue;
            }
            $children = $value['children'];
            $subDetails = $this->savechildren($menu, $lastId, $children);
            if ($subDetails) {
                $totalField++;
            }
        }
        if ($totalField > 0) {
            return response()->json($request->all(), 201);
        } else {
            return response()->json(["error"=>"Unable to create record"], 400);
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
