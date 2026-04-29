<?php

/*!
 * https://raccoonsquare.com
 */

if (!defined("APP_SIGNATURE")) {

    header("Location: /");
    exit;
}

if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $result = array(
        "error" => false,
        "error_code" => ERROR_SUCCESS
    );

    $notifications_count = 0;
    $messages_count = 0;
    $matches_count = 0;
    $guests_count = 0;
    $friends_count = 0;

    $registrationComplete = 0;

    if ($accountId != 0) {

        $account = new account($dbo, $accountId);
        $accountInfo = $account->get();
        unset($account);

        $registrationComplete = isset($accountInfo['registrationComplete']) ? (int) $accountInfo['registrationComplete'] : 0;

        // Messages
        if (APP_MESSAGES_COUNTERS) {

            $msg = new msg($dbo);
            $msg->setRequestFrom($accountId);
            $messages_count = $msg->getNewMessagesCount();
            unset($msg);
        }

        // Notifications
        $notifications = new notify($dbo);
        $notifications->setRequestFrom($accountId);
        $notifications_count = $notifications->getNewCount($accountInfo['lastNotifyView']);
        unset($notifications);

        // Matches
        $matches = new matches($dbo, $accountId);
        $matches->setRequestFrom($accountId);
        $matches_count = $matches->getNewCount($accountInfo['lastMatchesView']);
        unset($matches);

        // Guests
        $guests = new guests($dbo, $accountId);
        $guests->setRequestFrom($accountId);
        $guests_count = $guests->getNewCount($accountInfo['lastGuestsView']);
        unset($guests);

        // Friends
        $friends = new friends($dbo, $accountId);
        $friends->setRequestFrom($accountId);
        $friends_count = $friends->getNewCount($accountInfo['lastFriendsView']);
        unset($friends);

        // Account values
        $result['free_messages_count'] = $accountInfo['free_messages_count'];
        $result['admob'] = $accountInfo['admob'];
        $result['ghost'] = $accountInfo['ghost'];
        $result['pro'] = $accountInfo['pro'];
        $result['verified'] = $accountInfo['verified'];
        $result['balance'] = $accountInfo['balance'];
        $result['lowPhotoUrl'] = $accountInfo['lowPhotoUrl'];
    }

    // Counters
    $result['guestsCount'] = $guests_count;
    $result['messagesCount'] = $messages_count;
    $result['notificationsCount'] = $notifications_count;
    $result['newFriendsCount'] = $friends_count;
    $result['newMatchesCount'] = $matches_count;

    $result['registrationComplete'] = $registrationComplete;

    // Settings
    $settings = new settings($dbo);
    $config = $settings->get();

    $result['seenTyping'] = $config['allowSeenTyping']['intValue'];
    $result['rewardedAds'] = $config['allowRewardedAds']['intValue'];
    $result['allowAdBannerInGalleryItem'] = $config['allowAdBannerInGalleryItem']['intValue'];
    $result['defaultGhostModeCost'] = $config['defaultGhostModeCost']['intValue'];
    $result['defaultVerifiedBadgeCost'] = $config['defaultVerifiedBadgeCost']['intValue'];
    $result['defaultDisableAdsCost'] = $config['defaultDisableAdsCost']['intValue'];
    $result['defaultProModeCost'] = $config['defaultProModeCost']['intValue'];
    $result['defaultSpotlightCost'] = $config['defaultSpotlightCost']['intValue'];
    $result['defaultMessagesPackageCost'] = $config['defaultMessagesPackageCost']['intValue'];
    $result['allowShowNotModeratedProfilePhotos'] = $config['allowShowNotModeratedProfilePhotos']['intValue'];

    // Ads
    $result['interstitialAdAfterProfileView'] = $config['interstitialAdAfterProfileView']['intValue'];
    $result['interstitialAdAfterNewGalleryItem'] = $config['interstitialAdAfterNewGalleryItem']['intValue'];
    $result['interstitialAdAfterNewProfileLike'] = $config['interstitialAdAfterNewProfileLike']['intValue'];
    $result['interstitialAdAfterNewLike'] = $config['interstitialAdAfterNewLike']['intValue'];
    $result['interstitialAdAfterNewComment'] = $config['interstitialAdAfterNewComment']['intValue'];

    // Android Ads
    $result['android_admob_app_id'] = $config['android_admob_app_id']['textValue'];
    $result['android_admob_banner_ad_unit_id'] = $config['android_admob_banner_ad_unit_id']['textValue'];
    $result['android_admob_rewarded_ad_unit_id'] = $config['android_admob_rewarded_ad_unit_id']['textValue'];
    $result['android_admob_interstitial_ad_unit_id'] = $config['android_admob_interstitial_ad_unit_id']['textValue'];
    $result['android_admob_banner_native_ad_unit_id'] = $config['android_admob_banner_native_ad_unit_id']['textValue'];

    // iOS Ads
    $result['ios_admob_app_id'] = $config['ios_admob_app_id']['textValue'];
    $result['ios_admob_banner_ad_unit_id'] = $config['ios_admob_banner_ad_unit_id']['textValue'];
    $result['ios_admob_rewarded_ad_unit_id'] = $config['ios_admob_rewarded_ad_unit_id']['textValue'];
    $result['ios_admob_interstitial_ad_unit_id'] = $config['ios_admob_interstitial_ad_unit_id']['textValue'];
    $result['ios_admob_banner_native_ad_unit_id'] = $config['ios_admob_banner_native_ad_unit_id']['textValue'];

    // Agora
    $result['agora_app_enabled'] = $config['agora_app_enabled']['intValue'];
    $result['agora_app_id'] = $config['agora_app_id']['textValue'];
    $result['agora_app_certificate'] = $config['agora_app_certificate']['textValue'];

    // Phone login
    $result['pl_enabled'] = $config['pl_enabled']['intValue'];

    $phone = new phone($dbo);
    $c_list = $phone->c_getList(0);
    unset($phone);

    $result['c_list'] = $c_list['items'];

    // PayPal
    $result['paypal_enabled'] = $config['paypal_enabled']['intValue'];
    $result['paypal_client_id'] = $config['paypal_client_id']['textValue'];
    $result['paypal_mode'] = $config['paypal_mode']['textValue'];
    $result['paypal_currency'] = $config['paypal_currency']['textValue'];
    $result['paypal_count'] = $config['paypal_count']['intValue'];
    $result['paypal_price'] = $config['paypal_price']['intValue'];

    echo json_encode($result);
    exit;
}
