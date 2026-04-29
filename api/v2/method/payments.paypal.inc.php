<?php

    /*!
     * https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2025 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */;

    if (!defined("APP_SIGNATURE")) {

        header("Location: /");
        exit;
    }

    //

    $settings = new settings($dbo);
    $settings_result = $settings->get();

    $api_url = "https://api-m.sandbox.paypal.com";

    if ($settings_result['paypal_secret_key']['intValue']) {

        $api_url = "https://api-m.paypal.com";
    }

    //

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $orderID = isset($_POST['orderId']) ? $_POST['orderId'] : '';

    $orderID = helper::clearText($orderID);
    $orderID = helper::escapeText($orderID);

    //

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    //

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url."/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSLVERSION , 6); //NEW ADDITION
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $settings_result['paypal_client_id']['textValue'].":".$settings_result['paypal_secret_key']['textValue']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    $result = curl_exec($ch);

    if (empty($result))die("Error: No response."); else {

        $json = json_decode($result);
        $accessToken = $json->access_token;

        if (empty($orderID)) {

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $api_url.'/v2/checkout/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(
                array (
                    "intent" => "CAPTURE",
                    "purchase_units" => array (
                        array(
                            "amount" => array (
                                "currency_code" => $settings_result['paypal_currency']['textValue'],
                                "value" => "{$settings_result['paypal_price']['intValue']}.00"
                            )
                        )
                    )
                )
            ));

            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: Bearer '.$accessToken;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);

            if (curl_errno($ch)) {

                echo 'Error:' . curl_error($ch);
            }

            curl_close($ch);

            $response = json_decode($result, true);

            echo json_encode($response);
            exit;

        } else {

            $url = $api_url."/v2/checkout/orders/{$orderID}/capture";
            $requestId = uniqid();
            $uniqueOrderID = $orderID; // This should ideally come from your order system.
            $paypalClientMetadataId = time() . "-" . $uniqueOrderID;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSLVERSION => 6,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(

                    'Content-Type: application/json',
                    'Authorization: Bearer '.$accessToken,
                    'PayPal-Request-Id: '.$requestId,
                    'Prefer: return=representation',
                    'PayPal-Client-Metadata-Id: '.$paypalClientMetadataId
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);


            if ($response === false) {

                http_response_code(500);
                echo json_encode(['error' => 'Failed to capture order. No response data from PayPal.']);
                exit;
            }

            $responseData = json_decode($response, true);

            if (!$responseData) {

                http_response_code(500);
                echo json_encode(['error' => 'Failed to capture order. Invalid JSON response.']);
                exit;
            }

            if (isset($responseData['id']) && isset($responseData['status']) && $responseData['status'] == 'COMPLETED') {

                echo json_encode(['success' => true, 'id' => $responseData['id'], 'status' => $responseData['status']]);

            } else {

                http_response_code(500);
                echo json_encode(['error' => 'Failed to capture order.', 'details' => $responseData]);
            }
        }

    }
