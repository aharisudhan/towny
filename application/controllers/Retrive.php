<?php

require APPPATH . '/libraries/ImplementJwt.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

//status : success
//statusCode : 200
//error : false
//offer_type_id

class Retrive extends CI_Controller {

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
        echo "Lodding................";
    }

//change as done in myprofile --> myProfile
    public function getMyProfile() {
        $response = array();
        $response["error"] = false;
        $response["myProfile"] = array();
        $selectquery = "SELECT * FROM `users` WHERE  `users`.`user_status` = 0 AND `users`.`user_id` =" . $this->jwt_user_id;
        $Uset = $this->connect_db->query($selectquery)->result_array() [0];
        switch ($this->jwt_user_type) {
            case '1':
                $userrtype = 'admin';
                break;

            case '2':
                $userrtype = 'super admin';
                break;

            case '0':
                $userrtype = 'user';
                break;
        }
        if ($Uset['user_pincode'] == 0) {
            $userpincode = '';
        } else {
            $userpincode = $Uset['user_pincode'];
        }
        if (sizeof($Uset) > 0) {
            $imgsarray[] = json_decode($Uset['user_img']);
            $this->res_myprofile[] = array(
                'user_name' => $Uset['user_name'],
                'user_email' => $Uset['user_email'],
                'user_address' => $Uset['user_address'],
                'user_pincode' => $userpincode,
                'user_img' => $imgsarray[0],
                'user_id' => $Uset['user_id'],
                'user_phone' => $Uset['user_mobile'],
                'user_type_id' => $Uset['user_type'],
                'user_type' => $userrtype,
                'user_imgpath' => $Uset['user_filepath']
            );
            http_response_code('200');
            $response["status"] = $this->status_200;
            $response["statusCode"] = $this->status_code_200;
            $response["myProfile"] = $this->res_myprofile;
        } else {
            http_response_code('404');
            $response["status"] = $this->status_404;
            $response["statusCode"] = $this->status_code_404;
            $response["myProfile"] = "Profile not found";
        }
        echo json_encode($response);
    }

//    change made  shopcategories_list ==> shopCategoriesList
    public function getShopsCategories() {
        $response = array();
        $response["error"] = false;
        $response["shopCategoriesList"] = array();
        $selectquery = "SELECT * FROM `categories` WHERE  `categories`.`status` = 0";
        $Catresults = $this->connect_db->query($selectquery)->result_array();
        foreach ($Catresults as $skeys) {
            $this->res_disp_gcat[] = array(
                'category_id' => $skeys['category_id'],
                'category_name' => $skeys['category_name']
            );
        }
        http_response_code('200');
        $response["status"] = $this->status_200;
        $response["statusCode"] = $this->status_code_200;
        $response["shopCategoriesList"] = $this->res_disp_gcat;
        echo json_encode($response);
    }

//change made Ads_type --> AdsType
    public function getAdsType() {
        $response = array();
        $response["error"] = false;
        $response["AdsType"] = array();
        $selectquery = "SELECT * FROM `keywords2` WHERE keywords2.key_type = 'ads_type' AND `keywords2`.`key_status` = 0";
        $Catresults = $this->connect_db->query($selectquery)->result_array();
        foreach ($Catresults as $skeys) {
            $this->res_disp_adstype[] = array(
                'ads_type_id' => $skeys['key_id'],
                'ads_type_name' => $skeys['key_name']
            );
        }
        http_response_code('200');
        $response["status"] = $this->status_200;
        $response["statusCode"] = $this->status_code_200;
        $response["AdsType"] = $this->res_disp_adstype;
        echo json_encode($response);
    }

//    changes made my_postads --> myPostAds
    public function getMyPostAds() {
        $response = array();
        $response["error"] = false;
        $response["myPostAds"] = array();
        $selectquery = "SELECT `post_ads`.*,`categories`.`category_name`,`shops`.`shop_name` FROM `post_ads`,`shops`,`categories`,`keywords` WHERE post_ads.ads_status = 0 AND `post_ads`.`category_id` = `categories`.`category_id` AND `post_ads`.`shop_id` = `shops`.`shop_id` AND `post_ads`.`offer_id`=`keywords`.`key_id` AND `post_ads`.`user_id` =" . urldecode($this->jwt_user_id);
        $Catresults = $this->connect_db->query($selectquery)->result_array();
        if (sizeof($Catresults) > 0) {
            foreach ($Catresults as $skeys) {
                $imgsarray[] = json_decode($skeys['ads_img']);
                $this->res_disp_myads[] = array(
                    'ads_id' => $skeys['ads_id'],
                    'offer_id' => $skeys['offer_id'],
                    'category_id' => $skeys['category_id'],
                    'shop_id' => $skeys['shop_id'],
                    'user_id' => $skeys['user_id'],
                    'ads_type_id' => $skeys['ads_type_id'],
                    'ads_type_discount_id' => $skeys['ads_type_discount_id'],
                    'ads_name' => $skeys['ads_name'],
                    'ads_description' => $skeys['ads_description'],
                    'ads_img' => $imgsarray[0],
                    'ads_fpath' => $skeys['ads_fpath'],
                    'ads_likes' => $skeys['ads_likes'],
                    'ads_shares' => $skeys['ads_shares'],
                    'ads_views' => $skeys['ads_views'],
                    'ads_status' => $skeys['ads_status'],
                    'ads_posted_on' => $skeys['ads_posted_on'],
                    'ads_show_from' => $skeys['ads_show_from'],
                    'ads_updated_on' => $skeys['ads_updated_on'],
                    'ads_expires_on' => $skeys['ads_expires_on'],
                    'ads_original_price' => $skeys['ads_original_price'],
                    'ads_discount_price' => $skeys['ads_discount_price'],
                    'category_name' => $skeys['category_name'],
                    'shop_name' => $skeys['shop_name']
                );
            }
            http_response_code('200');
            $response["status"] = $this->status_200;
            $response["statusCode"] = $this->status_code_200;
            $response["myPostAds"] = $this->res_disp_myads;
        } else {
            http_response_code('404');
            $response["status"] = $this->status_404;
            $response["statusCode"] = $this->status_code_404;
            $response["myPostAds"] = "No Offers posted yet";
        }
        echo json_encode($response);
    }

