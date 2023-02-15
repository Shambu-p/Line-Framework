<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 1/18/2020
 * Time: 9:42 PM
 */

namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQL;

class QueryConstructor{
    public $query;
    public $values;

    static function singleDerdari(array $arr){
        return implode(", ", $arr);
    }

    /**
     * @param array $arr
     * @param $placeholder
     * @return array
     */
    static function setDerdari($arr, $placeholder){

        $return = array();
        $array = array();
        $query = "";

        $count = 0;

        foreach($arr as $key => $value){

            if($count == 0){

                $query = $query.$key." = :".$placeholder."_".$key;

            }else{

                $query = $query.", ".$key." = :".$placeholder."_".$key;

            }

            $array[$placeholder."_".$key] = $value;
            $count += 1;

        }

        $return['query'] = $query;
        $return['values'] = $array;

        return $return;
    }

    static function conditionDerdari(array $arr){

        $return = "";
        $count = 0;
        $cnt = [];

        foreach($arr as $value){

            $name = str_replace(".", "_", $value["name"]);

            if(isset($cnt[$name])){

                $cnt[$name] += 1;

            }else{
                $cnt[$name] = 1;
            }

            if($count == 0){

                $return .= $value["name"]." ". $value['equ'] ." :condition_".$cnt[$name].$name;

            }else{

                if($value['det'] == "and" || $value['det'] == "or"){

                    $return .= " ". $value['det'] ." ".$value["name"]." ". $value['equ'] ." :condition_".$cnt[$name].$name;

                }else{

                    $return .= " and ".$value['name']." ". $value['equ'] ." :condition_".$cnt[$name].$name;

                }

            }

            $count += 1;

        }

        return $return;
    }

}
