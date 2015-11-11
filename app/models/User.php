<?php 
class User {
	
	private $conn;

	function __construct() {
		require_once dirname(__FILE__) . '/../includes/DbConnect.php';
		// openning db connection
		$db = new DbConnect();
		$this->conn = $db->connect();
	}


	/*--------------- login user function -----------------
	@return true if user is successfully logged in
	@return false if username/password combination is wrong
	*/
	public function login($username, $password){
		$stmt = $this->conn->prepare("SELECT password FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->bind_result($password_hash);
		$stmt->store_result();

		if ($stmt->num_rows > 0 ){
			 $stmt->fetch();
			 $stmt->close();
			 	//returns true if vertification went right
				if (password_verify($password, $password_hash)){
					return true;
				} else { return false; }
		} else {
			//user entered wrong username
			$stmt->close();
			return false;
		}
	}
	
	/* --------------------create new user-------------------
	@return corresponding code for specific error occured
	*/
	public function createUser($username, $password, $name, $lastname, $email) {
		if ($this->validateUsername($username)){
			if (!$this->isUserExistsByUsername($username)){
				if ($this->validatePassword($password)){
					if (filter_var($email, FILTER_VALIDATE_EMAIL)){
						if (!$this->isUserExistsByEmail($email)){
							if (strlen($name) > 2 && strlen($name) < 50 ){
								if (strlen($lastname) > 2 && strlen($lastname) < 50){
									$password_hash = password_hash($password, PASSWORD_DEFAULT, array('cost' => '10'));
									$access_token = $this->generateAccessToken();
									$stmt = $this->conn->prepare("INSERT INTO users (username, password, name, lastname, e_mail, access_token)
											  VALUES (?, ?, ?, ?, ?, ?)");
									$stmt->bind_param("ssssss", ucfirst(strtolower($username)), $password_hash, ucfirst(strtolower($name)),
									 		  ucfirst(strtolower($lastname)), $email, $access_token);
									$result = $stmt->execute();
									$stmt->close();
									if ($result) {
										return USER_CREATED_SUCCESSFULLY;
									} else { return USER_CREATE_FAILED; }
								} else { return USER_LASTNAME_WRONG_SIZE; }
							} else { return USER_NAME_WRONG_SIZE;}
						} else { return USER_EMAIL_EXISTS; }
					} else { return USER_EMAIL_WRONG_FORMAT; }
				} else { return USER_PASSWORD_LENGTH; }
			} else { return USER_USERNAME_EXISTS; }
		} else { return USER_USERNAME_WRONG_LENGTH; }
	}
	
	/*	validates age, comming date, leaving date, country, religion and access token.
		some restrictions.. arrival date must be 1 year before or 1 year after the submiting momment.
		31 536 000  =  1 year in seconds
	*/
	public function uploadProfileInfo($age, $date_arrival, $date_leaving, $country, $religion, $access_token){
		if (isset($age) && isset($date_arrival) && isset($date_leaving) && isset($country) && isset($religion) && isset($access_token)
			&& !empty($age) && !empty($date_arrival) && !empty($date_leaving) && !empty($country) && !empty($religion) && !empty($access_token)){
			if ($age > 11 && $age < 100 ){
				if  (abs((time() - strtotime($date_arrival))) < 31536000) {
					if ((strtotime($date_leaving) - strtotime($date_arrival)) >= 0 ) {
						require ('/../functions/CountryCheck.php');
						if (isCountry($country)){
							$stmt = $this->conn->prepare("UPDATE users
													  SET age = ?, date_arrival = ?, date_leaving = ?, country = ?, religion = ?
													  WHERE access_token = ?");
							$stmt->bind_param("isssss",$age, $date_arrival, $date_leaving, $country, ucfirst(strtolower($religion)), $access_token);
							if ($stmt->execute()){
								return USER_PROFILE_INFO_UPDATED_SUCCESS;
							} else { USER_PROFILE_INFO_UPDATE_FAIL; }
						} else { return USER_COUNTRY_NOT_VALID; }
					} else { return USER_DATE_ARRIVAL_BIGGER_THAN_LEAVING; }
				} else { return USER_DATE_ARRIVAL_NOT_ALLOWED; }
			} else { return USER_AGE_NOT_ALLOWED; }
		} else { return USER_INVALID_ARGUMENTS; }
	}

	// returns AccessToken by username
	public function getUserTokenByUsername($username){
		$stmt = $this->conn->prepare ("SELECT access_token FROM users
									   WHERE username = ?");
		$stmt->bind_param("s", $username);
		if ($stmt->execute()){
			$stmt->bind_result($access_token);
			$stmt->store_result();

			if ($stmt->num_rows > 0 ){
				$stmt->fetch();
				$stmt->close();
				return $access_token;
			} else { return 0;}

		} else { return NULL; }
	}

	public function getUserByUsername($username){
		$stmt = $this->conn->prepare ("SELECT username, name, lastname, access_token FROM users
									   WHERE username = ?");
		$stmt->bind_param("s", $username);
		if ($stmt->execute()){
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();

			return $user;
		} else { return NULL; }
	}

	// generate access_token
	public function generateAccessToken(){
		return md5(uniqid(rand(), true));
	}

	//chek if user exist in database
	public function isUserExists($username, $email){
		if ($this->isUserExistsByUsername($username)) {
			//echo message that user should try another username
			return true;
		} else if ($this->isUserExistsByEmail($email)){
			//echo message that user should try another email
			return true;
		}
		else return false;
	}

	//chek if user exist in database by username
	public function isUserExistsByUsername($username){
		$stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->store_result();
		$num_rows = $stmt->num_rows;
		$stmt->close();
		if ($num_rows > 0) {
			return true;
		} 
		else return false;
	}

	//chek if user exist in database by email
	public function isUserExistsByEmail($email){
		$stmt = $this->conn->prepare("SELECT id FROM users WHERE e_mail = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();
		$num_rows = $stmt->num_rows;
		$stmt->close();
		if ($num_rows > 0) {
			return true;
		} 	
		else return false;
	}



	/*------------VALIDATION FUNCTIONS------------------------*/

	

	// validate username length does not exceed 32 and is not too short
	public function validateUsername($username){
		if (strlen((string)$username) <= 32 && strlen((string)$username) > 3){
			return true;
		} else { return false; }
	}

	// validate password length 
	public function validatePassword($password){
		if (strlen((string)$password) <= 32 && strlen((string)$password) >= 6){
			return true;
		} else { return false; }
	}


}
 ?>