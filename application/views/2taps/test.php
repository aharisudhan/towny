<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <title>Test</title>
    </head>
    <bodY>
         
       
    <form enctype="multipart/form-data" action="testImgUpload" method="POST">
        <input type="hidden" name="MAX_FILE_SIZE" value="512000" />
        Send this file: <input name="file[]" type="file" multiple='' /><!-- 
        <input type="text" name="shop_name" placeholder="shop_name"/>
        <input type="text" name="shop_lati" placeholder="shop_lati"/>
        <input type="text" name="shop_logi" placeholder="shop_logi "/>
        <input type="text" name="shop_phone" placeholder="shop_phone "/>
        <input type="text" name="shop_pincode" placeholder="shop_pincode "/>
        <input type="text" name="owner_name" placeholder="owner_name "/>
        <input type="text" name="user_id" placeholder="user_id "/>
        <input type="text" name="shop_email" placeholder="shop_email "/>
        <input type="text" name="shop_address" placeholder="shop_address"/>
        <input type="text" name="shop_category" placeholder="shop_category"/> -->
        <input type="submit" value="Send File" />
    </form>
    </bodY>
</html>