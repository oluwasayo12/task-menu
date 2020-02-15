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



    private function showChildren($parentid)
    {
        $childrenData = Item::select('it_id', 'it_field')->where('it_parent_id', $parentid)->get();

        $childrenFields = [];
        $finalData = [];
        foreach ($childrenData as $key => $value) {
            $childrenFields['field'] = $value->it_field;
            $querySubchild = Item::select('it_field')->where('it_parent_id', $value->it_id)->get();
            foreach ($querySubchild as $key => $subchild) {
                $childrenFields['children'][]['field'] = $subchild->it_field;
            }
            $finalData[] = $childrenFields;
        }

        return $finalData;
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
                $items['children'] = $childrenData;
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
