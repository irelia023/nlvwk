<?php


class Response {

	public static function echoResponse($status_code, $response){
		$app = \Slim\Slim::getInstance();
		$app->status($status_code);
		$app->contentType('application/json');

		echo json_encode($response);
	}	
}

?>