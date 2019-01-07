<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <title>Test</title>
    </head>
    <bodY>
    <form enctype="multipart/form-data" action="http://shopclicks.in.md-24.webhostbox.net/ci/Update/ProfileUpdate/" method="POST">
        <input type="text" name="user_id" value="1003"/>
        <input type="text" name="user_phone" value="7418525241"/>
        <input type="text" name="user_email" placeholder="email"/>
        <input type="text" name="user_address" placeholder="address"/>
        <input type="text" name="user_pincode" placeholder="pincode"/>
        <input type="text" name="user_name" placeholder="name"/>
        
        <input type="hidden" name="MAX_FILE_SIZE" value="512000" />
        Send this file: <input name="user_img" type="file" />
        <!--<input type="text" name="shop_name" value="Changed shops"/>-->
        <!--<input type="text" name="shop_category" value="5"/>-->
        <!--<input type="text" name="shop_lati" value="-33.7379"/>-->
        <!--<input type="text" name="shop_logi" value="151.235 "/>-->
        <!--<input type="text" name="shop_phone" value="845120852 "/>-->
        <!--<input type="text" name="shop_email" placeholder="shop_email "/>-->
        <!--<input type="text" name="shop_desc" placeholder="shop_desc "/>-->
        <!--<input type="text" name="shop_address" value="shop_address"/>-->
        <input type="submit" value="Send File" />
    </form>
    </bodY>
</html>