//    changes made ads_discount_type --> adsDiscountType
    public function getAdsDiscountType() {
        $response = array();
        $response["error"] = false;
        $response["adsDiscountType"] = array();
        $selectquery = "SELECT * FROM `keywords` WHERE keywords.key_type = 'ads_discount_type' AND `keywords`.`key_status` = 0";
        $Catresults = $this->connect_db->query($selectquery)->result_array();
        foreach ($Catresults as $skeys) {
            $this->res_disp_ads_dtype[] = array(
                'ads_type_id' => $skeys['key_id'],
                'ads_type_name' => $skeys['key_name']
            );
        }
        http_response_code('200');
        $response["status"] = $this->status_200;
        $response["statusCode"] = $this->status_code_200;
        $response["adsDiscountType"] = $this->res_disp_ads_dtype;
        echo json_encode($response);
    }

//changes made offertitles_list -->offerTitlesList    
    public function getOffersTitle() {
        $response = array();
        $response["error"] = false;
        $response["offerTitlesList"] = array();
        $selectquery = "SELECT * FROM `keywords` WHERE `keywords`.`key_type` = 'offers_title' ";
        if ($_GET['otype'] === 'product') {
            $selectquery .= "AND `keywords`.`key_status` = 1";
        } else {
            $selectquery .= "AND `keywords`.`key_status` = 0";
        }

        $Catresults = $this->connect_db->query($selectquery)->result_array();
        foreach ($Catresults as $skeys) {
            $this->res_disp_goffert[] = array(
                'offertitle_id' => $skeys['key_id'],
                'offertitle_name' => $skeys['key_name']
            );
        }
        http_response_code('200');
        $response["status"] = $this->status_200;
        $response["statusCode"] = $this->status_code_200;
        $response["offerTitlesList"] = $this->res_disp_goffert;
        echo json_encode($response);
    }

//    change made offers_list --> offersList
    public function getOffersFilter() {
        $response = array();
        $response["error"] = false;
        $response["offersList"] = array();
        $selectquery = "SELECT * FROM `keywords` WHERE `keywords`.`key_type` = 'offers_filter' AND `keywords`.`key_status` = 0";
        $Catresults = $this->connect_db->query($selectquery)->result_array();
        foreach ($Catresults as $skeys) {
            $this->res_disp_gflist[] = array(
                'offersort_id' => $skeys['key_id'],
                'offersort_name' => $skeys['key_name']
            );
        }
        http_response_code('200');
        $response["status"] = $this->status_200;
        $response["statusCode"] = $this->status_code_200;
        $response["offersList"] = $this->res_disp_gflist;
        echo json_encode($response);
    }

//   no change
//    input parameters => lat,long,rad 
//    (i,e) Latitude , Longitude, Radius 
    public function getOffers() {
        $response = array();
        $response["error"] = false;
        $response["offers"] = array();
        $selectquery = "SELECT post_ads.offer_id,shops.shop_id,shops.shop_rating,shops.shop_name,offers.offer_expires, keywords.key_name as offer_name,offers.description,offers.extended_description,offers.offer_img,offers.offer_fpath, (
                        3959 * acos (
                        cos ( radians(" . urldecode($_GET['lat']) . "))
                        * cos( radians( `shop_latitude` ))
                        * cos( radians( `shop_longitude` ) - radians(" . urldecode($_GET['long']) . ") )
                        + sin ( radians(" . urldecode($_GET['lat']) . ") )
                        * sin( radians( `shop_latitude` ))
                        )
                        ) AS distance
                        FROM shops,post_ads,offers,keywords WHERE shops.shop_id = post_ads.shop_id AND post_ads.offer_id = offers.offer_id  AND post_ads.ads_status = 0 AND offers.status = 0 AND keywords.key_id = offers.offer_name GROUP BY post_ads.offer_id 
                        HAVING distance <" . urldecode($_GET['rad']) . " ORDER BY distance asc";
        $Cresults = $this->connect_db->query($selectquery)->result_array();
        if (sizeof($Cresults) > 0) {
            foreach ($Cresults as $skeys) {
                $this->res_disp_goffer[] = array(
                    'offer_id' => $skeys['offer_id'],
                    'offer_name' => $skeys['offer_name'],
                    'offer_img' => $skeys['offer_img'],
                    'offer_description' => $skeys['description'],
                    'offer_extended_description' => $skeys['extended_description'],
                    'offer_basepath' => $skeys['offer_fpath'],
                    'offer_expires' => $skeys['offer_expires'],
                    'shop_name' => $skeys['shop_name'],
                    'shop_rating' => $skeys['shop_rating'],
                    'distance' => $skeys['distance']
                );
            }
            http_response_code('200');
            $response["status"] = $this->status_200;
            $response["statusCode"] = $this->status_code_200;
            $response["offers"] = $this->res_disp_goffer;
        } else {

            http_response_code('404');
            $response["status"] = $this->status_404;
            $response["statusCode"] = $this->status_code_404;
            $response["offers"] = "No Offer found";
        }
        echo json_encode($response);
    }

