<?php
namespace Application\Controllers;

use Absoft\Line\App\Pager\Alert;
use Absoft\Line\App\Security\AuthorizationManagement;
use Absoft\Line\App\Security\LineAuthentication;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Absoft\Line\Core\FaultHandling\Errors\ForbiddenAccess;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\HTTP\JSONResponse;
use Absoft\Line\Core\HTTP\Route;
use Absoft\Line\Core\HTTP\ViewResponse;
use Absoft\Line\Core\Modeling\Controller;
use Application\Models\UsersModel;

class UsersController extends Controller{

    /**
     * @param $request
     * @throws OperationFailed
     */
    function view($request){

        if(!$this->validate()){
            Alert::sendErrorAlert($this->validationMessage());
            return $this->display("/home");
        }

        $auth = LineAuthentication::user("user_auth");
        if(empty($auth)){
            Alert::sendErrorAlert($this->validationMessage());
            return $this->display("/home");
        }

        if($auth["User_Role"] == "doctor"){
            $model = new UsersModel();
            $result = $model->findRecord($request["id"]);
        }else{
            $result = $auth;
        }

        if(empty($result)){
            Alert::sendErrorAlert("User not found");
        }

        return $this->display("/home");

    }

    function dashboard($request){
        return $this->display("/dashboard");
    }

    /**
     * @param $request
     * @throws DBConnectionError
     * @throws ExecutionException
     */
    function createUser($request) {

        if(!$this->validate()){
            Alert::sendErrorAlert($this->validationMessage());
            return $this->display("/home");
        }

        $model = new UsersModel();
        $model->createUser($request["username"], $request["phone"], "password", $request["role"]);
        Alert::sendSuccessAlert("User created successfully. <b>use phone number <i>".$request["phone"]."</i> and password '<i>password</i>' </b>");
        Route::view("/user_list");

        return $this->display("/User/create_message", );

    }

    /**
     * @param $request
     * @throws DBConnectionError
     * @throws ExecutionException
     * @throws OperationFailed
     */
    public function updateUser($request) {

        if(!$this->validate()){
            Alert::sendErrorAlert($this->validationMessage());
            Route::view("/change_user_form");
            return new JSONResponse();
        }

        $model = new UsersModel();
        $model->change($request["id"], $request["username"], $request["phone"]);
        Alert::sendSuccessAlert("user changed successfully");
        Route::view("/user_list");
        return new JSONResponse();

    }

    /**
     * @param $request
     * @return ViewResponse
     * @throws OperationFailed
     */
    public function editForm($request) {

        if(!$this->validate()){
            Alert::sendErrorAlert($this->validationMessage());
            Route::goRoute("/home");
        }

        $model = new UsersModel();
        return $this->display("/Users/user_edit", $model->findRecord($request["id"]));

    }

    /**
     * @param $request
     * @throws OperationFailed
     * @throws DBConnectionError
     * @throws ExecutionException
     */
    public function changePassword($request){

        if(!$this->validate()){
            Alert::sendErrorAlert($this->validationMessage());
            Route::view("/change_password_form");
        }

        $auth = LineAuthentication::user("user_auth");

        $model = new UsersModel();
        $user = $model->findRecord($auth["idUser"]);

        if($request["confirm_password"] != $request["new_password"]) {
            Alert::sendErrorAlert("Password confirmation doesn't match!");
            Route::view("/change_password_form");
        }

        if(!password_verify($request["old_password"], $user["password"]) ) {
            Alert::sendErrorAlert("Current Password doesn't match!");
            Route::view("/change_password_form");
        }

        $model->changePassword($auth["idUser"], $request["new_password"]);
        Alert::sendSuccessAlert("Password Changed. <b>use the new password on next login!</b>");
        Route::view("/change_password_form", $user);

    }

    /**
     * @param $request
     * @throws DBConnectionError
     * @throws ExecutionException
     */
    function getUsers($request){

        if(!$this->validate()){
            Alert::sendErrorAlert($this->validationMessage());
            Route::view("/home");
            return new JSONResponse();
        }

        $model = new UsersModel();
        return $this->display("/Users/users_list", $model->allUsers($request["User_Role"] ?? ""));

    }

    function delete($request){
        if(!$this->validate()) {
            Alert::sendErrorAlert($this->validationMessage());
            Route::view("/user_list");
        }

        $model = new UsersModel();
        $model->deleteUser($request["id"]);
        Alert::sendInfoAlert("User has been deleted");
        Route::view("/user_list");
        return new JSONResponse();
    }

}
?>