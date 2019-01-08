<?php

require APPPATH . '/libraries/ImplementJwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

class Login extends CI_Controller {
  public function __construct() {
        parent::__construct();
        error_reporting(0);
         $abcd = array(
			'hostname' => 'localhost',
			'username' => 'shopc3ho_igni810',
			'password' => 'p8E1cS4--0',
			'database' => 'shopc3ho_igni810',
			'dbdriver' => 'mysqli'
        ); 
        $this->connect_db = $this->load->database($abcd, true);
        $this->load->library('auth_lib');
        $this->objOfJwt = new ImplementJwt();
  }
  
  public function index()
  {
    // $this->load->view('2taps/test');
    echo 'Loding......';
     echo $this->auth_lib->checkToken('1005','6bab5f2114c124dc95c878e4142aec54');
        // echo $this->auth_lib->removeToken('1005');
  }
  
  public function getOTP(){
    $response = array();
    $response["error"] = false;
    $response["otp"] = array();
    if($_GET['phone_no']  == NULL){
        $_GET['phone_no']  = '';
    }
    if ($_GET['phone_no'] !== '') {
        $digits = 4;
        $response['otp'] = rand(pow(10, $digits-1), pow(10, $digits)-1);
        echo json_encode($response);
    }else{
        echo 'Request Cannot be processed';
    }
  }
  public function getUser(){
    $response = array();
    $response["error"] = false;
    $response["userinfo"] = array();
    if ($_GET['phone_no'] !== '' && $_GET['user_type'] !== '') {
        $response["userinfo"] = $this->checkUser($_GET['phone_no'], $_GET['user_type']);
        echo json_encode($response);
    }else{
        echo 'Request Cannot be processed';
    }
  }
  public function checkUser($phonecheck, $utype){
      $selectquery = "SELECT * FROM `users` WHERE users.user_mobile = '".$phonecheck."'";
      $result = $this->connect_db->query($selectquery)->result_array();
      // using JWT method

      if(count($result) > 0){
        foreach($result as $rkeys){
                $tokenData['phone_no'] = urldecode($_GET['phone_no']); 
                $tokenData['role'] = urldecode($_GET['user_type']);
                $tokenData['user_id'] = $rkeys['user_id'];
                $tokenData['timeStamp'] = Date('Y-m-d h:i:s');
                $jwtToken = $this->objOfJwt->GenerateToken($tokenData); 
                $this->res_user[] = array(
                    'user_tag'=>'existing',
                    'user_id'=>$rkeys['user_id'],
                    'user_name'=>$rkeys['user_name'],
                    'user_email'=>$rkeys['user_email'],
                    'user_gender'=>$rkeys['user_gender'],
                    'user_mobile'=>$rkeys['user_mobile'],
                    'user_img'=>$rkeys['user_img'],
                    'user_type'=>$rkeys['user_type'],
                    'user_status'=>$rkeys['user_status'],
                    'user_address'=>$rkeys['user_address'],
                    'user_pincode'=>$rkeys['user_pincode'],
                    'user_email_verified'=>$rkeys['user_email_verified'],
                    'user_phone_verified'=>$rkeys['user_phone_verified'],
                    'user_rating'=>$rkeys['user_rating'],
                    'user_rate_count'=>$rkeys['user_rate_count'],
                    'user_token' => $jwtToken
                    );
            }
            return $this->res_user;
      }else{
          $insertquery = "INSERT into users(`user_mobile`, `user_type`) VALUES('".$phonecheck."','".$utype."')";
          if ($this->connect_db->query($insertquery) === TRUE) {
            $nselectquery = "SELECT * FROM `users` WHERE users.user_mobile = '".$phonecheck."'";
            $nresult = $this->connect_db->query($nselectquery)->result_array();
            foreach($nresult as $nkeys){
                $tokenData['phone_no'] = urldecode($_GET['phone_no']); 
                $tokenData['role'] = urldecode($_GET['user_type']);
                $tokenData['user_id'] = $nkeys['user_id'];
                $tokenData['timeStamp'] = Date('Y-m-d h:i:s');
                $jwtToken = $this->objOfJwt->GenerateToken($tokenData); 
                $this->res_user[] = array(
                    'user_tag'=>'new',
                    'user_id'=>$nkeys['user_id'],
                    'user_mobile'=>$nkeys['user_mobile'],
                    'user_token' => $jwtToken
                    );     
            }
            return $this->res_user;;
          }else{
              echo 'Request Cannot be processed';
          }
      }
  }
  
}
?>