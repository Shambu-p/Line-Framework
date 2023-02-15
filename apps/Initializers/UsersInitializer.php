<?php
namespace Application\Initializers;

use Absoft\Line\Core\Modeling\Initializer;

class UsersInitializer extends Initializer {

    /*
    public $VALUES = [
        [
            "id" => "the_id",
            "name" => "the_name",
        ],
        [
            "id" => "the_id",
            "name" => "the_name"
        ]
    ];

    */
    
    public $BUILDER = "Users";

    /*************************************************************************
        In this property you are expected to put all the values you want
        to insert into database. the you can initialize the operation from
        line cli.
    *************************************************************************/

    public $VALUES = [
        [
            "User_Name" => "Shambel",
            "User_PhoneNumber" => "0987654322",
            "password" => "password",
            "User_Role" => "doctor"
        ]
    ];
    
}
?>