//    change made offer_details to offerDetails
//    input parameters => lat,long,rad,catid
//    (i,e) Latitude , Longitude, Radius , Category ID
    public function getOffersUnderCategory() {
        $response = array();
        $response["error"] = false;
        $response["offerDetails"] = array();
        $selectquery = "SELECT post_ads.offer_id, post_ads.ads_id, shops.shop_id, shops.shop_rating, shops.shop_name, offers.offer_expires
       , categories.category_id, keywords.key_name as offer_name, offers.description, offers.extended_description, offers.offer_img, offers.offer_fpath
       , categories.category_name, ( 3959 * acos ( cos ( radians(" . urldecode($_GET['lat']) . ")) * cos( radians( `shop_latitude` )) * cos( radians( `shop_longitude` ) - radians(" . urldecode($_GET['long']) . ") ) + sin ( radians(" . urldecode($_GET['lat']) . ") ) * sin( radians( `shop_latitude` )) ) ) AS distance 
           FROM shops, post_ads, offers, keywords, categories WHERE shops.shop_id = post_ads.shop_id AND categories.category_id = post_ads.category_id AND post_ads.offer_id      = offers.offer_id
         AND offers.status = 0 AND categories.status = 0 AND post_ads.ads_status    = 0 AND shops.shop_status = 0  AND keywords.key_id        = offers.offer_name
         AND post_ads.category_id   = " . urldecode($_GET['catid']) . " HAVING distance <" . urldecode($_GET['rad']) . " ORDER BY distance asc";
        $Cresults = $this->connect_db->query($selectquery)->result_array();
        if (sizeof($Cresults) > 0) {
            foreach ($Cresults as $skeys) {
                $imgsarray[] = json_decode($skeys['offer_img']);
                $this->res_disp_gofferuc[] = array(
                    'offer_id' => $skeys['offer_id'],
                    'ads_id' => $skeys['ads_id'],
                    'offer_name' => $skeys['offer_name'],
                    'offer_img' => $imgsarray[0],
                    'category_id' => $skeys['category_id'],
                    'category_name' => $skeys['category_name'],
                    'offer_description' => $skeys['description'],
                    'offer_extended_description' => $skeys['extended_description'],
                    'offer_basepath' => $skeys['offer_fpath'],
                    'offer_expires' => $skeys['offer_expires'],
                    'shop_name' => $skeys['shop_name'],
                    'shop_id' => $skeys['shop_id'],
                    'shop_rating' => $skeys['shop_rating'],
                    'distance' => $skeys['distance']
                );
            }
            http_response_code('200');
            $response["status"] = $this->status_200;
            $response["statusCode"] = $this->status_code_200;
            $response["myProfile"] = $this->res_myprofile;
        } else {
            http_response_code('404');
            $response["status"] = $this->status_404;
            $response["statusCode"] = $this->status_code_404;
            $response["offerDetails"] = "No Offer is found";
        }
        echo json_encode($response);
    }

//    changes made selected_offer_details to selectedOfferDetails
//    input paramters => offer_id
//    (i,e) Ads ID
    public function getOfferDetails() {
        $response = array();
        $response["error"] = false;
        $response["selectedOfferDetails"] = array();
        $selectquery = "SELECT `post_ads`.`ads_id`,`post_ads`.`offer_id` as offer_type_id,`post_ads`.`shop_id`,`post_ads`.`user_id`,`post_ads`.`ads_name`,`post_ads`.`ads_img`,`post_ads`.`ads_fpath`,`post_ads`.`ads_likes`,`post_ads`.`ads_views`,`post_ads`.`ads_shares`,`shops`.`shop_name`,`shops`.`shop_img`,`shops`.`shop_fpath`,`shops`.`shop_latitude`,`shops`.`shop_longitude`,`shops`.`shop_rating`,`shops`.`shop_followers_count`,`shops`.`shop_phone`,`shops`.`shop_whatsapp`,`shops`.`shop_email`,`shops`.`shop_address`,`shops`.`shop_pincode`,`keywords`.`key_name` as offer_name,`offers`.`offer_img`,`offers`.`offer_fpath`FROM `post_ads`,`shops`,`offers`,`keywords` WHERE `offers`.`status` = 0 AND `post_ads`.`ads_status` = 0 AND `shops`.`shop_status` = 0 AND `keywords`.`key_status` = 0 AND `keywords`.`key_id` = `offers`.`offer_name` AND shops.shop_id = post_ads.shop_id AND `offers`.`offer_id` = `post_ads`.`offer_id` AND `post_ads`.`ads_id` =" . urldecode($_GET['offer_id']);
        $skeys = $this->connect_db->query($selectquery)->result_array()[0];
        if (sizeof($skeys) > 0) {
            $selectuserfollow = "SELECT shop_follower.fs_status FROM shop_follower WHERE shop_follower.shop_id = " . $skeys['shop_id'] . " AND shop_follower.user_id =" . $this->jwt_user_id;
            $usersfollows = $this->connect_db->query($selectuserfollow)->result_array()[0];
            if (sizeof($usersfollows) > 0) {
                if ($usersfollows['fs_status'] == 0) {
                    $su_follow = "yes";
                } else {
                    $su_follow = "no";
                }
            } else {
                $su_follow = "no";
            }
            $offer_img[] = json_decode($skeys['offer_img']);
            $shopsimg[] = json_decode($skeys['shop_img']);
            $ads_img[] = json_decode($skeys['ads_img']);
            $this->res_disp_gofferview[] = array('ads_id' => $skeys['ads_id'],
                'offer_type_id' => $skeys['offer_type_id'],
                'shop_id' => $skeys['shop_id'],
                'user_id' => $skeys['user_id'],
                'ads_name' => $skeys['ads_name'],
                'ads_img' => $ads_img[0],
                'ads_fpath' => $skeys['ads_fpath'],
                'ads_likes' => $skeys['ads_likes'],
                'ads_views' => $skeys['ads_views'],
                'ads_shares' => $skeys['ads_shares'],
                'shop_name' => $skeys['shop_name'],
                'shop_img' => $shopsimg[0],
                'shop_fpath' => $skeys['shop_fpath'],
                'shop_latitude' => $skeys['shop_latitude'],
                'shop_longitude' => $skeys['shop_longitude'],
                'shop_rating' => $skeys['shop_rating'],
                'shop_followers_count' => $skeys['shop_followers_count'],
                'shop_phone' => $skeys['shop_phone'],
                'shop_whatsapp' => $skeys['shop_whatsapp'],
                'shop_email' => $skeys['shop_email'],
                'shop_address' => $skeys['shop_address'],
                'shop_pincode' => $skeys['shop_pincode'],
                'offer_type_name' => $skeys['offer_name'],
                'offer_type_img' => $offer_img[0],
                'offer_type_fpath' => $skeys['offer_fpath'],
                'user_follow_status' => $su_follow);
            http_response_code('200');
            $response["status"] = $this->status_200;
            $response["statusCode"] = $this->status_code_200;
            $response["selectedOfferDetails"] = $this->res_disp_gofferview;
        } else {
            http_response_code('404');
            $response["status"] = $this->status_404;
            $response["statusCode"] = $this->status_code_404;
            $response["selectedOfferDetails"] = "No data found";
        }
        echo json_encode($response);
    }

