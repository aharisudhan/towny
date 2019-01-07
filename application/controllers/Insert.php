<?php

require APPPATH . '/libraries/ImplementJwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

class Insert extends CI_Controller {

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
        $this->uploadnow = '../ci/src/images/';
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
// 		$this->load->view('2taps/test');
        echo 'Loding......';
        echo $subfolder_path = $this->base_directory . $this->products_directory . $udefined_shopid . '/';
    }

//  Parameters format : ads_id,quantity,fstatus,rstatus
//  Must pass Parameter:  Ads id
//  Optional Parameters :  Quantity,Favourite status,Remove status
//	Favourite status :  0 / 1
//                Remove status  0 / 1
    public function addToCart() {
        if (strlen($_GET['ads_id']) > 0) {
            if ($_GET['quantity'] === '') {
                $_GET['quantity'] = 1;
            }
            $insertquery = "INSERT INTO `mycart`( `user_id`, `ads_id`, `quantity`, `fav_status`,`remove_status`) VALUES ('" . $this->jwt_user_id . "','" . urldecode($_GET['ads_id']) . "','" . urldecode($_GET['quantity']) . "','" . urldecode($_GET['fstatus']) . "','" . urldecode($_GET['rstatus']) . "')";
            if ($this->connect_db->query($insertquery) === TRUE) {
                $this->res_disaddcart = array(
                    'error' => false,
                    'status' => 'success',
                    "statusCode" => $this->status_code_200
                );
            } else {
                $this->res_disaddcart = array(
                    'error' => false,
                    'status' => 'failed',
                    "statusCode" => $this->status_code_200
                );
            }
            echo json_encode($this->res_disaddcart);
        } else {
            http_response_code('404');
            $response["statusCode"] = $this->status_code_404;
            $response['status'] = 'parameters are missing';
            echo json_encode($response);
        }
    }

//  Parameters format : offer_id, shop_id, category_id, ads_imgfiles, ads_des
//  Must pass Parameter: 
//          "ads_imgfiles","ads_name","shop_id","offer_id","category_id"
//  Optional Parameters :  
//          "ads_type","ads_desc","ads_discount_type","ads_original_price","ads_discount_price","show_date","exp_date"

    public function postAds() {
        if (strlen($_POST['category_id']) > 0 && strlen($_POST['shop_id']) > 0 && strlen($_POST['ads_name']) > 0) {
            if (strlen($_POST['offer_id']) > 0) {
                $offerid = $_POST['offer_id'];
            } else {
                $selectquery = "SELECT * FROM `keywords` WHERE keywords.key_type = 'offers_title' AND `keywords`.`key_status` = 1 AND `keywords`.`key_extra` = 'product_offers'";
                $Catresults = $this->connect_db->query($selectquery)->result_array() [0];
                $offerid = $Catresults['key_id'];
            }

            $update_imgpath = '';
            $udefined_shopid = urldecode(trim(strtolower($_POST['ads_name']))) . _ . urldecode(trim($_POST['shop_id']));
            $subfolder_path = $this->base_directory . $this->products_directory . $udefined_shopid . '/';
            $fullfolder_path = $this->uploadnow . $this->products_directory . $udefined_shopid . '/';
            $images_array = [];
            if (!is_dir($fullfolder_path)) {
                mkdir($fullfolder_path, 0777, TRUE);
            }


            for ($i = 0; $i < count($_FILES); $i++) {
                $sname = 'file' . $i;
                $filename = $_FILES[$sname]['name'];
                $extension = end(explode(".", $filename));
                $originalimg = "towny_" . $i . "." . $extension;
                $uploadfile = $fullfolder_path . '/' . $originalimg;
                if (move_uploaded_file($_FILES[$sname]["tmp_name"], $uploadfile)) {
                    $images_array[] = $originalimg;
                } else {
                    $errorUpload .= $_FILES[$sname]['name'] . ', ';
                }
            }

            if ($_POST['show_date'] === '') {
                $now = new DateTime();
                $ads_showfrom = $now->format('Y-m-d H:i:s');
            } else {
                $ads_showfrom = urldecode($_POST['show_date']);
            }

            if ($_POST['exp_date'] === '') {
                $adexpry_on = date('Y-m-d H:i:s', strtotime("+30 days"));
            } else {
                $adexpry_on = urldecode($_POST['exp_date']);
            }

            $insertquery = "INSERT INTO `post_ads`(`offer_id`, `category_id`, `shop_id`, `ads_name`, `ads_description`, `ads_img`, `user_id`,`ads_fpath`,`ads_type_id`,`ads_type_discount_id`,`ads_original_price`,`ads_discount_price`,`ads_show_from`,`ads_expires_on`,`product_name`) VALUES ('" . $offerid . "','" . urldecode($_POST['category_id']) . "','" . urldecode($_POST['shop_id']) . "','" . urldecode($_POST['ads_name']) . "','" . urldecode($_POST['ads_desc']) . "','" . json_encode($images_array) . "','" . $this->jwt_user_id . "','" . $subfolder_path . "','" . urldecode($_POST['ads_type']) . "','" . urldecode($_POST['ads_discount_type']) . "','" . urldecode($_POST['ads_original_price']) . "','" . urldecode($_POST['ads_discount_price']) . "','" . $ads_showfrom . "','" . $adexpry_on . "','" . urldecode($_POST['product_name']) . "')";
            if ($this->connect_db->query($insertquery) === TRUE) {
                $this->res_dispostads = array(
                    'error' => false,
                    'status' => 'success',
                    "statusCode" => $this->status_code_200
                );
            } else {
                $this->res_dispostads = array(
                    'error' => false,
                    'status' => 'failed',
                    "statusCode" => $this->status_code_200
                );
            }
            echo json_encode($this->res_dispostads);
        } else {
            http_response_code('404');
            $response["statusCode"] = $this->status_code_404;
            $response['status'] = 'parameters are missing';
            echo json_encode($response);
        }
    }

