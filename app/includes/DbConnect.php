<?php 

//Handling DB connection

class DbConnect {
	private $conn; 
	function __construct(){
	}
	
	/**
     * Establishing database connection
     * @return database connection handler
     */
    function connect() {
    	include_once dirname(__FILE__) . './Config.php';    

    	// Connecting to mysql database
        $this->conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Check for database connection error
        if (mysqli_connect_errno()){
        	echo 'Failed to connect to MySQL: ' . mysqli_connect.error();
        }

        //return connection resource

        return $this->conn;
	}
}

 ?>