//    no changes
//    input paramters => lat,long,rad
//    (i,e) Latitude , Longitude, Radius 
    public function getShops() {
        $response = array();
        $response["error"] = false;
        $response["shops"] = array();
        $selectquery = "SELECT shops.shop_id,shops.shop_name,shops.shop_owner_name,shops.shop_img,shops.shop_fpath,shops.shop_description,post_ads.offer_id,post_ads.category_id,categories.category_name as shop_category,shops.shop_latitude,shops.shop_longitude,shops.shop_rating,shops.shop_followers_count,shops.shop_reviews,shops.shop_reviews_count,shops.shop_phone,shops.shop_whatsapp,shops.shop_email,shops.shop_address,shops.shop_pincode,(
              3959 * acos (
              cos( radians(" . urldecode($_GET['lat']) . "))
              * cos( radians( `shop_latitude` ))
              * cos( radians( `shop_longitude` ) - radians(" . urldecode($_GET['long']) . ") )
              + sin( radians(" . urldecode($_GET['lat']) . ") )
              * sin( radians( `shop_latitude` ))
              )
              ) AS distance
              FROM shops,post_ads,categories
              WHERE shops.shop_status=0 AND shops.shop_id = post_ads.shop_id GROUP BY shops.shop_id  
              HAVING distance <" . urldecode($_GET['rad']) . " LIMIT 0,10";
        $Cresults = $this->connect_db->query($selectquery)->result_array();
        foreach ($Cresults as $skeys) {
            $imgsarray[] = json_decode($skeys['shop_img']);
            $this->res_gshops[] = array(
                'shop_id' => $skeys['shop_id'],
                'offer_id' => $skeys['offer_id'],
                'category_id' => $skeys['category_id'],
                'shop_name' => $skeys['shop_name'],
                'shop_owner_name' => $skeys['shop_owner_name'],
                'shop_img' => $imgsarray[0],
                'shop_fpath' => $skeys['shop_fpath'],
                'shop_description' => $skeys['shop_description'],
                'shop_category' => $skeys['shop_category'],
                'shop_latitude' => $skeys['shop_latitude'],
                'shop_longitude' => $skeys['shop_longitude'],
                'shop_rating' => $skeys['shop_rating'],
                'shop_followers_count' => $skeys['shop_followers_count'],
                'shop_reviews' => $skeys['shop_reviews'],
                'shop_reviews_count' => $skeys['shop_reviews_count'],
                'shop_phone' => $skeys['shop_phone'],
                'shop_whatsapp' => $skeys['shop_whatsapp'],
                'shop_email' => $skeys['shop_email'],
                'shop_address' => $skeys['shop_address'],
                'shop_pincode' => $skeys['shop_pincode'],
                'distance' => $skeys['distance']
            );
        }
        http_response_code('200');
        $response["status"] = $this->status_200;
        $response["statusCode"] = $this->status_code_200;
        $response["shops"] = $this->res_gshops;
        echo json_encode($response);
    }

//    changes made shops_details ==> shopsDetails
//    input paramters => lat,long,rad,shop_id
//    (i,e) Latitude , Longitude, Radius,Shop ID 
    public function getShopDetails() {
        $response = array();
        $response["error"] = false;
        $response["shopsDetails"] = array();
        if (strlen($_GET['shop_id']) > 0 && strlen($this->jwt_user_id)) {
            $selectquery = "SELECT shops.shop_name,shops.shop_owner_name,shops.shop_img,shops.shop_fpath,shops.shop_description,categories.category_name as shop_category,shops.shop_latitude,shops.shop_longitude,shops.shop_rating,shops.shop_followers_count,shops.shop_reviews,shops.shop_reviews_count,shops.shop_phone,shops.shop_whatsapp,shops.shop_email,shops.shop_address,shops.shop_pincode FROM `shops`,`categories` WHERE shops.shop_status = 0 AND shops.shop_category = categories.category_id AND shops.shop_id =" . urldecode($_GET['shop_id']);
            $floowerid = urldecode($this->jwt_user_id) . urldecode($_GET['shop_id']);
            $selectexitsting = "SELECT * FROM `shop_follower` where fid =" . $floowerid;
            $checkexits = $this->connect_db->query($selectexitsting)->result_array() [0];

            if (!array_filter($checkexits) == []) {
                $follow_status = "yes";
            } else {
                $follow_status = "no";
            }

            $skeys = $this->connect_db->query($selectquery)->result_array() [0];
            if (sizeof($skeys) > 0) {
                $imgsarray[] = json_decode($skeys['shop_img']);
                $this->res_gshopsdetails[] = array(
                    'shop_name' => $skeys['shop_name'],
                    'shop_owner_name' => $skeys['shop_owner_name'],
                    'shop_img' => $imgsarray[0],
                    'shop_fpath' => $skeys['shop_fpath'],
                    'shop_description' => $skeys['shop_description'],
                    'shop_category' => $skeys['shop_category'],
                    'shop_latitude' => $skeys['shop_latitude'],
                    'shop_longitude' => $skeys['shop_longitude'],
                    'shop_rating' => $skeys['shop_rating'],
                    'shop_followers_count' => $skeys['shop_followers_count'],
                    'shop_reviews' => $skeys['shop_reviews'],
                    'shop_reviews_count' => $skeys['shop_reviews_count'],
                    'shop_phone' => $skeys['shop_phone'],
                    'shop_whatsapp' => $skeys['shop_whatsapp'],
                    'shop_email' => $skeys['shop_email'],
                    'shop_address' => $skeys['shop_address'],
                    'shop_pincode' => $skeys['shop_pincode'],
                    'shop_following' => $follow_status
                );
                http_response_code('200');
                $response["status"] = $this->status_200;
                $response["statusCode"] = $this->status_code_200;
                $response["shopsDetails"] = $this->res_gshopsdetails;
            } else {
                http_response_code('404');
                $response["status"] = $this->status_404;
                $response["statusCode"] = $this->status_code_404;
                $response["shopsDetails"] = "shop is not found";
            }
        } else {
            http_response_code('404');
            $response["status"] = $this->status_404;
            $response["statusCode"] = $this->status_code_404;
            $response["shopsDetails"] = "parameters are missing";
        }
        echo json_encode($response);
    }

