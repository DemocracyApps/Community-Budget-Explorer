<?php namespace DemocracyApps\GB\Accounts;

use DemocracyApps\GB\Utility\EloquentPropertiedObject;

class DataItem extends EloquentPropertiedObject
{
    protected $table = 'data_items';

    public function addCategories ($categories) {
        $size = sizeof($categories);
        if ($size>0) $this->category1 = $categories[0];
        if ($size>1) $this->category2 = $categories[1];
        if ($size>2) $this->category3 = $categories[2];
        if ($size>3) {
            $spill = array();
            for ($i=0; $i<$size-3; ++$i) {
                $spill[] = $categories[3+$i];
            }
            $this->categoryN = json_encode($spill);
        }
    }



}