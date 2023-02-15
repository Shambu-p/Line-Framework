<?php
namespace Absoft\Line\Core\DbConnection\QueryConstruction\SQL;

use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Absoft\Line\Core\Modeling\Models\ModelInterface;
use PDOStatement;

class WriteQuery implements Query {

    private $query;
    private $values = [];
    private $model;

    function __construct(ModelInterface $model, string $query_string){
        $this->model = $model;
        $this->query = $query_string;
    }

    /**
     * @return bool|PDOStatement
     * @throws DBConnectionError
     * @throws ExecutionException
     */
    function execute(){
        return $this->model->execute($this);
    }

    function getQuery() {
        return $this->query;
    }

    function getValues() {
        return $this->values;
    }
}