//  changes made shops_details => shopsDetails
//  input parameters  => shop_id
//   (i,e) Shop ID
    public function getShopDetailsWithOffers() {
        $response = array();
        $response["error"] = false;
        $response["offerDetails"] = array();
        $response["shopsDetails"] = array();

        if (strlen($_GET['shop_id']) > 0 && strlen($this->jwt_user_id)) {
            $selectquery = "SELECT shops.shop_name,shops.shop_owner_name,shops.shop_img,shops.shop_fpath,shops.shop_description,categories.category_name as shop_category,shops.shop_latitude,shops.shop_longitude,shops.shop_rating,shops.shop_followers_count,shops.shop_reviews,shops.shop_reviews_count,shops.shop_phone,shops.shop_whatsapp,shops.shop_email,shops.shop_address,shops.shop_pincode FROM `shops`,`categories` WHERE shops.shop_category = categories.category_id AND shops.shop_id =" . urldecode($_GET['shop_id']);
            $selectofferquery = "SELECT    `post_ads`.`offer_id`,`shops`.`shop_id`,`shops`.`shop_rating`,`shops`.`shop_name`,`offers`.`offer_expires`,`offers`.`description`,`offers`.`extended_description`
                ,`offers`.`offer_img`,`offers`.`offer_fpath`,`keywords`.`key_name` as `offer_name`,`categories`.`category_id`,`categories`.`category_name`
            FROM `shops`, `post_ads`, `offers`, `keywords`, `categories` WHERE `post_ads`.`category_id`= `categories`.`category_id` AND `shops`.`shop_id`= `post_ads`.`shop_id` AND `categories`.`category_id` = `post_ads`.`category_id`  AND `post_ads`.`offer_id = `offers`.`offer_id` AND `post_ads`.`ads_status = 0 AND `offers`.`status` = 0 AND `keywords`.`key_id` = `offers`.`offer_name` AND `post_ads`.`shop_id` =" . urldecode($_GET['shop_id']);
            $floowerid = urldecode($this->jwt_user_id) . urldecode($_GET['shop_id']);
            $selectexitsting = "SELECT * FROM `shop_follower` where fid =" . $floowerid;
            $checkexits = $this->connect_db->query($selectexitsting)->result_array() [0];
            if (!array_filter($checkexits) == []) {
                $follow_status = "yes";
            } else {
                $follow_status = "no";
            }

            $skeys = $this->connect_db->query($selectquery)->result_array() [0];
            if (sizeof($skeys) > 0) {
                $imgsarray[] = json_decode($skeys['shop_img']);
                $this->res_gshopsdetails[] = array(
                    'shop_name' => $skeys['shop_name'],
                    'shop_owner_name' => $skeys['shop_owner_name'],
                    'shop_img' => $imgsarray[0],
                    'shop_fpath' => $skeys['shop_fpath'],
                    'shop_description' => $skeys['shop_description'],
                    'shop_category' => $skeys['shop_category'],
                    'shop_latitude' => $skeys['shop_latitude'],
                    'shop_longitude' => $skeys['shop_longitude'],
                    'shop_rating' => $skeys['shop_rating'],
                    'shop_followers_count' => $skeys['shop_followers_count'],
                    'shop_reviews' => $skeys['shop_reviews'],
                    'shop_reviews_count' => $skeys['shop_reviews_count'],
                    'shop_phone' => $skeys['shop_phone'],
                    'shop_whatsapp' => $skeys['shop_whatsapp'],
                    'shop_email' => $skeys['shop_email'],
                    'shop_address' => $skeys['shop_address'],
                    'shop_pincode' => $skeys['shop_pincode'],
                    'shop_following' => $follow_status
                );
                $response["shopsDetails"] = $this->res_gshopsdetails;
            } else {
                $response["shopsDetails"] = 'shop is not found';
            }
            $Cofferresults = $this->connect_db->query($selectofferquery)->result_array();
            foreach ($Cofferresults as $dkey) {
                $imgsarray1[] = json_decode($dkey['offer_img']);
                $this->res_shop_offer[] = array(
                    'offer_id' => $dkey['offer_id'],
                    'offer_name' => $dkey['offer_name'],
                    'offer_img' => $imgsarray1[0],
                    'category_id' => $dkey['category_id'],
                    'category_name' => $dkey['category_name'],
                    'offer_description' => $dkey['description'],
                    'offer_extended_description' => $dkey['extended_description'],
                    'offer_basepath' => $dkey['offer_fpath'],
                    'offer_expires' => $dkey['offer_expires'],
                    'shop_name' => $dkey['shop_name'],
                    'shop_id' => $dkey['shop_id'],
                    'shop_rating' => $dkey['shop_rating'],
                    'distance' => is_null($dkey['distance']) ? "" : $dkey['distance']
                );
            }
            http_response_code('200');
            $response["status"] = $this->status_200;
            $response["statusCode"] = $this->status_code_200;
            $response["offerDetails"] = $this->res_shop_offer;
        } else {
            http_response_code('404');
            $response["status"] = $this->status_404;
            $response["statusCode"] = $this->status_code_404;
            $response["shopsDetails"] = "parameters are missing";
        }
        echo json_encode($response);
    }

