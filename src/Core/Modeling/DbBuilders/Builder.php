<?php


namespace Absoft\Line\Core\Modeling\DbBuilders;



use Absoft\Line\Core\DbConnection\Database;
use Absoft\Line\Core\DbConnection\QueryConstruction\SQL\Creation;
use Absoft\Line\Core\DbConnection\QueryConstruction\SQLite\Creation as SQLiteCreation;
use Absoft\Line\Core\DbConnection\QueryConstruction\SQLite\Drop as SQLiteDrop;
use Absoft\Line\Core\DbConnection\QueryConstruction\SQL\Drop;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Exceptions\ExecutionException;
use Absoft\Line\Core\FaultHandling\Errors\ReferenceNotFound;

abstract class Builder {

    public $ATTRIBUTES;
    public $HIDDEN_ATTRIBUTES;
    public $TABLE_NAME;
    public $PRIMARY_KEY = "id";
    public $DATABASE = "MySql";
    public $DATABASE_NAME = "first";


    public function __construct(){

        $schema = new Schema();
        $this->construct($schema);

    }

    abstract function construct(Schema $schema);

    /**
     * @return array|bool
     * @throws DBConnectionError
     * @throws ExecutionException
     */
    public function create(){

        $con = new Database($this->DATABASE, $this->DATABASE_NAME);
        $query = ($this->DATABASE == "SQLite") ? new SQLiteCreation($this->TABLE_NAME, array_merge($this->ATTRIBUTES, $this->HIDDEN_ATTRIBUTES)) : new Creation($this->TABLE_NAME, array_merge($this->ATTRIBUTES, $this->HIDDEN_ATTRIBUTES));
        return $con->execute($query);

    }

    /**
     * @return array|bool
     * @throws DBConnectionError|ExecutionException
     */
    public function drop(){

        $con = new Database($this->DATABASE, $this->DATABASE_NAME);
        $query = ($this->DATABASE == "SQLite") ? new SQLiteDrop($this->TABLE_NAME) : new Drop($this->TABLE_NAME);
        return $con->execute($query);

    }

    public function checkAttribute($attribute_name){

    }

    /**
     * @param $table_name
     * @return mixed
     * @throws ReferenceNotFound
     */
    public function getReference($table_name){

        foreach($this->ATTRIBUTES as $attribute){

            if($attribute->foreign == $table_name){
                return $attribute->name;
            }

        }

        throw new ReferenceNotFound($this->TABLE_NAME, $table_name);

    }

    public function getPrimaryAttribute(){
        return $this->PRIMARY_KEY;
    }

}