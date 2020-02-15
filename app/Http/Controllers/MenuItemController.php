<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ItemTrait;
use App\Item;

class MenuItemController extends Controller
{
    use ItemTrait;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

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
        if (empty($menu)) {
            return response()->json(["error"=>"Invalid menu id"], 400);
        } else {
            $menuItems = Item::select('it_id', 'it_field')->where('it_mn_id', $menu)->whereNull('it_parent_id')->get();
            $items = [];
            foreach ($menuItems as $key => $item) {
                $parent = $item->it_id;
                
                $childrenData = $this->showChildren($parent);

                $items['field'] = $item->it_field;
                if (!empty($childrenData)) {
                    $items['children'] = $childrenData;
                }
                $allItems[] = $items;
            }
            if (empty($allItems)) {
                return response()->json(["error"=>"No record found"], 400);
            } else {
                return $allItems;
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy($menu)
    {
        if (empty($menu)) {
            return response()->json(["error"=>"Invalid menu id"], 400);
        } else {
            $deleteMenu = Item::where([['it_mn_id', '=', $menu]])->delete();
            if ($deleteMenu) {
                return response()->noContent();
            } else {
                return response()->json(["error"=>"Unable to delete record"], 400);
            }
        }
    }
}