//    changes made myshops => myShops
    public function getMyShops() {
        $response = array();
        $response["error"] = false;
        $response["myShops"] = array();
        $selectquery = "SELECT shops.shop_whatsapp,shops.shop_id,shops.shop_name,shops.shop_img,shops.shop_fpath,shops.shop_description,shops.shop_category,shop_latitude,shops.shop_longitude,shops.shop_phone,shops.shop_email,shops.shop_address,categories.category_name,shops.shop_pincode,shops.shop_owner_name FROM `shops`,`categories` WHERE shops.shop_status = 0 AND shops.shop_category = categories.category_id AND shops.marchant_id =" . urldecode($this->jwt_user_id);
        $Cresults = $this->connect_db->query($selectquery)->result_array();
        if (sizeof($Cresults) > 0) {
            foreach ($Cresults as $rkeys) {
                $imgsarray[] = json_decode($rkeys['shop_img']);
                $this->res_gmyshops[] = array(
                    'shop_id' => $rkeys['shop_id'],
                    'shop_name' => $rkeys['shop_name'],
                    'shop_img' => $imgsarray[0],
                    'shop_fpath' => $rkeys['shop_fpath'],
                    'shop_pincode' => $rkeys['shop_pincode'],
                    'shop_owner_name' => $rkeys['shop_owner_name'],
                    'shop_description' => $rkeys['shop_description'],
                    'shop_category' => $rkeys['shop_category'],
                    'shop_latitude' => $rkeys['shop_latitude'],
                    'shop_whatsapp' => $rkeys['shop_whatsapp'],
                    'shop_longitude' => $rkeys['shop_longitude'],
                    'shop_phone' => $rkeys['shop_phone'],
                    'shop_email' => $rkeys['shop_email'],
                    'shop_address' => $rkeys['shop_address'],
                    'category_name' => $rkeys['category_name']
                );
                http_response_code('200');
                $response["status"] = $this->status_200;
                $response["statusCode"] = $this->status_code_200;
                $response["myShops"] = $this->res_gmyshops;
            }
        } else {
            http_response_code('404');
            $response["status"] = $this->status_404;
            $response["statusCode"] = $this->status_code_404;
            $response["myShops"] = "No shops found";
        }

        echo json_encode($response);
    }

//    input paramters => lat,long,rad
//    (i,e) Latitude , Longitude, Radius,
    public function getCategories() {
        $response = array();
        $response["error"] = false;
        $response["categories"] = array();
        $selectquery = "SELECT COUNT(shops.shop_id) as shops_count,post_ads.offer_id, post_ads.category_id,categories.category_name,categories.category_img,categories.category_description,categories.category_extended_description ,(
              3959 * acos (
              cos ( radians(" . urldecode($_GET['lat']) . "))
              * cos( radians( `shop_latitude` ))
              * cos( radians( `shop_longitude` ) - radians(" . urldecode($_GET['long']) . ") )
              + sin ( radians(" . urldecode($_GET['lat']) . ") )
              * sin( radians( `shop_latitude` ))
              )
              ) AS distance
              FROM shops,post_ads,categories
              WHERE categories.status = 0 AND shops.shop_id = post_ads.shop_id AND post_ads.category_id = categories.category_id AND post_ads.offer_id =" . urldecode($_GET['offerid']) . " GROUP BY categories.category_id
              HAVING distance <" . urldecode($_GET['rad']);
        $Cresults = $this->connect_db->query($selectquery)->result_array();
        if (sizeof($Cresults) > 0) {
            foreach ($Cresults as $skeys) {
                $imgsarray[] = json_decode($skeys['category_img']);
                $this->res_disp[] = array(
                    'shops_count' => $skeys['shops_count'],
                    'offer_id' => $skeys['offer_id'],
                    'category_id' => $skeys['category_id'],
                    'category_name' => $skeys['category_name'],
                    'category_img' => $imgsarray[0],
                    'category_description' => $skeys['category_description'],
                    'category_extended_description' => $skeys['category_extended_description'],
                    'distance' => $skeys['distance']
                );
            }
            http_response_code('200');
            $response["status"] = $this->status_200;
            $response["statusCode"] = $this->status_code_200;
            $response["categories"] = $this->res_disp;
        } else {
            http_response_code('404');
            $response["status"] = $this->status_404;
            $response["statusCode"] = $this->status_code_404;
            $response["categories"] = "Categories not found";
        }
        echo json_encode($response);
    }

// changes made categories_with_count => categoriesWithCount
//    input paramters => lat,long,rad
//    (i,e) Latitude , Longitude, Radius,
    public function getCategoriesWithCount() {
        $response = array();
        $response["error"] = false;
        $response["categoriesWithCount"] = array();
        $selectquery = "SELECT COUNT(post_ads.ads_id) as offers_count , post_ads.category_id, categories.category_name as category_name, categories.category_img, categories.category_img_fpath, categories.category_description, categories.category_extended_description 
            FROM shops, post_ads, categories WHERE categories.status = 0 AND shops.shop_status = 0 AND post_ads.ads_status = 0 AND shops.shop_id = post_ads.shop_id AND post_ads.category_id = categories.category_id AND post_ads.offer_id in (select x.offer_id from (select distinct post_ads.offer_id,( 3959 * acos ( cos ( radians(" . urldecode($_GET['lat']) . ")) * cos( radians( `shop_latitude` )) * cos( radians( `shop_longitude` ) - radians(" . urldecode($_GET['long']) . ") ) + sin ( radians(" . urldecode($_GET['lat']) . ") ) * sin( radians( `shop_latitude` )) ) ) AS distance from post_ads, shops where shops.shop_id = post_ads.shop_id and shops.shop_status = 0 HAVING  distance < " . urldecode($_GET['rad']) . ")x) 
            GROUP BY categories.category_id";
        $Cresults = $this->connect_db->query($selectquery)->result_array();
        foreach ($Cresults as $skeys) {
            $imgsarray[] = json_decode($skeys['category_img']);
            $this->res_disp_cw[] = array(
                'count' => $skeys['shops_count'],
                'category_id' => $skeys['category_id'],
                'category_name' => $skeys['category_name'],
                'category_img_name' => $imgsarray[0],
                'category_img_fpath' => $skeys['category_img_fpath'],
                'category_description' => $skeys['category_description'],
                'category_extended_description' => $skeys['category_extended_description'],
                'distance' => $skeys['distance']
            );
        }
        http_response_code('200');
        $response["status"] = $this->status_200;
        $response["statusCode"] = $this->status_code_200;
        $response["categoriesWithCount"] = $this->res_disp_cw;
        echo json_encode($response);
    }

