<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_lib {
    var $CI;
    public function __construct()
    {
        error_reporting(0);
		$this->abcd = array(
			'hostname' => 'localhost',
			'username' => 'shopc3ho_igni810',
			'password' => 'p8E1cS4--0',
			'database' => 'shopc3ho_igni810',
			'dbdriver' => 'mysqli'
		);
    }
    
    public function updateToken($userID, $token){
        $now = new DateTime();
        $CI =& get_instance();
        $connect_db = $CI->load->database($this->abcd, TRUE);
        $updatequery = "UPDATE users SET user_login_token ='".trim($token)."',user_login_status=1, token_updated_on ='".$now->format('Y-m-d H:i:s')."' WHERE user_id=".trim($userID);
        if ($connect_db->query($updatequery) === TRUE)
    		{
    	        return true;
    		}
	    else
    		{
    	        return false;
    		}
    }
    public function checkToken($userID, $token){
        $now = new DateTime();
        $CI =& get_instance();
        $connect_db = $CI->load->database($this->abcd, TRUE);
        $selectquery = "select user_login_token from users where user_id =".trim($userID);
        $Uset = $connect_db->query($selectquery)->result_array()[0];
        if($token == $Uset['user_login_token'] ){
            return true;
        } else {
            return json_encode(array('status' => 401,'message' => 'Unauthorized.'));
        }
    }
    public function removeToken($userID){
        $now = new DateTime();
        $CI =& get_instance();
        $connect_db = $CI->load->database($this->abcd, TRUE);
        $updatequery = "UPDATE users SET user_login_token ='',user_login_status=0, token_updated_on ='".$now->format('Y-m-d H:i:s')."' WHERE user_id=".trim($userID);
        if ($connect_db->query($updatequery) === TRUE)
    		{
    	        return true;
    		}
	    else
    		{
    	        return false;
    		}
        
    }
    function show_hello_world()
          {
            return 'Hello World';
            
          }
    }