
<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

class Delete extends CI_Controller {

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
        $this->base_directory = "/src/images/";
        $this->products_directory = "products/";
        $this->promo_directory = "promo/";
        $this->offers_directory = "offers/";
        $this->categories_directory = "categories/";
        $this->shops_directory = "shops/";
        $this->path = getcwd();
        $this->uploaddir = '/src/images/';
        $this->objOfJwt = new ImplementJwt();
        $this->received_Token = $this->input->request_headers('Authorization');
        $this->status_404 = "No data found";
        $this->status_401 = "Unauthorized Access";
        $this->status_500 = "Internal Server Error";
        $this->status_200 = "Success";
        $this->status_code_404 = "404";
        $this->status_code_401 = "401";
        $this->status_code_500 = "500";
        $this->status_code_200 = "200";
        try {
            $this->jwtData = $this->objOfJwt->DecodeToken($this->received_Token['token']);
            $this->jwt_user_phone = $this->jwtData['phone_no'];
            $this->jwt_user_id = $this->jwtData['user_id'];
            $this->jwt_user_type = $this->jwtData['role'];
        } catch (Exception $e) {
            http_response_code('401');
            echo json_encode(array("error" => false, "status" => $this->status_401, "statusCode" => $this->status_code_401, "message" => $e->getMessage()));
            exit;
        }
    }

    public function index() {
        $this->load->view('2taps/testdelete');
        echo "Lodding................";
    }

//    input parameters : ads_id,ads_delete
//    changesAPI name as AdsDelete => adsDelete
    public function adsDelete() {
        if ($_POST['ads_id'] == NULL) {
            $_POST['ads_id'] = '';
        }
        if ($_POST['ads_delete'] == NULL) {
            $_POST['ads_delete'] = '';
        }
        if ($_POST['ads_id'] != '' && $_POST['ads_delete'] != '') {
            $deletequery = "UPDATE post_ads SET post_ads.ads_status =" . urldecode($_POST['ads_delete']);
            $now = new DateTime();
            $deletequery .= ", post_ads.ads_updated_on= '" . $now->format('Y-m-d H:i:s') . "'";
            $deletequery .= " WHERE post_ads.ads_id =" . urldecode($_POST['ads_id']);
            if ($this->connect_db->query($deletequery) === TRUE) {
                $this->res_update_adsdelete = array(
                    'error' => false,
                    'status' => 'success',
                    "statusCode" => $this->status_code_200
                );
            } else {
                $this->res_update_adsdelete = array(
                    'error' => false,
                    'status' => 'failed',
                    "statusCode" => $this->status_code_200
                );
            }

            echo json_encode($this->res_update_adsdelete);
        } else {
            http_response_code('404');
            $response["statusCode"] = $this->status_code_404;
            $response['status'] = 'parameters are missing';
            echo json_encode($response);
        }
    }

// input parameters : shop_id , shop_delete
// changesAPI name as ShopDelete => shopDelete
    public function shopDelete() {
        if ($_POST['shop_id'] == NULL) {
            $_POST['shop_id'] = '';
        }
        if ($_POST['shop_delete'] == NULL) {
            $_POST['shop_delete'] = '';
        }
        if ($_POST['shop_id'] !== '' && $_POST['shop_delete'] != '') {
            $deletequery = "UPDATE `shops` SET shops.shop_status =" . urldecode($_POST['shop_delete']);
            $now = new DateTime();
            $deletequery .= ", shops.`shop_updated_on`= '" . $now->format('Y-m-d H:i:s') . "'";
            $deletequery .= " WHERE shops.`shop_id` =" . urldecode($_POST['shop_id']);
            if ($this->connect_db->query($deletequery) === TRUE) {
                $this->res_update_shopdelete = array(
                    'error' => false,
                    'status' => 'success',
                    "statusCode" => $this->status_code_200
                );
            } else {
                $this->res_update_shopdelete = array(
                    'error' => false,
                    'status' => 'failed',
                    "statusCode" => $this->status_code_200
                );
            }

            echo json_encode($this->res_update_shopdelete);
        } else {
            http_response_code('404');
            $response["statusCode"] = $this->status_code_404;
            $response['status'] = 'parameters are missing';
            echo json_encode($response);
        }
    }

//  input parameters : user_id, user_delete
// changesAPI name as UserDelete => userDelete
    public function userDelete() {
        if ($_POST['user_id'] == NULL) {
            $_POST['user_id'] = '';
        }
        if ($_POST['user_delete'] == NULL) {
            $_POST['user_delete'] = '';
        }
        if ($_POST['user_id'] !== '' && $_POST['user_delete'] != '') {
            $deletequery = "UPDATE `users` SET users.user_status =" . urldecode($_POST['user_delete']);
            $now = new DateTime();
            $deletequery .= ", users.`last_updated_date`= '" . $now->format('Y-m-d H:i:s') . "'";
            $deletequery .= " WHERE users.`user_id` =" . urldecode($_POST['user_id']);
            if ($this->connect_db->query($deletequery) === TRUE) {
                $this->res_update_userdelete = array(
                    'error' => false,
                    'status' => 'success',
                    "statusCode" => $this->status_code_200
                );
            } else {
                $this->res_update_userdelete = array(
                    'error' => false,
                    'status' => 'failed',
                    "statusCode" => $this->status_code_200
                );
            }

            echo json_encode($this->res_update_userdelete);
        } else {
            http_response_code('404');
            $response["statusCode"] = $this->status_code_404;
            $response['status'] = 'parameters are missing';
            echo json_encode($response);
        }
    }

}