// changes made categories_offer_with_count => categoriesWithOfferCount
//    input paramters => lat,long,rad
//    (i,e) Latitude , Longitude, Radius,
    public function getCategoriesWithOfferCount() {
        $response = array();
        $response["error"] = false;
        $response["categoriesWithOfferCount"] = array();
        $selectquery = "SELECT COUNT(post_ads.offer_id) as offers_count , post_ads.category_id, categories.category_name as category_name
       , categories.category_img, categories.category_img_fpath, categories.category_description, categories.category_extended_description
       , ( 3959 * acos ( cos ( radians(" . urldecode($_GET['lat']) . ")) * cos( radians( `shop_latitude` )) * cos( radians( `shop_longitude` ) - radians(" . urldecode($_GET['long']) . ") ) + sin ( radians(" . urldecode($_GET['lat']) . ") ) * sin( radians( `shop_latitude` )) ) ) AS distance
        FROM shops, post_ads, categories WHERE categories.status =0 AND shops.shop_id = post_ads.shop_id AND post_ads.category_id = categories.category_id GROUP BY categories.category_id HAVING distance <" . urldecode($_GET['rad']);
        $Cresults = $this->connect_db->query($selectquery)->result_array();
        foreach ($Cresults as $skeys) {
            $this->res_disp_cw[] = array(
                'count' => $skeys['offers_count'],
                'category_id' => $skeys['category_id'],
                'category_name' => $skeys['category_name'],
                'category_img_name' => $skeys['category_img'],
                'category_img_fpath' => $skeys['category_img_fpath'],
                'category_description' => $skeys['category_description'],
                'category_extended_description' => $skeys['category_extended_description'],
                'distance' => $skeys['distance']
            );
        }
        http_response_code('200');
        $response["status"] = $this->status_200;
        $response["statusCode"] = $this->status_code_200;
        $response["categoriesWithOfferCount"] = $this->res_disp_cw;
        echo json_encode($response);
    }

// changes made categories_offer_with_count => categoriesWithOfferCount
//    input paramters => lang_id
//    (i,e)Language
    public function getPromo() {
        if (urldecode(strtolower($_GET["lang_id"])) == 'english') {
            $response = array();
            $sql = 'SELECT `pid`, `pname`, `pimg_path`, `pdesc`, `status` FROM `promotions`';
            $result = $this->connect_db->query($sql)->result_array();
            $response["error"] = false;
            $response["promotions"] = array();
            foreach ($result as $skeys) {
                $this->res_dispromo[] = array(
                    'pid' => $skeys['pid'],
                    'pname' => $skeys['pname'],
                    'pimg_path' => $skeys['pimg_path'],
                    'pdesc' => $skeys['pdesc'],
                    'status' => $skeys['status']
                );
            }
            http_response_code('200');
            $response["status"] = $this->status_200;
            $response["statusCode"] = $this->status_code_200;
            $response["promotions"] = $this->res_dispromo;
        } else {
            http_response_code('404');
            $response["status"] = $this->status_404;
            $response["statusCode"] = $this->status_code_404;
            $response["promotions"] = 'Request Cannot be processed';
        }
        echo json_encode($response);
    }

//changes made mycart => myCart
    public function getMyCart() {
        $response = array();
        $response["error"] = false;
        $response["myCart"] = array();
        if (urldecode(strtolower($_GET["ctype"])) == 'onlycount') {
            $selectquery = 'SELECT COUNT(*) as `my_cart_count` FROM `mycart` WHERE `mycart`.`user_id`=' . urldecode($this->jwt_user_id);
            $result = $this->connect_db->query($selectquery)->result_array() [0];
            echo $result['my_cart_count'];
        } elseif (urldecode(strtolower($_GET["ctype"])) == 'viewcart') {
            $selectquery = 'SELECT `mycart`.`quantity`,`mycart`.`mcart_id`,`mycart`.`fav_status`,`post_ads`.`ads_name`,`post_ads`.`ads_description`,`post_ads`.`ads_img`,`post_ads`.`ads_likes`,`post_ads`.`ads_shares`,`post_ads`.`ads_views` FROM `mycart`,`post_ads` WHERE `post_ads`.`ads_status` =0 AND `mycart`.`order_status` =0 AND `mycart`.`remove_status` =0  AND `mycart`.`ads_id` = `post_ads`.`ads_id` AND `mycart`.`user_id`=' . urldecode($this->jwt_user_id);
            $result = $this->connect_db->query($selectquery)->result_array();
            foreach ($result as $rkeys) {
                $this->res_viewcart[] = array(
                    'mcart_id' => $rkeys['mcart_id'],
                    'quantity' => $rkeys['quantity'],
                    'product_name' => $rkeys['ads_name'],
                    'product_desc' => $rkeys['ads_description'],
                    'product_img' => $rkeys['ads_img'],
                    'product_likes' => $rkeys['ads_likes'],
                    'product_shares' => $rkeys['ads_shares'],
                    'product_views' => $rkeys['ads_views']
                );
            }
            http_response_code('200');
            $response["status"] = $this->status_200;
            $response["statusCode"] = $this->status_code_200;
            $response["myCart"] = $this->res_viewcart;
        } else {
            http_response_code('404');
            $response["status"] = $this->status_404;
            $response["statusCode"] = $this->status_code_404;
            $response["myCart"] = 'Request Cannot be processed';
        }
        echo json_encode($response);
    }

//    input parameters =>searchshop
    public function getAllShops() {
        $response = array();
        $response["error"] = false;
        $response["storeDetails"] = array();
        $selectquery = "SELECT shops.*, categories.category_name  FROM `shops`,`categories` WHERE shops.shop_category = categories.category_id AND shops.shop_status=0";
        if (!empty($_GET['searchshop'])) {
            $selectquery .= " AND shops.shop_name LIKE '%" . urldecode($_GET['searchshop']) . "%'";
        }
        $selectquery .= " ORDER BY shops.shop_name";
        $result = $this->connect_db->query($selectquery)->result_array();
        foreach ($result as $rkeys) {
            $this->res_viewcart[] = array(
                'store_id' => $rkeys['shop_id'],
                'store_name' => $rkeys['shop_name'],
            );
        }
        http_response_code('200');
        $response["status"] = $this->status_200;
        $response["statusCode"] = $this->status_code_200;
        $response["shopsCount"] = sizeof($result);
        $response["storeDetails"] = (is_null($this->res_viewcart)) ? array() : $this->res_viewcart;
        echo json_encode($response);
    }

