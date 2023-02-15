<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 2/28/2021
 * Time: 1:39 PM
 */

namespace Absoft\Line\Core\Modeling\DbBuilders;

interface DatabaseBuilder
{

    public function create();

    public function drop();

    public function getAttributes();

    public function getHiddenAttributes();

    public function checkAttribute();

}
