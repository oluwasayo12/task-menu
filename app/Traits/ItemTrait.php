<?php

namespace App\Traits;

use App\Item;

trait ItemTrait
{

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



    private function saveItemChildren($parent, $childrenData)
    {
        $no = 0;

        if (is_array($childrenData)) {
            foreach ($childrenData as $childKey => $childValue) {
                $childField = $childValue['field'];
                
                $createItem= new Item;
                $createItem->it_parent_id = $parent;
                $createItem->it_field = $childField;
                $itemCreated = $createItem->save();
                if ($itemCreated) {
                    $no++;
                }

                if (empty($childValue['children'])) {
                    continue;
                }
                $this->saveItemChildren($parent, $childValue['children']);
            }
        }
        if ($no > 0) {
            return true;
        } else {
            return false;
        }
    }



    private function showItemChildren($parentid)
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
}
