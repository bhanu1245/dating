case IMAGE_TYPE_PROFILE_PHOTO: {

    $result = $imglib->newProfilePhoto($new_file_name);

    if (!$result['error']) {

        // Delete old photos
        $profile = new profile($dbo, $accountId);
        $profileInfo = $profile->getVeryShort();
        unset($profile);

        @unlink(PHOTO_PATH."/".basename($profileInfo['normalPhotoUrl']));
        @unlink(PHOTO_PATH."/".basename($profileInfo['bigPhotoUrl']));
        @unlink(PHOTO_PATH."/".basename($profileInfo['lowPhotoUrl']));
        unset($profileInfo);

        // Set new photos
        $account = new account($dbo, $accountId);
        $account->setPhoto($result);
        unset($account);

        // 🔥 FORCE UPDATE (NO PREPARE)
        $dbo->query("UPDATE users SET registrationComplete = 1 WHERE id = {$accountId}");

        error_log("✅ registrationComplete updated for user: " . $accountId);

        // Moderator
        $moderator = new moderator($dbo);
        $moderator->postPhoto($accountId, $result['originPhotoUrl']);
        unset($moderator);

        $settings = new settings($dbo);

        if ($settings->getIntValue("allowAutoModerate") == 1) {

            $moderator = new moderator($dbo);
            $moderator->approvePhoto($accountId);
            unset($moderator);
        }

        unset($settings);

        if (auth::isSession()) {
            auth::setCurrentUserPhotoUrl($result['normalPhotoUrl']);
        }
    }

    break;
}