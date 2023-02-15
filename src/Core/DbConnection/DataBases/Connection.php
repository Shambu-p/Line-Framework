<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 1/18/2020
 * Time: 10:58 PM
 */

namespace Absoft\Line\Core\DbConnection\DataBases;


use Absoft\Line\Core\DbConnection\QueryConstruction\Query;

interface Connection{

    function getConnection();

    function execute(Query $query);

    function executeUpdate(Query $query);

    function executeFetch(Query $query);

    function executeInReturn(Query $query);

    /**
     * @return bool
     */
    function beginTransaction();

    /**
     * @return bool
     */
    function commit();

    /**
     * @return bool
     */
    function rollback();

    /**
     * @return integer|string
     */
    function lastInsertId();

}
