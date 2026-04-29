<?php

    /*!
     * https://raccoonsquare.com, https://raccoonjohn.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2026 Demianchuk Dmytro and Raccoon John (raccoonsquare@gmail.com)
     */

    if (!defined("APP_SIGNATURE")) {

        header("Location: /");
        exit;
    }

    if (auth::isSession()) {

        header("Location: /");
        exit;
    }

    include_once('sys/config/gconfig.inc.php');
    require_once 'sys/addons/vendor/autoload.php';

    $user_username = '';
    $user_email = '';
    $user_fullname = '';
    $gender = 2;
    $age = 0;
    $sex_orientation = 0;
    $user_referrer = 0;

    $error = false;
    $error_message = array();

    if (!empty($_POST)) {

        $error = false;

        $user_username = isset($_POST['username']) ? $_POST['username'] : '';
        $user_fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
        $user_password = isset($_POST['password']) ? $_POST['password'] : '';
        $user_email = isset($_POST['email']) ? $_POST['email'] : '';
        $user_referrer = isset($_POST['referrer']) ? $_POST['referrer'] : 0;
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
        $recaptcha_token = isset($_POST['recaptcha_token']) ? $_POST['recaptcha_token'] : '';

        $gender = isset($_POST['gender']) ? $_POST['gender'] : 2;
        $age = isset($_POST['age']) ? $_POST['age'] : 0;
        $sex_orientation = isset($_POST['sex_orientation']) ? $_POST['sex_orientation'] : 0;

        $user_referrer = helper::clearInt($user_referrer);

        $user_username = helper::clearText($user_username);
        $user_fullname = helper::clearText($user_fullname);
        $user_password = helper::clearText($user_password);
        $user_email = helper::clearText($user_email);

        $user_username = helper::escapeText($user_username);
        $user_fullname = helper::escapeText($user_fullname);
        $user_password = helper::escapeText($user_password);
        $user_email = helper::escapeText($user_email);

        $gender = helper::clearInt($gender);
        $age = helper::clearInt($age);
        $sex_orientation = helper::clearInt($sex_orientation);

        // Google Recaptcha

        if (GOOGLE_RECAPTCHA_WEB) {

            $recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_SECRET_KEY);
            $resp = $recaptcha->verify($recaptcha_token, $_SERVER['REMOTE_ADDR']);

            if (!$resp->isSuccess()) {

                $error = true;
                $error_message[] = "Google Recaptcha error";
            }
        }

        //

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
            $error_token = true;
            $error_message[] = $LANG['msg-error-unknown'];
        }

        if (!helper::isCorrectLogin($user_username)) {

            $error = true;
            $error_username = true;
            $error_message[] = $LANG['msg-login-incorrect'];
        }

        if ($helper->isLoginExists($user_username)) {

            $error = true;
            $error_username = true;
            $error_message[] = $LANG['msg-login-taken'];
        }

        if (!helper::isCorrectFullname($user_fullname)) {

            $error = true;
            $error_fullname = true;
            $error_message[] = $LANG['msg-fullname-incorrect'];
        }

        if (!helper::isCorrectPassword($user_password)) {

            $error = true;
            $error_password = true;
            $error_message[] = $LANG['msg-password-incorrect'];
        }

        if (!helper::isCorrectEmail($user_email)) {

            $error = true;
            $error_email = true;
            $error_message[] = $LANG['msg-email-incorrect'];
        }

        if ($helper->isEmailExists($user_email)) {

            $error = true;
            $error_email = true;
            $error_message[] = $LANG['msg-email-taken'];
        }

        if ($age > 110 || $age < 18) {

            $error = true;
            $error_email = true;
            $error_message[] = $LANG['msg-age-incorrect'];
        }

        if ($gender > 2 || $gender < 0) {

            // 0 = male
            // 1 = female
            // 2 = secret

            $error = true;
            $error_email = true;
            $error_message[] = $LANG['msg-gender-incorrect'];
        }

        if ($sex_orientation > 4 || $sex_orientation < 1) {

            // 0 = undefined

            $error = true;
            $error_email = true;
            $error_message[] = $LANG['msg-sex-orientation-incorrect'];
        }

        if (!$error) {

            $account = new account($dbo);

            $result = array();
            $result = $account->signup($user_username, $user_fullname, $user_password, $user_email, $gender, 2000, 1, 1, $age, $sex_orientation, $LANG['lang-code'], 0);

            if (!$result['error']) {

                $clientId = 0; // Desktop version

                $auth = new auth($dbo);
                $access_data = $auth->create($result['accountId'], $clientId, APP_TYPE_WEB, "", $LANG['lang-code']);

                if (!$access_data['error']) {

                    auth::setSession($result['accountId'], $result['username'], $result['fullname'], "", 0, $result['balance'], 0, $result['free_messages_count'], 0, $access_data['accessToken']);
                    auth::updateCookie($user_username, $access_data['accessToken']);

                    $language = $account->getLanguage();

                    $account->setLastActive();

                    // refsys

                    if ($user_referrer != 0) {

                        $settings = new settings($dbo);
                        $app_settings = $settings->get();
                        unset($settings);

                        $ref = new refsys($dbo);
                        $ref->setRequestFrom($account->getId());
                        $ref->setBonus($app_settings['defaultReferralBonus']['intValue']);
                        $ref->setReferrer($user_referrer);

                        unset($ref);
                    }

                    //Facebook connect

                    if (isset($_SESSION['oauth'])) {

                        switch ($_SESSION['oauth']) {

                            case 'facebook': {


                                break;
                            }

                            default: {

                                // google signin

                                if ($helper->getUserIdByGoogle($_SESSION['oauth_id']) == 0) {

                                    $account->setGoogleFirebaseId($_SESSION['oauth_id']);
                                }
                            }
                        }
                    }

                    unset($_SESSION['oauth']);
                    unset($_SESSION['oauth_id']);
                    unset($_SESSION['oauth_name']);
                    unset($_SESSION['oauth_email']);
                    unset($_SESSION['oauth_link']);

                    $_SESSION['welcome_block'] = true;

                    auth::closeAuthenticityToken();

                    header("Location: /".$user_username);
                    exit;

                } else {

                    auth::closeAuthenticityToken();
                }

            } else {

                $error = true;
                $error_message[] = "You can not create multi-accounts!";

                auth::closeAuthenticityToken();
            }
        }
    }

    if (isset($_SESSION['oauth']) && empty($user_username) && empty($user_email)) {

        $user_fullname = $_SESSION['oauth_name'];
        $user_email = $_SESSION['oauth_email'];
    }

    auth::newAuthenticityToken();

    $page_id = "signup";

    $css_files = array("rje-landing.css?x12");
    $page_title = $LANG['page-signup']." | ".APP_TITLE;

    include_once("html/common/site_header.inc.php");

