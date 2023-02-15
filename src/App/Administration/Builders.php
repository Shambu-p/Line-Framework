<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 5/26/2021
 * Time: 8:46 AM
 */

namespace Absoft\Line\App\Administration;


use Application\conf\DirConfiguration;
use Absoft\Line\App\Files\Resource;
use Absoft\Line\Core\DbConnection\Attributes\SQL\Attribute;
use Absoft\Line\Core\FaultHandling\Errors\BuildersFolderNotFound;
use Absoft\Line\Core\FaultHandling\Errors\ClassNotFound;
use Absoft\Line\Core\FaultHandling\Errors\FileNotFound;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\Modeling\DbBuilders\Builder;

class Builders {

    private $builder_content = '<?php
namespace Application\Builders;

use Absoft\Line\Core\Modeling\DbBuilders\Builder;
use Absoft\Line\Core\Modeling\DbBuilders\Schema;


class @_name extends Builder{

    function construct(Schema $table, $table_name = "@_name"){

        $this->TABLE_NAME = $table_name;

        $this->ATTRIBUTES = [
            //@att_start
            $table->autoincrement("id"),
            //@att_end
        ];
        
        $this->HIDDEN_ATTRIBUTES = [
            //@hide_start
            //@hide_end
        ];

    }

}

        ';

    /**
     * @param $name
     * @return boolean
     * @throws BuildersFolderNotFound
     * @throws OperationFailed
     *
     * this method will generate builder file
     */
    public function create($name){

        $content = str_replace("@_name", $name, $this->builder_content);

        if(!Resource::checkFile(DirConfiguration::$dir["builders"]."/".$name.".php")){

            if(file_put_contents(DirConfiguration::$_main_folder.DirConfiguration::$dir["builders"]."/".$name.".php", $content)){
                return true;
            }

        }else{
            throw new OperationFailed("file exist".DirConfiguration::$_main_folder.DirConfiguration::$dir["builders"]);
        }

        return false;

    }

    /**
     * @param $name
     * parameter name is the name of the attribute that is
     * going to be added
     * @param $table
     * parameter table is the builder name
     * @param $category
     * parameter category is the definition of the attribute
     * wither it is hidden or not hidden
     * @param $type
     * parameter type defines datatype of the new attribute
     * @param $length
     * parameter length defines the length of the attribute
     * @param $sign
     * parameter sign defines wither the attribute is negative
     * or positive
     * @param $nullable
     * parameter nullable defines wither the parameter can be null
     * @param $on
     * if the attribute is foreign parameter "on" will defines
     * which attribute of the foreign builder it refers to.
     * @param $Reference
     * if the attribute is foreign key parameter "Reference"
     * defines which builder it refers to.
     * @param $autoincrement
     * parameter autoincrement defines wither the attribute
     * should increment by itself or not. only works if the
     * attribute type is integer
     * @param $unique
     * parameter unique defines wither the attribute values
     * should be unique or not
     * @param $setPrimaryKey
     * parameter setPrimaryKey defines the attribute as primary key
     *
     * @return bool
     * @throws FileNotFound
     *
     * this method add a new attribute to the builder file
     * with the name provided on the table parameter
     */
    public function addAttribute($name, $table, $category, $type, $length, $sign, $nullable, $on, $Reference, $autoincrement, $unique, $setPrimaryKey){

        $entity_full_address = DirConfiguration::$_main_folder.DirConfiguration::$dir["builders"]."/".$table.".php";

        if(file_exists($entity_full_address)) {

            $entity_total_content = file_get_contents($entity_full_address);

            $ret_array = $this->divideEntity($entity_total_content);

            if (sizeof($ret_array)) {

                $attributes = explode(",", $ret_array[$category]);
                $att_size = sizeof($attributes);

                if ($att_size > 0 && trim("\t", trim("\r", trim("\n", trim(" ", $attributes[$att_size - 1])))) != "") {

                    $last_exp = explode("->", $attributes[$att_size - 1]);

                    if (sizeof($last_exp) > 1) {

                        $last_exp[sizeof($last_exp) - 1] = explode(")", $last_exp[sizeof($last_exp) - 1])[0] . ")";
                        $attributes[sizeof($attributes) - 1] = implode("->", $last_exp);
                        $schema_object = explode("->", $attributes[0])[0];

                    } else {
                        $attributes = [];
                        $schema_object = "\r\n\t\t\t\$table";
                    }

                } else {
                    $schema_object = "\r\n\t\t\t\$table";
                }

                $properties[0] = $schema_object;
                $properties[1] = "$type(\"$name\")";

                $properties[] = "length($length)";
                $properties[] = "sign($sign)";
                $properties[] = "unique($unique)";
                $properties[] = "nullable($nullable)";
                $properties[] = "autoincrement($autoincrement)";
                $properties[] = "on(\"$on\")";
                $properties[] = "Reference(\"$Reference\")";
                $properties[] = "setPrimaryKey(\"$setPrimaryKey\")";

                //-----------------------saving file ---------------------

                if (sizeof($properties) > 1) {

                    $temp = $properties[0];

                    for ($i = 1; $i < sizeof($properties); $i++) {

                        $temp .= "->" . $properties[$i];

                    }

                    $attributes[] = $temp;

                }

                $ret_array[$category] = implode(",", $attributes);

                if ($category == "attributes") {

                    $entity_total_content = $ret_array["top_content"] . "//@att_start" . $ret_array["attributes"] . "\r\n\t\t\t//@att_end" . $ret_array["middle_content"] . "//@hide_start" . $ret_array["hidden"] . "//@hide_end" . $ret_array["bottom_content"];

                } else {

                    $entity_total_content = $ret_array["top_content"] . "//@att_start" . $ret_array["attributes"] . "//@att_end" . $ret_array["middle_content"] . "//@hide_start" . $ret_array["hidden"] . "\r\n\t\t\t//@hide_end" . $ret_array["bottom_content"];

                }

                file_put_contents($entity_full_address, $entity_total_content);

                return true;

            } else {

                return false;

            }

        }else{
            throw new FileNotFound($entity_full_address, __FILE__, __LINE__);
        }

    }

