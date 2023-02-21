<?php
namespace Application\Controllers;

use Absoft\Line\App\Pager\Alert;
use Absoft\Line\App\Security\Auth;
use Absoft\Line\App\Security\AuthorizationManagement;
use Absoft\Line\App\Security\LineAuthentication;
use Absoft\Line\Core\FaultHandling\Errors\DBConnectionError;
use Absoft\Line\Core\FaultHandling\Errors\ExecutionException;
use Absoft\Line\Core\FaultHandling\Errors\ForbiddenAccess;
use Absoft\Line\Core\FaultHandling\Errors\OperationFailed;
use Absoft\Line\Core\FaultHandling\FaultHandler;
use Absoft\Line\Core\HTTP\JSONResponse;
use Absoft\Line\Core\HTTP\Request;
use Absoft\Line\Core\HTTP\Response;
use Absoft\Line\Core\HTTP\Route;
use Absoft\Line\Core\HTTP\ViewResponse;
use Absoft\Line\Core\Modeling\Controller;

class AuthController extends Controller {

    /**
     * @param $request
     * @return string
     * @throws DBConnectionError
     * @throws ExecutionException
     */
    public function index($request){

        if(!$this->validate()){
            Alert::sendErrorAlert($this->validationMessage());
            return $this->display("/Auth/login");
        }

        $auth = LineAuthentication::Authenticate("user_auth", [$request["phone_number"], $request["password"]]);

        if(empty($auth) || $auth == null) {
            Alert::sendErrorAlert("Incorrect Phone number or Password");
            Route::view("/");
        }

        LineAuthentication::grant($auth, "user_auth");
        Alert::sendSuccessAlert("sign in successful");
        Route::view("/");
        return new JSONResponse();

    }

    /**
     * @param $request
     * @return void
     */
    public function logout($request) {

        LineAuthentication::deni("user_auth");
        Alert::sendSuccessAlert("Signed Out successfully");
        Route::view("/");

    }

    function home($request){
        return $this->page_auth();
    }

    function login_home(){
        $user = LineAuthentication::user("user_auth");
        if(empty($user)){
            return $this->display("/Auth/login");
        }else{
            return $this->display("/login_home");
        }
    }

    /**
     * @return JSONResponse|ViewResponse
     */
    function page_auth(){

        $user = LineAuthentication::user("user_auth");
        if(empty($user)){
            return $this->display("/Auth/login");
        }

        if($user["User_Role"] == "doctor"){
            Route::view("/dashboard");
        }

        Route::view("/patients_list/today");
        return new JSONResponse();

    }

}
?>
