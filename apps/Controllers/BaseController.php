<?php
namespace Application\Controllers;

use Absoft\Line\Core\Modeling\Controller;

class BaseController extends Controller{

    private $MODEL_OBJECT;

    function __construct($model) {
        $this->MODEL_OBJECT = $model;
    }

    /**
     * this method will set routes for the given 
     *
     * @param [type] $name
     * @param [type] $object
     * @return void
     */
    public static function setRoute($name, $object){
        
        $pk = $object->MODEL_OBJECT->getEntity()->PRIMARY_KEY;
        $columns = [];

        foreach($object->MODEL_OBJECT->MAINS  as $col){
            $columns[$col] = ["required"];
        }

        Route::get(
            "/$name/show",
            [$object, 'show']
        );

        Route::get(
            "/$name/view",
            [$object, 'view'],
            [ "id" => ["required"] ]
        );

        Route::post(
            "/$name/save",
            [$object, 'save'],
            $columns
        );

        Route::post(
            "/$name/update",
            [$object, 'update'],
            $columns
        );

        Route::post(
            "/$name/delete",
            [$object, 'update'],
            ["id" => ["required"]]
        );

    }

    private function show(){
        //TODO: here write showing codes to be Executed
        return "";
    }
    
    private function view($request){
        //TODO: here write viewing codes to be Executed
        return "";
    }

    private function save($request){
        //TODO: Here write save codes to be Executed
        return "";
    }
    
    public function update($request){
        //TODO: here write updating codes to be Executed
        return "";
    }
    
    private function delete($request){
        //TODO: here write deleting codes to be Executed
        return "";
    }

}
?>