    public function createAttribute($name, $table, $category, $type, $length, $sign, $nullable, $on, $Reference, $autoincrement, $unique, $setPrimaryKey){

        $entity_full_address = DirConfiguration::$_main_folder.DirConfiguration::$dir["builders"]."/".$table.".php";

        if(file_exists($entity_full_address)){
            throw new FileNotFound($entity_full_address, __FILE__, __LINE__);
        }

        $entity_total_content = file_get_contents($entity_full_address);

        $full_name = "Application\\Builders\\".$table;
        $return = [];

        /** @var $builder Builder */
        if($builder = new $full_name){

            if(isset($builder->ATTRIBUTES->$name)){
                throw new OperationFailed("Attribute name already exist!");
            }

            $new_attribute_string = "\$table->$type(\"$name\")";
            $new_attribute_string .= $length != null ? "->length(\"$length\")" : "";
            $new_attribute_string .= $nullable != null ? "->nullable(\"$nullable\")" : "";
            $new_attribute_string .= $sign != null ? "->sign(\"$sign\")" : "";
            $new_attribute_string .= $unique != null ? "->unique(\"$unique\")" : "";
            $new_attribute_string .= $autoincrement != null ? "->auto_increment(\"$autoincrement\")" : "";
            $new_attribute_string .= $setPrimaryKey != null ? "->setPrimaryKey(\"$setPrimaryKey\")" : "";
            $new_attribute_string .= $Reference != null ? "->Reference(\"$Reference\")" : "";
            $new_attribute_string .= $on != null ? "->on(\"$on\")" : "";

            if($category == "hidden"){
                str_replace("//@hide_end", $new_attribute_string . "\r\n\t\t\t//@hide_end", $entity_total_content);
            }else if($category == "attributes"){
                str_replace("//@att_end", $new_attribute_string . "\r\n\t\t\t//@att_end", $entity_total_content);
            }

        }

    }

