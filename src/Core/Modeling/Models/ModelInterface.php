<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 2/28/2021
 * Time: 12:49 PM
 */

namespace Absoft\Line\Core\Modeling\Models;


use Absoft\Line\Core\DbConnection\QueryConstruction\Query;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use PDOStatement;

interface ModelInterface {

    /**
     * @param $key
     * @return array
     */
    public function findRecord($key);

    /**
     * @return mixed
     */
    function getEntity();

    public function searchRecord();

    public function deleteRecord();

    public function updateRecord();

    public function addRecord();

    public function query(string $query_string);

    /**
     * @param array $search_array
     * @param array $limit
     * @return mixed
     *
     * [
            "name" => "",
            "email" => "dma"
            ":post" => [
                ":on" => "poster",
                "text" => "",
                "date" => ""
            ]
       ]
     */
    public function advancedSearch(Array $search_array, $limit = []);

    /**
     * @param Query $query
     * @return bool|PDOStatement
     * @throws DBConnectionError
     * @throws ExecutionException
     */
    public function execute(Query $query);

}
