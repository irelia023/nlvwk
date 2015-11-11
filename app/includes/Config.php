<?php 
/**
 * Database configuration
 */
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_NAME', 'vienna');
 

// defines for models/User.php
define('USER_CREATED_SUCCESSFULLY', 0);
define('USER_USERNAME_WRONG_LENGTH', 1);
define('USER_USERNAME_EXISTS', 2);
define('USER_PASSWORD_LENGTH', 3);
define('USER_EMAIL_WRONG_FORMAT', 4);
define('USER_EMAIL_EXISTS', 5);
define('USER_NAME_WRONG_SIZE', 6);
define('USER_LASTNAME_WRONG_SIZE', 7);
define('USER_CREATE_FAILED', 8);

define('USER_PROFILE_INFO_UPDATED_SUCCESS', 0);
define('USER_PROFILE_INFO_UPDATE_FAIL', 1); //database erorr
define('USER_COUNTRY_NOT_VALID', 2);
define('USER_DATE_ARRIVAL_BIGGER_THAN_LEAVING', 3);
define('USER_DATE_ARRIVAL_NOT_ALLOWED', 4);
define('USER_AGE_NOT_ALLOWED', 5);
define('USER_INVALID_ARGUMENTS', 6);








// defines for models/Picture.php
define('PICTURE_UPLOAD_SUCCESSFULLY', 0);
define('PICTURE_WRONG_EXTENSION', 1);
define('PICTURE_FILE_EMPTY', 2);
define('PICTURE_UPLOAD_NO_AUTOHORITY', 3);
define('PICTURE_DATABASE_FAILITURE', 4);




 ?>