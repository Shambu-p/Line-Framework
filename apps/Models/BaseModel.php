<?php
namespace Application\Models;

use Absoft\Line\Core\Modeling\Models\Model;

abstract class BaseModel extends Model{

    /*    public $MAINS = ["id", "username", "f_name"];    */
    
    //As the name indicate this is the Table name of the Model
    
    public $TABLE_NAME = "Base";

    /**********************************************************************
        In this property you are expected to put all the columns you want
        other than the fields you want to be hashed.
    ***********************************************************************/

    public $MAINS = ["id"];
    
    /**********************************************************************
        In this field you are expected to put all columns you want to be
        encrypted or hashed.
    ***********************************************************************/
    
    public $HIDDEN = ["id"];

    

    abstract function add($value_array);

    abstract function update($value_array);
    
    abstract function delete($value_array);
    
    function readAll() {
        $query = $this->searchRecord();
        $result = $query->fetch();
        return $result->fetchAll();
    }

    function readSingle($id) {
        $query = $this->searchRecord();
        $query->where($this->getEntity()->PRIMARY_KEY, $id);
        $result = $query->fetch();
        return $result->fetchAll();
    }

    function baseCreate($value_array){
        $query = $this->addRecord();
        $query->add($value_array);
        $query->insert();
    }

    function baseUpdate($id, $change) {

        $query = $this->updateRecord();

        foreach($change as $column => $value) {
            $query->set($column, $value);
        }

        $query->where($this->getEntity()->PRIMARY_KEY, $id);
        $query->update();

    }

    function baseDelete($id){
        $query = $this->deleteRecord();
        $query->where($this->getEntity()->PRIMARY_KEY, $id);
        $query->delete();
    }

}
?>