<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
  | -------------------------------------------------------------------------
  | URI ROUTING
  | -------------------------------------------------------------------------
  | This file lets you re-map URI requests to specific controller functions.
  |
  | Typically there is a one-to-one relationship between a URL string
  | and its corresponding controller class/method. The segments in a
  | URL normally follow this pattern:
  |
  |	example.com/class/method/id/
  |
  | In some instances, however, you may want to remap this relationship
  | so that a different class/function is called than the one
  | corresponding to the URL.
  |
  | Please see the user guide for complete details:
  |
  |	https://codeigniter.com/user_guide/general/routing.html
  |
  | -------------------------------------------------------------------------
  | RESERVED ROUTES
  | -------------------------------------------------------------------------
  |
  | There are three reserved routes:
  |
  |	$route['default_controller'] = 'welcome';
  |
  | This route indicates which controller class should be loaded if the
  | URI contains no data. In the above example, the "welcome" class
  | would be loaded.
  |
  |	$route['404_override'] = 'errors/page_missing';
  |
  | This route will tell the Router which controller/method to use if those
  | provided in the URL cannot be matched to a valid route.
  |
  |	$route['translate_uri_dashes'] = FALSE;
  |
  | This is not exactly a route, but allows you to automatically route
  | controller and method names that contain dashes. '-' isn't a valid
  | class or method name character, so it requires translation.
  | When you set this option to TRUE, it will replace ALL dashes in the
  | controller and method URI segments.
  |
  | Examples:	my-controller/index	-> my_controller/index
  |		my-controller/my-method	-> my_controller/my_method
 */
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Retrive
$route['getMyProfile'] = 'Retrive/getMyProfile';
$route['getShopsCategories'] = 'Retrive/getShopsCategories';
$route['getAdsType'] = 'Retrive/getAdsType';
$route['getMyPostAds'] = 'Retrive/getMyPostAds';
$route['getAdsDiscountType'] = 'Retrive/getAdsDiscountType';
$route['getOffersTitle'] = 'Retrive/getOffersTitle';
$route['getOffersFilter'] = 'Retrive/getOffersFilter';
$route['getOffers'] = 'Retrive/getOffers';
$route['getOffersUnderCategory'] = 'Retrive/getOffersUnderCategory';
$route['getOfferDetails'] = 'Retrive/getOfferDetails';
$route['getShops'] = 'Retrive/getShops';
$route['getShopDetails'] = 'Retrive/getShopDetails';
$route['getShopDetailsWithOffers'] = 'Retrive/getShopDetailsWithOffers';
$route['getMyShops'] = 'Retrive/getMyShops';
$route['getCategories'] = 'Retrive/getCategories';
$route['getCategoriesWithCount'] = 'Retrive/getCategoriesWithCount';
$route['getCategoriesWithOfferCount'] = 'Retrive/getCategoriesWithOfferCount';
$route['getPromo'] = 'Retrive/getPromo';
$route['getMyCart'] = 'Retrive/getMyCart';
$route['getAllShops'] = 'Retrive/getAllShops';
$route['getSingleAd'] = 'Retrive/getSingleAd';
$route['getShopsFollowedByUser'] = 'Retrive/getShopsFollowedByUser';

// Login
$route['getUser'] = 'Login/getUser';
$route['getOTP'] = 'Login/getOTP';

// Update
$route['profileUpdate'] = 'Update/profileUpdate';
$route['adsUpdate'] = 'Update/adsUpdate';
$route['adsUpdateNow'] = 'Update/adsUpdateNow';
$route['shopsUpdate'] = 'Update/shopsUpdate';
$route['shopsRRUpdate'] = 'Update/shopsRRUpdate';

// Delete
$route['adsDelete'] = 'Delete/adsDelete';
$route['shopDelete'] = 'Delete/shopDelete';
$route['userDelete'] = 'Delete/userDelete';

// Insert
$route['addToCart'] = 'Insert/addToCart';
$route['postAds'] = 'Insert/postAds';
$route['addOffers'] = 'Insert/addOffers';
$route['addCategories'] = 'Insert/addCategories';
$route['addShops'] = 'Insert/addShops';