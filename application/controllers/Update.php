<?php

require APPPATH . '/libraries/ImplementJwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

class Update extends CI_Controller {

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
        $this->users_directory = "users/";
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
        $this->load->view('2taps/test1');
        echo "Lodding................";
    }

//  input parameters: user_name,user_email,user_address,user_pincode,user_phone
//    Must Pass: user_phone
//    Optional Pass : user_name,user_email,user_address,user_pincode
//    chages made in API Name as  ProfileUpdate => profileUpdate
    public function profileUpdate() {

        if ($_POST['user_name'] == NULL) {
            $_POST['user_name'] = '';
        }
        if ($_POST['user_email'] == NULL) {
            $_POST['user_email'] = '';
        }
        if ($_POST['user_address'] == NULL) {
            $_POST['user_address'] = '';
        }
        if ($_POST['user_pincode'] == NULL) {
            $_POST['user_pincode'] = '';
        }
        if ($_POST['user_phone'] == NULL) {
            $_POST['user_phone'] = '';
        }


        if (strlen($_POST['user_phone']) > 0) {
            $updatequery = "UPDATE users SET user_mobile=" . urldecode($_POST['user_phone']);
            $updatequery .= ", user_name='" . urldecode($_POST['user_name']) . "'";
            $updatequery .= ", user_email='" . urldecode($_POST['user_email']) . "'";
            $updatequery .= ", user_address='" . urldecode($_POST['user_address']) . "'";
            $updatequery .= ", user_pincode='" . urldecode($_POST['user_pincode']) . "'";
            $update_imgpath = '';
            $udefined_userid = $this->jwt_user_id . '_' . urldecode(trim($_POST['user_phone']));
            $subfolder_path = $this->base_directory . $this->users_directory . $udefined_userid . '/';
            $fullfolder_path = $this->uploadnow . $this->users_directory . $udefined_userid;
            $images_array = [];
            if (!is_dir($fullfolder_path)) {
                mkdir($fullfolder_path, 0777, TRUE);
            }
            if (count($_FILES) > 0) {
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
                $updatequery .= ",`user_img` = '" . json_encode($images_array) . "', ";
                $updatequery .= "`user_filepath` = '" . $subfolder_path . "' ";
            }

            $now = new DateTime();
            $updatequery .= ",`last_updated_date`= '" . $now->format('Y-m-d H:i:s') . "'";
            $updatequery .= " WHERE `user_id` =" . $this->jwt_user_id;
            if ($this->connect_db->query($updatequery) === TRUE) {
                $this->res_update_users = array(
                    'error' => false,
                    'status' => 'success',
                    "statusCode" => $this->status_code_200
                );
            } else {
                $this->res_update_users = array(
                    'error' => false,
                    'status' => 'failed',
                    "statusCode" => $this->status_code_200
                );
            }

            echo json_encode($this->res_update_users);
        } else {
            http_response_code('404');
            $response["statusCode"] = $this->status_code_404;
            $response['status'] = 'parameters are missing';
            echo json_encode($response);
        }
    }

