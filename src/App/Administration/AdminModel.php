<?php
namespace Absoft\Line\App\Administration;

use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\Modeling\Models\Model;

class AdminModel extends Model{

    /*
    public $MAINS = [
        "id" => "",
        "username" => "",
        "f_name" => ""
    ];

    */
    
    //As the name indicate this is the Table name of the Model
    
    public $TABLE_NAME = "Admin";

    /**********************************************************************
        In this property you are expected to put all the columns you want
        other than the fields you want to be hashed.
    ***********************************************************************/

    public $MAINS = [
        //@att_start
        "id" => "",
        "username" => "",
        "role" => "",
        "fullname" => "",
        "status" => "",
        "email" => ""
        //@att_end
    ];
    
    /**********************************************************************
        In this field you are expected to put all columns you want to be
        encrypted or hashed.
    ***********************************************************************/
    
    public $HIDDEN = [
        //@hide_start
        "password" => ""
        //@hide_end
    ];


    /**
     * @param $username
     * @return array|mixed
     */
    public function getUser($username){

        $result = $this->searchRecord([
            [
                "name" => "username",
                "value" => $username,
                "equ" => "=",
                "det" => "and"
            ]
        ]);

        if(sizeof($result)){
            return $result[0];
        }

        return [];

    }

    /**
     * @param $user
     * @param $password
     * @throws OperationFailed
     */
    public function changePassword($user, $password){

        if(!$this->updateRecord(
            [
                "password" => $password
            ],
            [
                [
                    "name" => "username",
                    "value" => $user,
                    "equ" => "=",
                    "det" => "and"
                ]
            ]
        )){
            throw new OperationFailed("system ran into problem. cannot change password.");
        }

    }

}
?>