<?php

    /*!
     * https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2024 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    include('sys/config/gconfig.inc.php');

    $result = array();

    if (isset($_GET["code"]))
    {
        //It will Attempt to exchange a code for an valid authentication token.
        $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

        //This condition will check there is any error occur during geting authentication token. If there is no any error occur then it will execute if block of code/
        if (!isset($token['error']))
        {
            //Set the access token used for requests
            $google_client->setAccessToken($token['access_token']);

            //Create Object of Google Service OAuth 2 class
            $google_service = new Google_Service_Oauth2($google_client);

            //Get user profile data from google
            $data = $google_service->userinfo->get();

            $google_client->revokeToken();

            //Below you can find Get profile data and store into $_SESSION variable

            $helper = new helper($dbo);
            $account_id = $helper->getUserIdByGoogle($data['id']);

            if (auth::getCurrentUserId() != 0) {

                if ($account_id != 0) {

                    header("Location: /account/settings/services?status=g_error");
                    exit;

                } else {

                    $account = new account($dbo, auth::getCurrentUserId());
                    $account->setGoogleFirebaseId($data['id']);
                    unset($account);

                    header("Location: /account/settings/services?status=g_connected");
                    exit;
                }

            } else {


                if ($account_id == 0) {

                    // Auto signup

                    $oauth_name = "";

                    if (!empty($data['given_name']))
                    {

                        $oauth_name = $data['given_name'];
                    }

                    if (!empty($data['family_name']))
                    {

                        $oauth_name = $oauth_name." ".$data['family_name'];
                    }

                    $oauth_email = "";

                    if (!empty($data['email']))
                    {

                        $oauth_email = $data['email'];
                    }

                    $account = new account($dbo);
                    $account_info = $account->signupOauth(OAUTH_TYPE_GOOGLE, $data['id'], $oauth_name, $oauth_email);
                    unset($account);

                    if (!$account_info['error']) {

                        $account_id = $account_info['accountId'];

                        if (!empty($data['picture'])) {

                            $time = time();

                            $imgFilename = "tmp/{$time}_{$account_id}.jpg";

                            @file_put_contents($imgFilename, file_get_contents($data['picture']));

                            $cdn = new cdn($dbo);
                            $response = $cdn->uploadPhoto($imgFilename);

                            if (!$response['error']) {

                                $pic_result['normalPhotoUrl'] = $response['fileUrl'];
                                $pic_result['originPhotoUrl'] = $response['fileUrl'];
                                $pic_result['bigPhotoUrl'] = $response['fileUrl'];
                                $pic_result['lowPhotoUrl'] = $response['fileUrl'];

                                $acc = new account($dbo, $account_id);
                                $acc->setPhoto($pic_result);
                                unset($acc);
                            }

                            unset($cdn);
                        }
                    }
                }

                if ($account_id != 0) {

                    $account = new account($dbo, $account_id);
                    $account_info = $account->get();

                    if ($account_info['state'] == ACCOUNT_STATE_ENABLED) {

                        $auth = new auth($dbo);
                        $result = $auth->create($account_id, CLIENT_ID, APP_TYPE_WEB);

                        if (!$result['error']) {

                            $account->setLastActive();

                            auth::setSession($result['accountId'], $account_info['username'], $account_info['fullname'], $account_info['lowPhotoUrl'], $account_info['verified'], $account_info['balance'], $account_info['pro'], $account_info['free_messages_count'], 0, $result['accessToken']);
                            auth::setCurrentUserAdmobFeature($account_info['admob']);
                            auth::setCurrentUserGhostFeature($account_info['ghost']);
                            //auth::setCurrentUserOtpVerified($account_info['otpVerified']);
                            auth::updateCookie($account_info['username'], $result['accessToken']);

                            auth::setRegistrationComplete($account_info['registrationComplete']);

                            header("Location: /");
                            exit;
                        }
                    }

                    unset($account);
                    unset($account_info);
                }
            }

        } else {

            header("Location: /");
            exit;
        }

    } else {

        if (isset($_SESSION['oauth'])) {

            unset($_SESSION['oauth']);
            unset($_SESSION['oauth_id']);
            unset($_SESSION['oauth_name']);
            unset($_SESSION['oauth_email']);
            unset($_SESSION['oauth_link']);
            unset($_SESSION['oauth_img_link']);

            header("Location: /signup");
            exit;

        } else {

            header("Location: /");
            exit;
        }
    }