<?php 



class Picture {

	private $conn;

	function __construct() {
		require_once dirname(__FILE__) . '/../includes/DbConnect.php';
		// openning db connection
		$db = new DbConnect();
		$this->conn = $db->connect();
	}

	function uploadPicture ($user_id, $picture_url) {
		// authenticate that right user has persmision to upload a photo..
		// check if picture exists
		$stmt = $this->conn->prepare("INSERT INTO pictures (user_id, url) VALUES (?, ?)");
		$stmt->bind_param("ss", $user_id, $picture_url);
		if ($result = $stmt->execute()){
			$stmt->close();
			if ($result) {
				return true;
			}
		} else { return false; }
	}



	/*
		calculate md5 hash of picture, remove last 4 characters of that hash and put current unix timestamp last 4 digits.
		create a file system structure such as "XX/YYY/hash+timestamp.jpg" check if XX exists ,then checks if YYY exists inside XX if not
		creates new directory.
	*/
	function savePicture($username, $access_token){
			if (isset($username) && !empty($username)){	
				$stmt = $this->conn->prepare("SELECT access_token, id FROM users WHERE username = ?");
				$stmt->bind_param("s", $username);
				if ($stmt->execute()){	
					$user = $stmt->get_result()->fetch_assoc();
					$stmt->close();
					if ($access_token == $user['access_token']){
						$allowed = array ('image/png', 'image/jpg', 'image/jpeg');

						$tmp_name = $_FILES['picture']['tmp_name'];
						$name = md5_file($tmp_name);
						$size = $_FILES['picture']['size'];
						$type = $_FILES['picture']['type'];

						/* validate it is jpeg/jpg/png */
						if (isset($name) && !empty($name)){
							$firstTwoLetters = substr($name, 0, 2);
							$ThreeToSixLetters = substr($name, 3, 3);
							$unixTimeStamp = time();
							$randomTime = substr((string)$unixTimeStamp, -4);
							$name = substr($name, 0, -4);
							$name = $name.$randomTime;
							$url = 'uploads/'.$firstTwoLetters.'/'.$ThreeToSixLetters.'/'.$name.'.jpg';	

							if (in_array($type, $allowed) && $size < 5120000){
								if (file_exists('uploads/'.$firstTwoLetters)){
									if (file_exists('uploads/'.$firstTwoLetters.'/'.$ThreeToSixLetters)){
										move_uploaded_file($tmp_name, 'uploads/'.$firstTwoLetters.'/'.$ThreeToSixLetters.'/'.$name.'.jpg');
									} else {
											mkdir('uploads/'.$firstTwoLetters.'/'.$ThreeToSixLetters);
											move_uploaded_file($tmp_name, 'uploads/'.$firstTwoLetters.'/'.$ThreeToSixLetters.'/'.$name.'.jpg');
										}
									} else {
											mkdir('uploads/'.$firstTwoLetters.'/'.$ThreeToSixLetters, 077, true);
											move_uploaded_file($tmp_name, 'uploads/'.$firstTwoLetters.'/'.$ThreeToSixLetters.'/'.$name.'.jpg');	
										}
								$this->uploadPicture($user['id'], $url);
								return PICTURE_UPLOAD_SUCCESSFULLY;
							} else { return PICTURE_WRONG_EXTENSION; }
						} else { return PICTURE_FILE_EMPTY; }
					} else { return PICTURE_UPLOAD_NO_AUTOHORITY; }
				} else { return PICTURE_DATABASE_FAILITURE; }
			} else { return PICTURE_UPLOAD_NO_AUTOHORITY; }

		
	}
}

 ?>