//continue here
    public function addOffers() {
        $update_imgpath = '';
        if ($_POST['offer_name'] !== '' && $_POST['offer_desc'] !== '') {
            $update_imgpath = '';
            $udefined_shopid = urldecode(trim(strtolower($_POST['ads_name']))) . _ . urldecode(trim($_POST['shop_id']));
            $subfolder_path = $this->base_directory . $this->offers_directory . $udefined_shopid . '/';
// 			$fullfolder_path = $this->path . $subfolder_path;
            $fullfolder_path = $this->uploadnow . $this->offers_directory . $udefined_shopid . '/';
            $images_array = '';
            if (!is_dir($fullfolder_path)) {
                mkdir($fullfolder_path, 0777, TRUE);
            }

            $originalimg = basename(strtolower($_FILES['offer_img']['name']));
            $uploadfile = $fullfolder_path . '/' . $originalimg;
            if (move_uploaded_file($_FILES["offer_img"]["tmp_name"], $uploadfile)) {
                $images_array = $originalimg;
            } else {
                $errorUpload .= $_FILES['files']['name'] . ', ';
            }

            $insertquery = "INSERT INTO `offers`( `user_id`, `offer_name`, `offer_img`, `description`, `extended_description`,`offer_fpath`,`offer_expires`,`shop_id`) VALUES ('" . urldecode($_POST['user_id']) . "','" . urldecode($_POST['offer_name']) . "','" . json_encode($images_array) . "','" . urldecode($_POST['offer_desc']) . "','" . urldecode($_POST['offer_edesc']) . "','" . $subfolder_path . "','" . urldecode($_POST['exp_date']) . "','" . urldecode($_POST['shop_id']) . "')";
            if ($this->connect_db->query($insertquery) === TRUE) {
                $this->res_disaddoffer = array(
                    'error' => false,
                    'status' => 'success',
                    "statusCode" => $this->status_code_200
                );
            } else {
                $this->res_disaddoffer = array(
                    'error' => false,
                    'status' => 'failed',
                    "statusCode" => $this->status_code_200
                );
            }

            echo json_encode($this->res_disaddoffer);
        } else {
            echo 'parameters are missing';
        }
    }

    public function addCategories() {
        $update_imgpath = '';
        if (strlen($_POST['category_name'] > 0) && strlen($_POST['category_img'] > 0) && strlen($_POST['category_desc'] > 0) && strlen($_POST['user_id'] > 0)) {
            $uploadfile = $this->path . $this->uploaddir . $this->categories_directory . basename(strtolower($_FILES['category_img']['name']));
            if (move_uploaded_file($_FILES['category_img']['tmp_name'], $uploadfile)) {
                $update_imgpath = $this->base_directory . $this->categories_directory . strtolower($_FILES['category_img']['name']);
            } else {
                $this->res_disaddcategory = array(
                    'status' => 'failed'
                );
            }

            $insertquery = "INSERT INTO `categories`(`user_id`, `category_name`, `category_img`, `category_description`, `category_extended_description`)VALUES ('" . urldecode($_POST['user_id']) . "','" . urldecode($_POST['category_name']) . "','" . $update_imgpath . "','" . urldecode($_POST['category_desc']) . "','" . urldecode($_POST['category_edesc']) . "')";
            if ($this->connect_db->query($insertquery) === TRUE) {
                $this->res_disaddcategory = array(
                    'status' => 'success'
                );
            } else {
                $this->res_disaddcategory = array(
                    'status' => 'failed'
                );
            }

            echo json_encode($this->res_disaddcategory);
        } else {
            echo 'parameters are missing';
        }
    }

    public function addShops() {
        if (strlen($_POST['shop_name']) > 0 && strlen($_POST['owner_name']) > 0 && strlen($_POST['shop_pincode']) > 0 && strlen($_POST['shop_category']) > 0 && strlen($_POST['shop_lati']) > 0 && strlen($_POST['shop_logi']) > 0 && strlen($_POST['shop_phone']) > 0 && strlen($_POST['shop_address']) > 0) {
            $update_imgpath = '';
            $udefined_shopid = urldecode(trim(strtolower($_POST['shop_name']))) . _ . $this->jwt_user_id;
            $subfolder_path = $this->base_directory . $this->shops_directory . $udefined_shopid . '/';
            $fullfolder_path = $this->uploadnow . $this->shops_directory . $udefined_shopid . '/';
            $images_array = [];
            if (!is_dir($fullfolder_path)) {
                mkdir($fullfolder_path, 0777, TRUE);
            }

            for ($i = 0; $i < count($_FILES); $i++) {
                $sname = 'file' . $i;
                $filename = $_FILES[$sname]['name'];
                $extension = end(explode(".", $filename));
                $originalimg = "towny_" . $i . "." . $extension;
                $uploadfile = $fullfolder_path . '/' . $originalimg;
                if (move_uploaded_file($_FILES[$sname]["tmp_name"], $uploadfile)) {
                    $images_array[] = $originalimg;
                } else {
                    $errorUpload .= $_FILES[$sname]['name'] . ', ';
                }
            }

            $insertquery = "INSERT INTO `shops`( `shop_name`, `shop_img`, `marchant_id`, `shop_description`, `shop_latitude`, `shop_longitude`, `shop_phone`, `shop_email`, `shop_address`,`shop_fpath`,`shop_category`,`shop_pincode`,`shop_owner_name`,`shop_whatsapp`) VALUES ('" . urldecode($_POST['shop_name']) . "','" . json_encode($images_array) . "','" . $this->jwt_user_id . "','" . urldecode($_POST['shop_desc']) . "','" . urldecode($_POST['shop_lati']) . "','" . urldecode($_POST['shop_logi']) . "','" . urldecode($_POST['shop_phone']) . "','" . urldecode($_POST['shop_email']) . "','" . urldecode($_POST['shop_address']) . "','" . $subfolder_path . "','" . urldecode($_POST['shop_category']) . "','" . urldecode($_POST['shop_pincode']) . "','" . urldecode($_POST['owner_name']) . "','" . urldecode($_POST['shop_whatsapp']) . "')";

            if ($this->connect_db->query($insertquery) === TRUE) {
                $this->res_disaddshops = array(
                    'error' => false,
                    'status' => 'success',
                    "statusCode" => $this->status_code_200
                );
            } else {
                $this->res_disaddshops = array(
                    'error' => false,
                    'status' => 'failed',
                    "statusCode" => $this->status_code_200
                );
            }
        } else {
            $this->res_disaddshops = array(
                'error' => false,
                'status' => 'parameters are missing',
                "statusCode" => $this->status_code_200
            );
        }
        echo json_encode($this->res_disaddshops);
    }

}
