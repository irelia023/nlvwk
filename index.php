<?php
require ('app/vendor/autoload.php');
require ('app/includes/DbConnect.php');
require ('app/models/User.php');
require ('app/models/Picture.php');
include ('app/functions/Response.php');



$app = new \Slim\Slim();

$app->post('/register', function () use ($app) {
    
	$response = array();

	$username = trim($app->request->post('username'));
	$password = trim($app->request->post('password'));
	$name = trim($app->request->post('name'));
	$lastname = trim($app->request->post('lastname'));
	$email = trim($app->request->post('email'));

	$user = new User();
	$res = $user->createUser($username, $password, $name, $lastname, $email);

	if ($res == USER_CREATED_SUCCESSFULLY) {
                $response["errorCode"] = 0;
                $response["message"] = "You are successfully registered";
                $response["access_token"] = $user->getUserTokenByUsername($username);
                Response::echoResponse(201, $response);
            } else if ($res == USER_USERNAME_WRONG_LENGTH) {
                $response["errorCode"] = 1;
                $response["message"] = "Oops! Username length either too short or too long";
                Response::echoResponse(200, $response);
            } else if ($res == USER_USERNAME_EXISTS) {
                $response["errorCode"] = 2;
                $response["message"] = "Sorry, this username already exists";
                Response::echoResponse(200, $response);
            } else if ($res == USER_PASSWORD_LENGTH) {
                $response["errorCode"] = 3;
                $response["message"] = "Oops! Password length either too short or too long";
                Response::echoResponse(200, $response);
            } else if ($res == USER_EMAIL_WRONG_FORMAT) {
                $response["errorCode"] = 4; 
                $response["message"] = "Oops! Email format is not correct";
                Response::echoResponse(200, $response);
            } else if ($res == USER_EMAIL_EXISTS) {
                $response["errorCode"] = 5;
                $response["message"] = "Sorry, this email already exists";
                Response::echoResponse(200, $response);
            } else if ($res == USER_NAME_WRONG_SIZE) {
                $response["errorCode"] = 6;
                $response["message"] = "Sorry, user's name wrong length";
                Response::echoResponse(200, $response);
            } else if ($res == USER_LASTNAME_WRONG_SIZE) {
                $response["errorCode"] = 7;
                $response["message"] = "User Lastname wrong length";
                Response::echoResponse(200, $response);
            } else if ($res == USER_CREATE_FAILED) {
                $response["errorCode"] = 8;
                $response["message"] = "Sorry, there was errorCode creating user";
                Response::echoResponse(200, $response);
            }

});
$app->post('/login', function() use ($app){
	$user = new User();

	$response = array();

	$username = $app->request->post('username');
	$password = $app->request->post('password');


	
	if ($user->login($username, $password)) {
		$user = $user->getUserByUsername($username);
		if ($user != NULL ){
			$response['errorCode'] = 0;
			$response['username'] = $user['username'];
			$response['name'] = $user['name'];
			$response['lastname'] = $user['lastname'];
			$response['access_token'] = $user['access_token'];
		} else {
			$response['errorCode'] = 1;
			$response['message'] = "Unknown errorCode occured...";
		} 
	} else { 
		$response['errorCode'] = 2;
		$response['message'] = "Bad username/password combination...";
	}
	Response::echoResponse(200, $response); 
});


$app->post('/uploadProfileInfo', function() use ($app){
	$user = new User();

	$response = array();

	$age = $app->request->post('age');
	$date_arrival = $app->request->post('date_arrival');
	$date_leaving = $app->request->post('date_leaving');
	$country = $app->request->post('country');
	$religion = $app->request->post('religion');
	$access_token = $app->request->post('access_token');
	$res = $user->uploadProfileInfo($age, $date_arrival, $date_leaving, $country, $religion, $access_token);

	if ($res == USER_PROFILE_INFO_UPDATED_SUCCESS){
		$response['errorCode'] = 0;
		$response['message'] = "Success";
		Response::echoResponse(201, $response);
	} else if ($res == USER_PROFILE_INFO_UPDATE_FAIL) {
		$response['errorCode'] = 1;
		$response['message'] = "Sorry, uknown database error occured.";
		Response::echoResponse(200, $response);
	}else if ($res == USER_COUNTRY_NOT_VALID) {
		$response['errorCode'] = 2;
		$response['message'] = "Sorry, please enter a valid country";
		Response::echoResponse(200, $response);
	}else if ($res == USER_DATE_ARRIVAL_BIGGER_THAN_LEAVING) {
		$response['errorCode'] = 3;
		$response['message'] = "Sorry, but you can't leave before you come.";
		Response::echoResponse(200, $response);
	}else if ($res == USER_DATE_ARRIVAL_NOT_ALLOWED) {
		$response['errorCode'] = 4;
		$response['message'] = "Sorry, your arrival date is far away from today...";
		Response::echoResponse(200, $response);
	}else if ($res == USER_AGE_NOT_ALLOWED) {
		$response['errorCode'] = 5;
		$response['message'] = "Sorry, you're too young or too long.";
		Response::echoResponse(200, $response);
	} else  {
		$response['errorCode'] = 6;	
		$response['message'] = "Sorry, bad arguments";
		Response::echoResponse(200, $response);
	}

});

$app->post('/uploadPicture', function() use ($app){

	$picture = new Picture();

	$response = array();
	$username = $app->request->post('username');
	$access_token = $app->request->post('access_token');
	$picture_url = $picture->savePicture($username, $access_token);

	if ($picture_url == 0) {	
			$response['errorCode'] = 0;
			$response['message'] = "Picture uploaded to server!";
			Response::echoResponse(201, $response);
		} else if ($picture_url == 1){
			$response['errorCode'] = 1;
			$response['message'] = "Error,Wrong extension. :(";
			Response::echoResponse(200, $response);
		} else if ($picture_url == 2){
			$response['errorCode'] = 2;
			$response['message'] = "Eror file is empty :(";
			Response::echoResponse(200, $response);
		} else if ($picture_url == 3){
			$response['errorCode'] = 3;
			$response['message'] = "Sorry, you are not allowed to make uploads :(";
			Response::echoResponse(200, $response);
		} else if ($picture_url == 4){
			$response['errorCode'] = 4;
			$response['message'] = "Eror in database occurred :(";
			Response::echoResponse(200, $response);
		}
});


$app->run();



 ?>