?>

<body class="rje-body">

    <div class="rje-auth">

        <div class="rje-auth__left">
            <div class="rje-illustration">
                <div class="rje-illustration__content">
                    <h2 class="rje-illustration__title"><?php echo APP_NAME; ?></h2>
                    <p class="rje-illustration__text">
                        <?php echo sprintf($LANG['main-page-prompt-signup'], "<strong>".APP_TITLE."</strong>"); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="rje-auth__right">

            <div>

                <div class="rje-card">

                    <div class="text-center mb-5">
                        <a href="/" class="">
                            <span class="rje-logo"></span>
                        </a>
                    </div>

                    <h1 class="rje-title"><?php echo $LANG['page-signup']; ?></h1>

                    <?php

                    if (GOOGLE_AUTHORIZATION) {

                        ?>

                        <a class="mb-2 gl-icon-btn gl-btn-large btn-social btn-google" href="<?php echo $google_client->createAuthUrl(); ?>"><span class="icon-container"><i class="icon icon-google"></i></span>
                            <span><?php echo $LANG['action-signin-google']; ?></span>
                        </a>
                        <?php
                    }

                    $settings = new settings($dbo);
                    $settingsList = $settings->get();
                    unset($settings);

                    if ($settingsList['pl_enabled']['intValue'] == 1) {

                        ?>
                        <a class="mb-2 phone-icon-btn phone-btn-large btn-social btn-phone" href="/phone"><span class="icon-container"><i class="icon icon-phone"></i></span>
                            <span><?php echo $LANG['action-signin-phone']; ?></span>
                        </a>
                        <?php
                    }

                    ?>

                    <div class="rje-divider">
                        <span>or</span>
                    </div>

                    <form class="rje-form" accept-charset="UTF-8" action="/signup" id="rje-form" method="post">

                        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                        <div class="rje-field alert alert-danger" style="<?php if (!$error) echo "display: none"; ?>">
                            <p class="title"><?php echo $LANG['label-errors-title']; ?></p>
                            <ul>
                                <?php

                                foreach ($error_message as $key => $value) {

                                    echo "<li>{$value}</li>";
                                }
                                ?>
                            </ul>
                        </div>

                        <div class="rje-field">
                            <label class="rje-label"><?php echo $LANG['label-username']; ?></label>
                            <input class="rje-input" name="username" placeholder="<?php echo $LANG['label-username']; ?>" required="required" size="30" type="text" value="<?php echo $user_username; ?>" />
                        </div>

                        <div class="rje-field">
                            <label class="rje-label"><?php echo $LANG['label-fullname']; ?></label>
                            <input class="rje-input" name="fullname" placeholder="<?php echo $LANG['label-fullname']; ?>" required="required" size="30" type="text" value="<?php echo $user_fullname; ?>" />
                        </div>

                        <div class="rje-field">
                            <label class="rje-label"><?php echo $LANG['label-password']; ?></label>
                            <input class="rje-input" name="password" placeholder="<?php echo $LANG['label-password']; ?>" required="required" size="30" type="password" value="" />
                        </div>

                        <div class="rje-field">
                            <label class="rje-label"><?php echo $LANG['label-email']; ?></label>
                            <input class="rje-input" name="email" placeholder="<?php echo $LANG['label-email']; ?>" required="required" size="30" type="email" value="<?php echo $user_email; ?>" />
                        </div>

                        <div class="rje-field">
                            <select name="age" id="age" style="">

                                <option disabled value="0" <?php if ($age < 18) echo "selected=\"selected\""; ?>><?php echo $LANG['label-select-age']; ?></option>

                                <?php

                                for ($i = 18; $i <= 110; $i++) {

                                    if ($i == $age) {

                                        echo "<option value=\"$i\" selected=\"selected\">$i</option>";

                                    } else {

                                        echo "<option value=\"$i\">$i</option>";
                                    }
                                }
                                ?>

                            </select>
                        </div>

                        <div class="rje-field">
                            <select name="gender" id="gender" style="">
                                <option value="2" <?php if ($gender != SEX_FEMALE && $gender != SEX_MALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-secret']; ?></option>
                                <option value="0" <?php if ($gender == SEX_MALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-male']; ?></option>
                                <option value="1" <?php if ($gender == SEX_FEMALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-female']; ?></option>
                            </select>
                        </div>

                        <div class="rje-field">
                            <select name="sex_orientation" id="sex_orientation" style="">
                                <option disabled value="0" <?php if ($sex_orientation == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['label-select-sex-orientation']; ?></option>
                                <option value="1" <?php if ($sex_orientation == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['sex-orientation-1']; ?></option>
                                <option value="2" <?php if ($sex_orientation == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['sex-orientation-2']; ?></option>
                                <option value="3" <?php if ($sex_orientation == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['sex-orientation-3']; ?></option>
                                <option value="4" <?php if ($sex_orientation == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['sex-orientation-4']; ?></option>
                            </select>
                        </div>

                        <div class="rje-field">
                            <label class="rje-label"><?php echo $LANG['label-signup-invite']; ?></label>
                            <input class="rje-input" id="referrer" name="referrer" placeholder="<?php echo $LANG['label-user-id']; ?>" size="8" type="number" value="<?php echo $user_referrer; ?>" />
                        </div>

                        <div class="rje-footer rje-field">
                            <label for="user_receive_digest">
                                <b><?php echo $LANG['label-signup-confirm']; ?></b>
                                <a href="/terms"><?php echo $LANG['page-terms']; ?></a>
                            </label>
                        </div>

                        <button type="submit" class="rje-btn primary button"><?php echo $LANG['action-signup']; ?></button>

                    </form>

                    <p class="rje-footer">
                        You have an account? <a href="/" class="rje-link rje-link--strong"><?php echo $LANG['action-login']; ?></a>
                    </p>

                </div>

                <script type="text/javascript" src="/js/jquery-3.3.1.min.js"></script>

                <?php

                    include_once("html/common/site_footer.inc.php");
                ?>
            </div>

        </div>

    </div>

    <script>

        $('#rje-form').submit(function(event) {

            if (constants.GOOGLE_RECAPTCHA_WEB) {

                event.preventDefault();

                grecaptcha.ready(function() {
                    grecaptcha.execute('<?php echo RECAPTCHA_SITE_KEY; ?>', {action: 'submit'}).then(function(token) {

                        $('#rje-form').prepend('<input type="hidden" name="recaptcha_token" value="'+ token + '">');
                        $('#rje-form').unbind('submit').submit();
                    });
                });
            }
        });
    </script>

</body>
</html>