<?php

/*!
 * https://raccoonsquare.com, https://raccoonjohn.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2026 Demianchuk Dmytro and Raccoon John (raccoonsquare@gmail.com)
 */

$C = array();
$B = array();

$B['APP_DEMO'] = false;                                      //true = enable demo version mode

$B['APP_MESSAGES_COUNTERS'] = true;                         //true = show new messages counters
$B['APP_MYSQLI_EXTENSION'] = true;                          //if on the server is not installed mysqli extension, set to false
$B['FACEBOOK_AUTHORIZATION'] = false;                        //false = Do not show buttons Login/Signup with Facebook | true = allow display buttons Login/Signup with Facebook

$C['COMPANY_URL'] = "http://codecanyon.net/user/raccoonsquaredev/portfolio?ref=raccoonsquaredev";

$B['APP_PATH'] = "app";
$B['APP_VERSION'] = "1";
$B['APP_NAME'] = "Dating App";
$B['APP_TITLE'] = "Dating App";
$B['APP_VENDOR'] = "raccoonsquare.com";
$B['APP_YEAR'] = "2026";
$B['APP_AUTHOR'] = "Demyanchuk Dmitry";
$B['APP_SUPPORT_EMAIL'] = "raccoonsquare@gmail.com";
$B['APP_AUTHOR_PAGE'] = "raccoonsquaredev";

$B['APP_HOST'] = "flure.in";                 //edit to your domain, example (WARNING - without http:// and www): yourdomain.com
$B['APP_URL'] = "https://flure.in";           //edit to your domain url, example (WARNING - with http:// or https://): http://yourdomain.com

$B['TEMP_PATH'] = "tmp/";                                //don`t edit this option
$B['PHOTO_PATH'] = "photo/";                             //don`t edit this option
$B['COVER_PATH'] = "cover/";                             //don`t edit this option
$B['CHAT_IMAGE_PATH'] = "chat_images/";                  //don`t edit this option
$B['GIFTS_PATH'] = "gifts/";                             //don`t edit this option
$B['MY_PHOTOS_PATH'] = "gallery/";                       //don`t edit this option
$B['VIDEO_PATH'] = "video/";                             //don`t edit this option
$B['STICKERS_PATH'] = "stickers/";                       //don`t edit this option

$B['GOOGLE_PLAY_LINK'] = "https://play.google.com/store/apps/details?id=com.raccoonsquare.dating";

$B['CLIENT_ID'] = 1;                               //Client ID | Integer | For identify the application | Example: 12567
$B['CLIENT_SECRET'] = "wFt4*KBoNN_kjSdG13m1k3k=";        //Client Secret | String | Text value for identify the application | Example: wFt4*KBoNN_kjSdG13m1k3k=

// Stripe settings | Settings from Cross-Platform Mobile Payments | See documentation

$B['STRIPE_PUBLISHABLE_KEY'] = "pk_test_Fv4E5657xhGYzW6r";
$B['STRIPE_SECRET_KEY'] = "sk_test_hLnPfu0vdl567quCtvbK";

// Google OAuth client |

$B['GOOGLE_CLIENT_ID'] = "567567567-qqva5672it0l3t6mguoosahb.apps.googleusercontent.com";
$B['GOOGLE_CLIENT_SECRET'] = "GOCS1PX-78e_tZoi7y4hnnnbcRLDU1EtsgKXl";

// Push notifications settings | For sending FCM (Firebase Cloud Messages) | https://raccoonsquare.com/help/how_to_create_fcm_android/

$B['GOOGLE_API_KEY'] = "AAAAkHFX7b4:567567567567567567";
$B['GOOGLE_SENDER_ID'] = "567567567567";

$B['FIREBASE_API_KEY'] = $B['GOOGLE_API_KEY'];
$B['FIREBASE_SENDER_ID'] = $B['GOOGLE_SENDER_ID'];

// Firebase project id need for OTP verification

$B['FIREBASE_PROJECT_ID'] = "dating-56756756";

// Facebook settings | For login/signup with facebook | https://raccoonsquare.com/help/how_to_create_facebook_application_and_get_app_id_and_app_secret/

$B['FACEBOOK_APP_ID'] = "2343245345345";
$B['FACEBOOK_APP_SECRET'] = "cb83c2a1daa557f9b62f319bfd61c9e055";

// Recaptcha settings | Create you keys for reCAPTCHA v3 | https://www.google.com/recaptcha/admin/create

$B['RECAPTCHA_SITE_KEY'] = "6LemcXcsA567567i-0f6weOiTpTYMX3kMXz1XT";
$B['RECAPTCHA_SECRET_KEY'] = "6LemcXcs5675670ZJieTlvPWwDNi3LUlHBEH4h";

// SMTP Settings | For password recovery

$B['SMTP_HOST'] = 'mysite.com';                         //SMTP host
$B['SMTP_AUTH'] = true;                                     //SMTP auth (Enable SMTP authentication)
$B['SMTP_SECURE'] = 'ssl';                                  //SMTP secure (Enable TLS encryption, `ssl` also accepted)
$B['SMTP_PORT'] = 465;                                      //SMTP port (TCP port to connect to)
$B['SMTP_EMAIL'] = 'support@mysite.com';                     //SMTP email
$B['SMTP_USERNAME'] = 'support@mysite.com';                  //SMTP username
$B['SMTP_PASSWORD'] = 'mypassword';                      //SMTP password