//    input parameter => ads_id
    public function getSingleAd() {
        $response = array();
        $response["error"] = false;
        $response["singleAd"] = array();
        if ($this->jwt_user_id != NULL && strlen($this->jwt_user_id) > 0 && $_GET['ads_id'] != NULL && strlen($_GET['ads_id']) > 0) {
            $selectquery = "SELECT post_ads.`offer_id`,post_ads.`product_name`, post_ads.`category_id`, post_ads.`shop_id`, post_ads.`ads_name`, post_ads.`ads_description`, post_ads.`ads_img`, post_ads.`user_id`,post_ads.`ads_fpath`,post_ads.`ads_type_id`,post_ads.`ads_type_discount_id`,post_ads.`ads_original_price`,post_ads.`ads_discount_price`,post_ads.`ads_show_from`,post_ads.`ads_expires_on`,keywords.key_name as offer_name, categories.category_name, shops.shop_name,keywords2.key_name as ads_type,users.user_name FROM post_ads,keywords2,keywords,categories,shops,users WHERE categories.category_id = post_ads.category_id AND keywords.key_id = post_ads.offer_id AND shops.shop_id = post_ads.shop_id AND users.user_id = post_ads.user_id AND keywords2.key_id = post_ads.ads_type_id AND post_ads.ads_id = " . $_GET['ads_id'] . " AND post_ads.user_id = " . $this->jwt_user_id;
            $rkey = $this->connect_db->query($selectquery)->result_array()[0];
            if (sizeof($rkey) > 0) {
                $imgsarray[] = json_decode($rkey['ads_img']);
                $this->res_singlead[] = array(
                    'offer_id' => $rkey['offer_id'],
                    'category_id' => $rkey['category_id'],
                    'shop_id' => $rkey['shop_id'],
                    'ads_name' => $rkey['ads_name'],
                    'ads_description' => $rkey['ads_description'],
                    'ads_img' => $imgsarray[0],
                    'user_id' => $rkey['user_id'],
                    'ads_fpath' => $rkey['ads_fpath'],
                    'ads_type_id' => $rkey['ads_type_id'],
                    'ads_type_discount_id' => $rkey['ads_type_discount_id'],
                    'ads_original_price' => $rkey['ads_original_price'],
                    'ads_discount_price' => $rkey['ads_discount_price'],
                    'ads_show_from' => $rkey['ads_show_from'],
                    'ads_expires_on' => $rkey['ads_expires_on'],
                    'offer_name' => $rkey['offer_name'],
                    'category_name' => $rkey['category_name'],
                    'shop_name' => $rkey['shop_name'],
                    'ads_type' => $rkey['ads_type'],
                    'user_name' => $rkey['user_name'],
                    'product_name' => $rkey['product_name']
                );
                http_response_code('200');
                $response["status"] = $this->status_200;
                $response["statusCode"] = $this->status_code_200;
                $response["singleAd"] = $this->res_singlead;
            } else {
                http_response_code('404');
                $response["status"] = $this->status_404;
                $response["statusCode"] = $this->status_code_404;
                $response["singleAd"] = "no data found";
            }
        } else {

            http_response_code('404');
            $response["status"] = $this->status_404;
            $response["statusCode"] = $this->status_code_404;
            $response["singleAd"] = "parameters are missing";
        }
        echo json_encode($response);
    }

// changes made shops_followed_by_user => shopsFollowedByUser
    public function getShopsFollowedByUser() {
        $response = array();
        $response["error"] = false;
        $response["shopsFollowedByUser"] = array();
        $selectquery = "SELECT `shop_follower`.`shop_id`,`shops`.`shop_name`,`shops`.`shop_owner_name`,`shops`.`shop_img`,`shops`.`shop_fpath`,`shops`.`marchant_id`,`shops`.`shop_description`,`shops`.`shop_category`,`shops`.`shop_latitude`,`shops`.`shop_longitude`,`shops`.`shop_rating`,`shops`.`shop_followers_count`,`shops`.`shop_phone`,`shops`.`shop_whatsapp`,`shops`.`shop_address`,`shops`.`shop_pincode`,`categories`.`category_name` FROM `shop_follower`,`categories`,`shops` WHERE `shop_follower`.fs_status = 0 AND `shops`.shop_id = `shop_follower`.shop_id AND `shops`.`shop_category` = `categories`.`category_id` AND `shop_follower`.user_id =" . urldecode($this->jwt_user_id);
        $Cresults = $this->connect_db->query($selectquery)->result_array();
        if (sizeof($Cresults) > 0) {
            foreach ($Cresults as $rkeys) {
                $imgsarray[] = json_decode($rkeys['shop_img']);
                $this->res_gmyshops[] = array(
                    'shop_id' => $rkeys['shop_id'],
                    'shop_name' => $rkeys['shop_name'],
                    'shop_img' => $imgsarray[0],
                    'shop_fpath' => $rkeys['shop_fpath'],
                    'shop_pincode' => $rkeys['shop_pincode'],
                    'shop_owner_name' => $rkeys['shop_owner_name'],
                    'shop_description' => $rkeys['shop_description'],
                    'shop_category' => $rkeys['shop_category'],
                    'shop_latitude' => $rkeys['shop_latitude'],
                    'shop_whatsapp' => $rkeys['shop_whatsapp'],
                    'shop_longitude' => $rkeys['shop_longitude'],
                    'shop_phone' => $rkeys['shop_phone'],
                    'shop_email' => $rkeys['shop_email'],
                    'shop_address' => $rkeys['shop_address'],
                    'category_name' => $rkeys['category_name']
                );

                http_response_code('200');
                $response["status"] = $this->status_200;
                $response["statusCode"] = $this->status_code_200;
                $response["shopsFollowedByUser"] = $this->res_gmyshops;
            }
        } else {
            http_response_code('404');
            $response["status"] = $this->status_404;
            $response["statusCode"] = $this->status_code_404;
            $response["shopsFollowedByUser"] = "No shops found";
        }
        echo json_encode($response);
    }

}