//    Inputs from Android: Ads ID, Offer Id, Category ID, Shop ID, Ads name, Ads Description, Ads Image
//  Can Update: Offer Id, Category ID, Shop ID, Ads name, Ads Description, Ads Image
//  Parameters format : ads_id, offer_id, shop_id, category_id, ads_imgfiles, ads_des
//  Must pass Parameter:  Offer Id, Ads ID, Shop ID, Ads name
//    chages made in API Name as  AdsUpdate => adsUpdate

    public function adsUpdate() {
        if ($_POST['ads_desc'] == NULL) {
            $_POST['ads_desc'] = '';
        }
        if ($_POST['category_id'] == NULL) {
            $_POST['category_id'] = '';
        }
        if ($_POST['ads_type'] == NULL) {
            $_POST['ads_type'] = '';
        }
        if ($_POST['ads_discount_type'] == NULL) {
            $_POST['ads_discount_type'] = '';
        }
        if ($_POST['ads_original_price'] == NULL) {
            $_POST['ads_original_price'] = '';
        }
        if ($_POST['show_date'] == NULL) {
            $_POST['show_date'] = '';
        }
        if ($_POST['ads_discount_price'] == NULL) {
            $_POST['ads_discount_price'] = '';
        }
        if ($_POST['product_name'] == NULL) {
            $_POST['product_name'] = '';
        }
        if ($_POST['exp_date'] == NULL) {
            $_POST['exp_date'] = '';
        }
        if ($_POST['ads_id'] == NULL) {
            $_POST['ads_id'] = '';
        }
        if ($_POST['shop_id'] == NULL) {
            $_POST['shop_id'] = '';
        }
        if ($_POST['offer_id'] == NULL) {
            $_POST['offer_id'] = '';
        }
        if ($_POST['ads_name'] == NULL) {
            $_POST['ads_name'] = '';
        }

        if (strlen($_POST['ads_id']) > 0 && strlen($_POST['shop_id']) > 0 && strlen($_POST['offer_id']) > 0 && strlen($_POST['ads_name']) > 0) {
            $updatequery = "UPDATE `post_ads` SET `ads_name` = '" . urldecode($_POST['ads_name']) . "', `user_id` = '" . $this->jwt_user_id . "', `shop_id` = '" . urldecode($_POST['shop_id']) . "', `offer_id` = '" . urldecode($_POST['offer_id']) . "', ";
            $updatequery .= "`ads_description` = '" . urldecode($_POST['ads_desc']) . "', ";
            $updatequery .= "`category_id` = '" . urldecode($_POST['category_id']) . "', ";
            $updatequery .= "`ads_type_id` = '" . urldecode($_POST['ads_type']) . "', ";
            $updatequery .= "`ads_type_discount_id` = '" . urldecode($_POST['ads_discount_type']) . "', ";
            $updatequery .= "`ads_original_price` = '" . urldecode($_POST['ads_original_price']) . "', ";
            $updatequery .= "`ads_discount_price` = '" . urldecode($_POST['ads_discount_price']) . "', ";
            $updatequery .= "`ads_show_from` = '" . urldecode($_POST['show_date']) . "', ";
            $updatequery .= "`product_name` = '" . urldecode($_POST['product_name']) . "', ";
            $updatequery .= "`ads_expires_on` = '" . urldecode($_POST['exp_date']) . "', ";
            $update_imgpath = '';
            $udefined_shopid = urldecode(trim(strtolower($_POST['ads_name']))) . '_' . urldecode(trim($_POST['shop_id']));
            $subfolder_path = $this->base_directory . $this->products_directory . $udefined_shopid . '/';
            $fullfolder_path = $this->uploadnow . $this->products_directory . $udefined_shopid . '/';
            $images_array = [];
            if (!is_dir($fullfolder_path)) {
                mkdir($fullfolder_path, 0777, TRUE);
            }

            if (count($_FILES) > 0) {
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

                $updatequery .= "`ads_img` = '" . json_encode($images_array) . "', ";
                $updatequery .= "`ads_fpath` = '" . $subfolder_path . "', ";
            }

            $now = new DateTime();
            $updatequery .= "`ads_updated_on`= '" . $now->format('Y-m-d H:i:s') . "'";
            $updatequery .= " WHERE `ads_id` =" . urldecode($_POST['ads_id']);
            if ($this->connect_db->query($updatequery) === TRUE) {
                $this->res_update_ads = array(
                    'error' => false,
                    'status' => 'success',
                    "statusCode" => $this->status_code_200
                );
            } else {
                $this->res_update_ads = array(
                    'error' => false,
                    'status' => 'failed',
                    "statusCode" => $this->status_code_200
                );
            }

            echo json_encode($this->res_update_ads);
        } else {
            http_response_code('404');
            $response["statusCode"] = $this->status_code_404;
            $response['status'] = 'parameters are missing';
            echo json_encode($response);
        }
    }