//Please edit database data

$C['DB_HOST'] = "localhost";                                //localhost or your db host
$C['DB_USER'] = "u715488437_Flure_in";                             //your db user
$C['DB_PASS'] = "Sukanya123*#@!";                         //your db password
$C['DB_NAME'] = "u715488437_flure_in";                             //your db name


$C['DEFAULT_BALANCE'] = 10;                                    // Default user balance in credits (Is charged during the user registration)

$C['ERROR_SUCCESS'] = 0;

$C['ERROR_UNKNOWN'] = 100;
$C['ERROR_ACCESS_TOKEN'] = 101;

$C['ERROR_LOGIN_TAKEN'] = 300;
$C['ERROR_EMAIL_TAKEN'] = 301;
$C['ERROR_FACEBOOK_ID_TAKEN'] = 302;

$C['ERROR_ACCOUNT_ID'] = 400;

$C['DISABLE_LIKES_GCM'] = 0;
$C['ENABLE_LIKES_GCM'] = 1;

$C['DISABLE_COMMENTS_GCM'] = 0;
$C['ENABLE_COMMENTS_GCM'] = 1;

$C['DISABLE_FOLLOWERS_GCM'] = 0;
$C['ENABLE_FOLLOWERS_GCM'] = 1;

$C['DISABLE_MESSAGES_GCM'] = 0;
$C['ENABLE_MESSAGES_GCM'] = 1;

$C['DISABLE_GIFTS_GCM'] = 0;
$C['ENABLE_GIFTS_GCM'] = 1;

$C['SEX_MALE'] = 0;
$C['SEX_FEMALE'] = 1;
$C['SEX_ANY'] = 2;

$C['USER_CREATED_SUCCESSFULLY'] = 0;
$C['USER_CREATE_FAILED'] = 1;
$C['USER_ALREADY_EXISTED'] = 2;
$C['USER_BLOCKED'] = 3;
$C['USER_NOT_FOUND'] = 4;
$C['USER_LOGIN_SUCCESSFULLY'] = 5;
$C['EMPTY_DATA'] = 6;
$C['ERROR_API_KEY'] = 7;

$C['NOTIFY_TYPE_LIKE'] = 0;
$C['NOTIFY_TYPE_FOLLOWER'] = 1;
$C['NOTIFY_TYPE_MESSAGE'] = 2;
$C['NOTIFY_TYPE_COMMENT'] = 3;
$C['NOTIFY_TYPE_COMMENT_REPLY'] = 4;
$C['NOTIFY_TYPE_FRIEND_REQUEST_ACCEPTED'] = 5;
$C['NOTIFY_TYPE_GIFT'] = 6;

$C['NOTIFY_TYPE_IMAGE_COMMENT'] = 7;
$C['NOTIFY_TYPE_IMAGE_COMMENT_REPLY'] = 8;
$C['NOTIFY_TYPE_IMAGE_LIKE'] = 9;

$C['GCM_NOTIFY_CONFIG'] = 0;
$C['GCM_NOTIFY_SYSTEM'] = 1;
$C['GCM_NOTIFY_CUSTOM'] = 2;
$C['GCM_NOTIFY_LIKE'] = 3;
$C['GCM_NOTIFY_ANSWER'] = 4;
$C['GCM_NOTIFY_QUESTION'] = 5;
$C['GCM_NOTIFY_COMMENT'] = 6;
$C['GCM_NOTIFY_FOLLOWER'] = 7;
$C['GCM_NOTIFY_PERSONAL'] = 8;
$C['GCM_NOTIFY_MESSAGE'] = 9;
$C['GCM_NOTIFY_COMMENT_REPLY'] = 10;
$C['GCM_FRIEND_REQUEST_INBOX'] = 11;
$C['GCM_FRIEND_REQUEST_ACCEPTED'] = 12;
$C['GCM_NOTIFY_GIFT'] = 14;
$C['GCM_NOTIFY_SEEN'] = 15;
$C['GCM_NOTIFY_TYPING'] = 16;
$C['GCM_NOTIFY_URL'] = 17;

$C['GCM_NOTIFY_IMAGE_COMMENT_REPLY'] = 18;
$C['GCM_NOTIFY_IMAGE_COMMENT'] = 19;
$C['GCM_NOTIFY_IMAGE_LIKE'] = 20;

$C['ACCOUNT_STATE_ENABLED'] = 0;
$C['ACCOUNT_STATE_DISABLED'] = 1;
$C['ACCOUNT_STATE_BLOCKED'] = 2;
$C['ACCOUNT_STATE_DEACTIVATED'] = 3;

$C['ACCOUNT_TYPE_USER'] = 0;
$C['ACCOUNT_TYPE_GROUP'] = 1;

$C['GALLERY_ITEM_TYPE_IMAGE'] = 0;
$C['GALLERY_ITEM_TYPE_VIDEO'] = 1;

$C['ADMIN_ACCESS_LEVEL_FULL'] = 0;
$C['ADMIN_ACCESS_LEVEL_MODERATOR'] = 1;
$C['ADMIN_ACCESS_LEVEL_GUEST'] = 2;

$LANGS = array();
$LANGS['English'] = "en";
$LANGS['Русский'] = "ru";

