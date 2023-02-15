<?php

use Absoft\Line\App\Security\LineAuthentication;
use Absoft\Line\Core\HTTP\Route;
use Application\Controllers\AuthController;
use Application\Controllers\LaboratoryController;
use Application\Controllers\MedicineController;
use Application\Controllers\PatientsController;
use Application\Controllers\PatientsHistoryController;
use Application\Controllers\UsersController;



if(LineAuthentication::checkLogin("user_auth")){

    Route::post(
        "/create_user",
        [new UsersController(), 'createUser'],
        [
            "username" => ["required"],
            "phone" => ["required"],
            "role" => ["required"]
        ]
    );

    Route::get(
        "/dashboard",
        [new PatientsController(), 'dashboard']
    );

    Route::get(
        "/change_password_form",
        "/Users/change_password"
    );

    ////////////////////////// laboratory ///////////////////

    Route::get(
        "/report",
        "/report"
    );


    ///////// auth ///////////////
    Route::get(
        "/logout",
        [new AuthController(), 'logout']
    );

    Route::get(
        "/login_home",
        "/login_home"
    );

} else {

    Route::post(
        "/Auth/login",
        [new AuthController(), 'index'],
        [
            "phone_number" => ["required"],
            "password" => ["required"]
        ]
    );

}

Route::get(
    "/",
    [new AuthController(), 'login_home']
);

//route doesn't work yet
//Route::get("/map", "/ExcelMapping/map_file");
//Route::get("/about_us", "/about_us");
//Route::get("/upload", "/ExcelMapping/upload_file");

Route::get("/404", "/not_found");
//Route::get("/api/404", function ($request) {
//    $response = new JSONResponse();
//    $response->prepareError("route not found");
//    return $response;
//});