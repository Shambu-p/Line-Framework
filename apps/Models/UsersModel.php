<?php
namespace Application\Models;

use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\Modeling\Models\Model;

class UsersModel extends Model {

    /*    public $MAINS = ["id", "username", "f_name"];    */
    
    //As the name indicate this is the Table name of the Model

    public string $TABLE_NAME = "Users";

    /**********************************************************************
        In this property you are expected to put all the columns you want
        other than the fields you want to be hashed.
    ***********************************************************************/

    public array $MAINS = ["idUser", "User_Name", "User_PhoneNumber", "User_Role"];
    
    /**********************************************************************
        In this field you are expected to put all columns you want to be
        encrypted or hashed.
    ***********************************************************************/

    public array $HIDDEN = ["password"];

    /**
     * @return bool|\PDOStatement
     * @throws DBConnectionError
     * @throws ExecutionException
     */
    function getUsers(){
        $query = $this->searchRecord();
        $query->filter(["idUser", "User_Name", "User_PoneNumber", "User_Role"]);
        return $query->fetch();
    }

    /**
     * @param $username
     * @param $phone_number
     * @param $password
     * @param $role
     * @return array
     * @throws DBConnectionError
     * @throws ExecutionException
     */
    function createUser($username, $phone_number, $password, $role){

        $user_data = [
            "User_Name" => $username,
            "User_PhoneNumber" => $phone_number,
            "User_Role" => $role,
            "password" => $password
        ];

        $query = $this->addRecord();
        $query->add($user_data);
        $query->insert();

        $user_data["idUser"] = $this->lastInsertId();

        return $user_data;

    }

    /**
     * @param $id
     * @param $password
     * @return array|mixed
     * @throws DBConnectionError
     * @throws ExecutionException
     * @throws OperationFailed
     */
    function changePassword($id, $password) {

        $result = $query = $this->findRecord($id);

        if(empty($result)) {
            throw new OperationFailed("User not found");
        }

        $query = $this->updateRecord();
        $query->set("password", $password);
        $query->where("idUser", $id);
        $query->update();

        $result["password"] = "";
        return $result;

    }

    /**
     * @param $id
     * @param $role
     * @return array|mixed
     * @throws DBConnectionError
     * @throws ExecutionException
     * @throws OperationFailed
     */
    function changePrivilege($id, $role){

        $result = $this->findRecord($id);

        if(empty($result)){
            throw new OperationFailed("User not found");
        }

        $query = $this->updateRecord();
        $query->set("User_Role", $role);
        $query->where("idUser", $id);
        $query->update();

        $result["password"] = "";
        $result["User_Role"] = $role;
        return $result;

    }

    /**
     * @param $id
     * @param $username
     * @param $phone_number
     * @return array|mixed
     * @throws DBConnectionError
     * @throws ExecutionException
     * @throws OperationFailed
     */
    function change($id, $username, $phone_number){

        $result = $this->findRecord($id);

        if(empty($result)){
            throw new OperationFailed("user not found!");
        }

        $query = $this->updateRecord();

        $query->set("User_Name", $username);
        $query->set("User_PhoneNumber", $phone_number);

        $query->where("idUser", $id);
        $query->update();

        unset($result["password"]);

        $result["User_Name"] = $username;
        $result["User_PhoneNumber"] = $phone_number;
        return $result;

    }

    /**
     * @return array
     * @throws DBConnectionError
     * @throws ExecutionException
     */
    function allUsers($role = ""){

        $query = $this->searchRecord();
        $query->filter(["idUser", "User_Name", "User_PhoneNumber", "User_Role"]);
        $query->like("User_Role", $role);
        $result = $query->fetch();

        return $result->fetchAll();

    }

    function deleteUser($id){
        $query = $this->deleteRecord();
        $query->where("idUser", $id);
        $query->delete();
    }

}
?>