//    Inputs from Android: Ads ID, Updatedetails
//  Can Update: Likes / Share / View
//  Parameter format: ads_id, ads_update_now 
//  Sample Update Parameter,		
//  likes  (i,e) ads_update_now = likes
//  shares (i,e) ads_update_now = shares
//  views (i,e) ads_update_now = views
//    chages made in API Name as  AdsUpdateNow => adsUpdateNow

    public function adsUpdateNow() {
        if ($_POST['ads_id'] == NULL) {
            $_POST['ads_id'] = '';
        }
        if ($_POST['ads_update_now'] == NULL) {
            $_POST['ads_update_now'] = '';
        }
        if ($_POST['ads_id'] !== '' && $_POST['ads_update_now'] != '') {
            $selectquery = "select ads_likes,ads_shares,ads_views from post_ads where ads_id =" . urldecode($_POST['ads_id']);
            $Catresults = $this->connect_db->query($selectquery)->result_array() [0];
            $ads_likes = $Catresults['ads_likes'];
            $ads_shares = $Catresults['ads_shares'];
            $ads_views = $Catresults['ads_views'];
            $updatequery = "UPDATE `post_ads` SET ";
            switch (urldecode($_POST['ads_update_now'])) {
                case 'likes':
                    $nupdatevalue = $ads_likes + 1;
                    $updatequery .= '`ads_likes`=' . $nupdatevalue;
                    break;

                case 'shares':
                    $nupdatevalue = $ads_shares + 1;
                    $updatequery .= '`ads_shares`=' . $nupdatevalue;
                    break;

                case 'views':
                    $nupdatevalue = $ads_views + 1;
                    $updatequery .= '`ads_views`=' . $nupdatevalue;
                    break;

                default:
                    break;
            }

            $now = new DateTime();
            $updatequery .= ", `ads_updated_on`= '" . $now->format('Y-m-d H:i:s') . "'";
            $updatequery .= " WHERE `ads_id` =" . urldecode($_POST['ads_id']);
            if ($this->connect_db->query($updatequery) === TRUE) {
                $this->res_update_ads = array(
                    'error' => false,
                    'status' => 'success',
                    "statusCode" => $this->status_code_200
                );
            } else {
                $this->res_update_ads = array(
                    'error' => false,
                    'status' => 'failed',
                    "statusCode" => $this->status_code_200
                );
            }

            echo json_encode($this->res_update_ads);
        } else {
            http_response_code('404');
            $response["statusCode"] = $this->status_code_404;
            $response['status'] = 'parameters are missing';
            echo json_encode($response);
        }
    }