    // $category, $type, $length, $sign, $nullable, $on, $Reference, $autoincrement, $unique, $setPrimaryKey
    public function updateAttribute($old_name, $table, $changed){
    
        $full_name = "Application\\Builders\\".$table;

        /** @var $builder Builder */
        if($builder = new $full_name) {

            /** @var $attribute Attribute */
            foreach ($builder->ATTRIBUTES as $attribute) {

                if($attribute->name == $old_name){

                    if($attribute->type == "text" && $attribute->length == 100){
                        $type = "hidden";
                    }else if($attribute->type == "varchar"){
                        $type = "string";
                    }else {
                        $type = $attribute->type;
                    }

                    $current = "\$table->$type(\"$old_name\")";
                }
            }
        }

    }

    /**
     * @param $name
     * @return bool
     * @throws FileNotFound
     * this method will remove/delete user/developer defined builder
     * file. it will return true or false.
     */
    public function delete($name){

        if(Resource::checkFile(DirConfiguration::$dir["builders"]."/".$name.".php")){

            if(unlink(DirConfiguration::$_main_folder.DirConfiguration::$dir["builders"]."/".$name.".php")){
                return true;
            }

        }else{
            throw new FileNotFound(DirConfiguration::$_main_folder.DirConfiguration::$dir["builders"]."/".$name.".php", __File__, __Line__);
        }

        return false;

    }

    /**
     * @param $name
     * parameter name is the name of the builder class
     *
     * @return mixed
     * @throws ClassNotFound
     *
     * this method will create table on the database configured
     * on database configuration file.
     */
    public function execute($name){

        $str_entity_name = 'Application\Builders\\'.$name;

        /** @var Builder $builder */
        $builder = new $str_entity_name;

        if($builder){
            return $builder->create();
        }else{
            throw new ClassNotFound($str_entity_name, __FILE__, __LINE__);
        }

    }

    /**
     * @param $name
     * parameter name is the name of the builder
     * @return mixed
     * @throws ClassNotFound
     * this method contains the logic to drop table
     * from the database that the system going to use
     */
    public function drop($name){

        $str_entity_name = 'Application\Builders\\'.$name;

        /** @var Builder $builder **/
        if($builder = new $str_entity_name){
            return $builder->drop();
        }else{
            throw new ClassNotFound($str_entity_name, __FILE__, 44);
        }

    }

    /**
     * this method will create all the tables
     * that are defined as builder for the system
     */
    public function executeAll(){

    }

    /**
     * @return array
     * @throws FileNotFound
     * @throws BuildersFolderNotFound
     *
     * this method will provide list of all the builders
     * that the system contains.
     */
    public function all(){

        $return = [];

        if(Resource::checkFile(DirConfiguration::$dir["builders"])){

            if(is_dir(DirConfiguration::$_main_folder.DirConfiguration::$dir["builders"])){

                $list = dir(DirConfiguration::$_main_folder.DirConfiguration::$dir["builders"]);

                while(($file = $list->read()) != false) {

                    if ($file == "." || $file == "..") {

                        continue;

                    } else {

                        $return[] = substr($file, 0, strpos($file, ".php"));

                    }

                }


            }
            else{

                throw new BuildersFolderNotFound(DirConfiguration::$_main_folder.DirConfiguration::$dir["builders"], __FILE__, __LINE__);
                //$return["error_message"] = " DatabaseBuilder folder has been misplaced or file type has been changed ";

            }

        }
        else{

            throw new FileNotFound(DirConfiguration::$_main_folder.DirConfiguration::$dir["builders"], __FILE__, __LINE__);
            //$return["error_message"] = " DatabaseBuilder folder has been deleted or misplaced. ";

        }

        return $return;

    }

    private function divideEntity($content){

        $return = [];

        $first_explosion = explode("//@att_start", $content);

        $return["top_content"] = $first_explosion[0];

        $sec_explosion = explode("//@att_end", $first_explosion[1]);

        $return["attributes"] = $sec_explosion[0];

        $third_explosion = explode("//@hide_start", $sec_explosion[1]);

        $return["middle_content"] = $third_explosion[0];

        $fourth_explosion = explode("//@hide_end", $third_explosion[1]);

        $return["hidden"] = $fourth_explosion[0];

        $return["bottom_content"] = $fourth_explosion[1];

        return $return;

    }

}
