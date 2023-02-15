<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 5/30/2021
 * Time: 1:52 PM
 */

namespace Absoft\Line\App\Administration;


use Absoft\Line\Core\DbConnection\Attributes\SQL\Attribute;
use Absoft\Line\Core\FaultHandling\Errors\ClassNotFound;
use Absoft\Line\Core\Modeling\DbBuilders\Builder;
use Absoft\Line\Core\Modeling\Models\Model;

class Table
{

    /**
     * @param $builder_name
     * this parameter accepts string as the of Builder name
     * @return array
     * @throws ClassNotFound
     * this method will provide all the structure of certain Table/Builder
     */
    function schema($builder_name){

        $full_name = "Application\\Builders\\".$builder_name;
        $return = [];

        /** @var $builder Builder */
        if($builder = new $full_name){

            $return["name"] = $builder->TABLE_NAME;
            $return["attributes"] = [];
            $return["hidden"] = [];

            $count = 0;
            /** @var $attribute Attribute */
            foreach ($builder->ATTRIBUTES as $attribute){

                $return["attributes"][$count]["name"] = $attribute->name;
                $return["attributes"][$count]["type"] = $attribute->type;
                $return["attributes"][$count]["length"] = $attribute->length;
                $return["attributes"][$count]["nullable"] = $attribute->nullable;
                $return["attributes"][$count]["auto_increment"] = $attribute->auto_increment;
                $return["attributes"][$count]["sign"] = $attribute->sign;
                $return["attributes"][$count]["unique"] = $attribute->unique;
                $return["attributes"][$count]["foreign"] = $attribute->foreign;
                $return["attributes"][$count]["reference"] = $attribute->reference;
                $return["attributes"][$count]["key"] = $attribute->key;

                $count += 1;

            }

            $count = 0;
            /** @var $attribute Attribute */
            foreach ($builder->HIDDEN_ATTRIBUTES as $attribute){

                $return["hidden"][$count]["name"] = $attribute->name;
                $return["hidden"][$count]["type"] = $attribute->type;
                $return["hidden"][$count]["length"] = $attribute->length;
                $return["hidden"][$count]["nullable"] = $attribute->nullable;
                $return["hidden"][$count]["auto_increment"] = $attribute->auto_increment;
                $return["hidden"][$count]["sign"] = $attribute->sign;
                $return["hidden"][$count]["unique"] = $attribute->unique;
                $return["hidden"][$count]["foreign"] = $attribute->foreign;
                $return["hidden"][$count]["reference"] = $attribute->reference;
                $return["hidden"][$count]["key"] = $attribute->key;

                $count += 1;

            }

            return $return;

        }else{
            throw new ClassNotFound($builder_name, "Administration file", __LINE__);
        }

    }

    function record($builder_name){

        $full_name = "Application\\Models\\".$builder_name."Model";

        /** @var $model Model */
        if($model = new $full_name){

            return $model->searchRecord([], []);

        }else{

            throw new ClassNotFound($builder_name."Model", "Administration file", __LINE__);
        }

    }

}