//  Inputs from Android:  shop Id, Shop Name, Owner Name, Shop Pincode, Shop Category,Shop Lati, Shop Longi,Shop Phone,Shop Address, Shop Images, Shop Email,Shop Whatsapp,Shop Desc
//  Parameters Format: 'shop_id','shop_name','owner_name','shop_pincode','shop_category','shop_lati','shop_logi','shop_phone','shop_address','shop_imgfiles','shop_email','shop_whatsapp','shop_desc'
//  Must Pass :shop Id,  Shop Name, Owner Name, Shop Pincode, Shop Category,Shop Lati, Shop Longi,Shop Phone,Shop Address, Shop Images
//  Optional Pass:  Shop Email,Shop Whatsapp,Shop Desc
//    chages made in API Name as  ShopsUpdate => shopsUpdate
    public function shopsUpdate() {

        if ($_POST['shop_email'] == NULL) {
            $_POST['shop_email'] = '';
        }
        if ($_POST['shop_whatsapp'] == NULL) {
            $_POST['shop_whatsapp'] = '';
        }
        if ($_POST['shop_desc'] == NULL) {
            $_POST['shop_desc'] = '';
        }

        if ($_POST['shop_phone'] == NULL) {
            $_POST['shop_phone'] = '';
        }
        if ($_POST['shop_category'] == NULL) {
            $_POST['shop_category'] = '';
        }
        if ($_POST['shop_lati'] == NULL) {
            $_POST['shop_lati'] = '';
        }
        if ($_POST['shop_logi'] == NULL) {
            $_POST['shop_logi'] = '';
        }
        if ($_POST['shop_pincode'] == NULL) {
            $_POST['shop_pincode'] = '';
        }
        if ($_POST['shop_address'] == NULL) {
            $_POST['shop_address'] = '';
        }
        if ($_POST['shop_category'] == NULL) {
            $_POST['shop_category'] = '';
        }
        if ($_POST['owner_name'] == NULL) {
            $_POST['owner_name'] = '';
        }
        if ($_POST['shop_name'] == NULL) {
            $_POST['shop_name'] = '';
        }
        if ($_POST['shop_id'] == NULL) {
            $_POST['shop_id'] = '';
        }
        if (strlen($_POST['shop_id']) > 0) {
            if (strlen($_POST['shop_name']) > 0 && strlen($_POST['owner_name']) > 0 && strlen($_POST['shop_pincode']) > 0 && strlen($_POST['shop_category']) > 0 && strlen($_POST['shop_lati']) > 0 && strlen($_POST['shop_logi']) > 0 && strlen($_POST['shop_phone']) > 0 && strlen($_POST['shop_address']) > 0) {
                $updatequery = "UPDATE shops SET shops.shop_name='" . urldecode($_POST['shop_name']) . "',shops.shop_owner_name='" . urldecode($_POST['owner_name']) . "',shops.shop_pincode='" . urldecode($_POST['shop_pincode']) . "', shops.shop_category='" . urldecode($_POST['shop_category']) . "', shops.shop_latitude='" . urldecode($_POST['shop_lati']) . "'
            , shops.shop_longitude= '" . urldecode($_POST['shop_logi']) . "', shops.shop_phone= '" . urldecode($_POST['shop_phone']) . "',shops.shop_address='" . urldecode($_POST['shop_address']) . "'";
                $updatequery .= ",shop_email = '" . urldecode($_POST['shop_email']) . "'";
                $updatequery .= ",shop_whatsapp = '" . urldecode($_POST['shop_whatsapp']) . "'";
                $updatequery .= ", shop_description ='" . urldecode($_POST['shop_desc']) . "'";
                $update_imgpath = '';
                $udefined_shopid = urldecode(trim(strtolower($_POST['shop_name']))) . '_' . urldecode(trim($_POST['shop_id']));
                $subfolder_path = $this->base_directory . $this->shops_directory . $udefined_shopid . '/';
                $fullfolder_path = $this->uploadnow . $this->shops_directory . $udefined_shopid . '/';
                $images_array = [];
                if (!is_dir($fullfolder_path)) {
                    mkdir($fullfolder_path, 0777, TRUE);
                }

                if (count($_FILES) > 0) {
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
                    $updatequery .= ",`shop_img` = '" . json_encode($images_array) . "', ";
                    $updatequery .= "`shop_fpath` = '" . $subfolder_path . "', ";
                }

                $now = new DateTime();
                $updatequery .= "`shop_updated_on`= '" . $now->format('Y-m-d H:i:s') . "'";
                $updatequery .= " WHERE `shop_id` =" . urldecode($_POST['shop_id']);
                if ($this->connect_db->query($updatequery) === TRUE) {
                    $this->res_update_shops = array(
                        'error' => false,
                        'status' => 'success',
                        "statusCode" => $this->status_code_200
                    );
                } else {
                    $this->res_update_shops = array(
                        'error' => false,
                        'status' => 'failed',
                        "statusCode" => $this->status_code_200
                    );
                }

                echo json_encode($this->res_update_shops);
            } else {
                http_response_code('404');
                $response["statusCode"] = $this->status_code_404;
                $response['status'] = 'parameters are missing';
                echo json_encode($response);
            }
        } else {
            http_response_code('404');
            $response["statusCode"] = $this->status_code_404;
            $response['status'] = 'parameters are missing';
            echo json_encode($response);
        }
    }

    // Rating and Reviews Update
    // parameters required,
    // common ==>  shop_id,rtype= rating / review / follow / unfollow
    // Rating ==> rating(in numbers)
    // Review ==> review_title,review_description
