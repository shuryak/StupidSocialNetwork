<?php

namespace application\lib;

class JsonData {

    public static function check($data, $dataList) {

        $notIsset = [];

        foreach($dataList as $dataItem) {
            if(!isset($data->{$dataItem})) {
                $notIsset[] = $dataItem;
            }
        }

        return $notIsset;
    }

}