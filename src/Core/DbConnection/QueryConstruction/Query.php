<?php
/**
 * @author Abnet Kebede
 * @meta time: 10:04 am date: sunday april 4 2013 E.C.
 */

namespace Absoft\Line\Core\DbConnection\QueryConstruction;


interface Query {

    function getQuery();

    function getValues();

}