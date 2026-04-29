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

        header("Location: /account/find");
    }

    include_once('sys/config/gconfig.inc.php');
    require_once 'sys/addons/vendor/autoload.php';

    $user_username = '';

    $error = false;
    $error_message = array();

    if (!empty($_POST)) {

        $user_username = isset($_POST['user_username']) ? $_POST['user_username'] : '';
        $user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
        $recaptcha_token = isset($_POST['recaptcha_token']) ? $_POST['recaptcha_token'] : '';

        $user_username = helper::clearText($user_username);
        $user_password = helper::clearText($user_password);

        $user_username = helper::escapeText($user_username);
        $user_password = helper::escapeText($user_password);

        // Google Recaptcha

        if (GOOGLE_RECAPTCHA_WEB) {

            $recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_SECRET_KEY);
            $resp = $recaptcha->verify($recaptcha_token, $_SERVER['REMOTE_ADDR']);

            if (!$resp->isSuccess()){

                $error = true;
                $error_message[] = "Google Recaptcha error";
            }
        }

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if (!$error) {

            $access_data = array();

            $account = new account($dbo);

            $access_data = $account->signin($user_username, $user_password);

            unset($account);

            if (!$access_data['error']) {

                $account = new account($dbo, $access_data['accountId']);
                $accountInfo = $account->get();

                $account_fullname = $accountInfo['fullname'];
                $account_photo_url = $accountInfo['lowPhotoUrl'];
                $account_verified = $accountInfo['verified'];
                $account_balance = $accountInfo['balance'];
                $account_pro_mode = $accountInfo['pro'];
                $account_free_messages_count = $accountInfo['free_messages_count'];

                //print_r($accountInfo);

                switch ($accountInfo['state']) {

                    case ACCOUNT_STATE_BLOCKED: {

                        break;
                    }

                    default: {

                        $account->setState(ACCOUNT_STATE_ENABLED);

                        $clientId = 0; // Desktop version

                        $auth = new auth($dbo);
                        $access_data = $auth->create($accountInfo['id'], $clientId, APP_TYPE_WEB, "", $LANG['lang-code']);

                        if (!$access_data['error']) {

                            auth::setSession($access_data['accountId'], $user_username, $account_fullname, $account_photo_url, $account_verified, $account_balance, $account_pro_mode, $account_free_messages_count, $account->getAccessLevel($access_data['accountId']), $access_data['accessToken']);
                            auth::setCurrentUserAdmobFeature($accountInfo['admob']);
                            auth::setCurrentUserGhostFeature($accountInfo['ghost']);
                            auth::updateCookie($user_username, $access_data['accessToken']);

                            auth::setRegistrationComplete($accountInfo['registrationComplete']);

                            unset($_SESSION['oauth']);
                            unset($_SESSION['oauth_id']);
                            unset($_SESSION['oauth_name']);
                            unset($_SESSION['oauth_email']);
                            unset($_SESSION['oauth_link']);

                            $account->setLastActive();

                            header("Location: /");
                        }
                    }
                }

            } else {

                $error = true;
            }
        }
    }

    auth::newAuthenticityToken();

    $page_id = "main";

    $css_files = array("rje-landing.css?x12");
    $page_title = APP_TITLE;

    include_once("html/common/site_header.inc.php");

?>

<body class="rje-body">

    <div class="rje-auth">

        <div class="rje-auth__left">
            <div class="rje-illustration">
                <div class="rje-illustration__content">
                    <h2 class="rje-illustration__title">My perfect match. Dating.</h2>
                    <p class="rje-illustration__text">
                        <?php echo $LANG['main-page-prompt-login']; ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="rje-auth__right">

            <div>

                <div class="rje-card">

                    <div class="mb-5" style="display: flex; justify-content: center;">
                        <a href="/" class="">
                            <span class="rje-logo"></span>
                        </a>
                    </div>

                    <h1 class="rje-title"><?php echo $LANG['page-login']; ?></h1>

                    <?php

                    if (GOOGLE_AUTHORIZATION) {

                        ?>

                        <p>
                            <a class="gl-icon-btn gl-btn-large btn-social btn-google" href="<?php echo $google_client->createAuthUrl(); ?>"><span class="icon-container"><i class="icon icon-google"></i></span>
                                <span><?php echo $LANG['action-signin-google']; ?></span>
                            </a>
                        </p>
                        <?php
                    }

                    $settings = new settings($dbo);
                    $settingsList = $settings->get();
                    unset($settings);

                    if ($settingsList['pl_enabled']['intValue'] == 1) {

                        ?>
                        <p>
                            <a class="phone-icon-btn phone-btn-large btn-social btn-phone" href="/phone"><span class="icon-container"><i class="icon icon-phone"></i></span>
                                <span><?php echo $LANG['action-signin-phone']; ?></span>
                            </a>
                        </p>
                        <?php
                    }

                    ?>

                    <div class="rje-divider">
                        <span>or</span>
                    </div>

                    <form class="rje-form" accept-charset="UTF-8" action="/" id="rje-form" method="post">

                        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                        <div class="alert alert-danger" style="<?php if (!$error) echo "display: none"; ?>">
                            <span class="title"><?php echo $LANG['label-errors-title']; ?></span>
                            <ul>
                                <li><?php echo $LANG['msg-error-authorize']; ?></li>
                            </ul>
                        </div>

                        <div class="rje-field">
                            <label class="rje-label"><?php echo $LANG['label-username']; ?></label>
                            <input class="rje-input" name="user_username" placeholder="<?php echo $LANG['label-username']; ?>" required="required" size="30" type="text" value="<?php echo $user_username; ?>" />
                        </div>

                        <div class="rje-field">
                            <label class="rje-label"><?php echo $LANG['label-password']; ?></label>
                            <input class="rje-input" name="user_password" placeholder="<?php echo $LANG['label-password']; ?>" required="required" size="30" type="password" value="" />
                        </div>

                        <div class="rje-row">
                            <a href="/remind" class="rje-link"><?php echo $LANG['action-forgot-password']; ?></a>
                        </div>

                        <button type="submit" class="rje-btn primary button"><?php echo $LANG['action-login']; ?></button>

                    </form>

                    <p class="rje-footer">
                        Don’t have an account? <a href="/signup" class="rje-link rje-link--strong"><?php echo $LANG['action-signup']; ?></a>
                    </p>

                    <?php

                        if (strlen(GOOGLE_PLAY_LINK) != 0) {

                        ?>

                            <div class="rje-divider"></div>

                            <p class="rje-footer">
                                <?php echo sprintf($LANG['label-prompt-app'], APP_TITLE, APP_TITLE) ?>

                                <a class="d-block" href="<?php echo GOOGLE_PLAY_LINK; ?>" target="_blank" rel="nofollow">
                                    <img class="mt-4" width="170" src="/img/google_play.png">
                                </a>

                            </p>

                        <?php

                        }
                    ?>

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