//    chages made in API Name as  ShopsRRUpdate => shopsRRUpdate
    public function shopsRRUpdate() {

        if ($_POST['shop_id'] == NULL) {
            $_POST['shop_id'] = '';
        }
        if ($_POST['rtype'] == NULL) {
            $_POST['rtype'] = '';
        }
        if (strlen($_POST['shop_id']) > 0) {
            $selectuser = "SELECT users.user_name FROM `users` WHERE  users.user_type = 0 AND users.user_id =" . $this->jwt_user_id;
            $selectshop = "SELECT shops.shop_rating,shops.shop_reviews_count,`shops`.`shop_followers_count`,shops.shop_reviews FROM shops WHERE shops.shop_id =" . urldecode($_POST['shop_id']);
            $Uresults = $this->connect_db->query($selectuser)->result_array() [0];
            if (!array_filter($Uresults) == []) {
                $user_name = $Uresults['user_name'];
                $Shopsresults = $this->connect_db->query($selectshop)->result_array() [0];
                $shop_rating = $Shopsresults['shop_rating'];
                $shop_reviews_count = $Shopsresults['shop_reviews_count'];
                $shop_reviews = $Shopsresults['shop_reviews'];
                $shop_followers_count = $Shopsresults['shop_followers_count'];
                $updatequery = "UPDATE shops SET ";
                $floowerid = urldecode($_POST['user_id']) . urldecode($_POST['shop_id']);
                $now = new DateTime();
                switch (urldecode($_POST['rtype'])) {
                    case 'rating':
                        $nrating = urldecode($_POST['rating']);
                        $calrating = $shop_rating + $nrating;
                        $finalRating = bcdiv($calrating, 2, 1);
                        $updatequery .= " shops.shop_rating = " . $finalRating;
                        break;

                    case 'follow':
                        $nfollwers = $shop_followers_count + 1;
                        $updatequery .= " shops.shop_followers_count = " . $nfollwers;
                        $selectexitsting = "SELECT * FROM `shop_follower` where fid =" . $floowerid;
                        $checkexits = $this->connect_db->query($selectexitsting)->result_array() [0];
                        if (!array_filter($checkexits) == []) {
                            $fquery = "UPDATE shop_follower SET shop_follower.fs_status = 0,shop_follower.updated_on='" . $now->format('Y-m-d H:i:s') . "' WHERE fid=" . $floowerid;
                        } else {
                            $fquery = "INSERT IGNORE into shop_follower(fid,user_id,shop_id) Values($floowerid," . urldecode($_POST['user_id']) . "," . urldecode($_POST['shop_id']) . ")";
                        }

                        $this->connect_db->query($fquery);
                        break;

                    case 'unfollow':
                        $nfollwers = $shop_followers_count - 1;
                        $updatequery .= " shops.shop_followers_count = " . $nfollwers;
                        $fquery = "UPDATE shop_follower SET shop_follower.fs_status = 1,shop_follower.updated_on='" . $now->format('Y-m-d H:i:s') . "' WHERE fid=" . $floowerid;
                        $this->connect_db->query($fquery);
                        break;

                    case 'review':

                        //  $nupdatevalue = $ads_shares+1;
                        // 		$updatequery .= '`ads_shares`='.$nupdatevalue;

                        break;

                    default:
                        break;
                }

                $updatequery .= ", `shop_rating_review_updated_on`= '" . $now->format('Y-m-d H:i:s') . "'";
                $updatequery .= " WHERE `shop_id` =" . urldecode($_POST['shop_id']);
                if ($this->connect_db->query($updatequery) === TRUE) {
                    $this->res_update_rrshops = array(
                        'error' => false,
                        'status' => 'success',
                        "statusCode" => $this->status_code_200
                    );
                } else {
                    $this->res_update_rrshops = array(
                        'error' => false,
                        'status' => 'failed',
                        "statusCode" => $this->status_code_200
                    );
                }

                echo json_encode($this->res_update_rrshops);
            } else {
                http_response_code('401');
                $response["statusCode"] = $this->status_code_401;
                $response['status'] = 'Unauthorized user';
                echo json_encode($response);
            }
        } else {
            http_response_code('404');
            $response["statusCode"] = $this->status_code_404;
            $response['status'] = 'parameters are missing';
            echo json_encode($response);
        }
    }

}
