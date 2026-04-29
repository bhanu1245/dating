<?php

    /*!
     * https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2024 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */


    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
        exit;
    }

    require_once 'sys/addons/vendor/autoload.php';

    $accountId = auth::getCurrentUserId();

    $error = false;

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
        $recaptcha_token = isset($_POST['recaptcha_token']) ? $_POST['recaptcha_token'] : '';

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
            $error_message[] = "Error. Try again later...";
        }

        // Google Recaptcha

        if (GOOGLE_RECAPTCHA_WEB) {

            $recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_SECRET_KEY);
            $resp = $recaptcha->verify($recaptcha_token, $_SERVER['REMOTE_ADDR']);

            if (!$resp->isSuccess()){

                $error = true;
                $error_message[] = "Google Recaptcha error";
            }
        }

        if (!$error) {

            // Deactivate Account

            $account = new account($dbo, $accountId);

            $result = $account->deactivation();

            if (!$result['error']) {

                header("Location: /account/logout?access_token=".auth::getAccessToken());
                exit;
            }
        }

        header("Location: /account/settings/deactivation?error=true");
        exit;
    }

    auth::newAuthenticityToken();

    $page_id = "settings_deactivation";

    $css_files = array("main.css", "my.css");
    $page_title = $LANG['page-profile-deactivation']." | ".APP_TITLE;

    include_once("html/common/site_header.inc.php");

?>

<body class="settings-page">

    <?php

        include_once("html/common/site_topbar.inc.php");
    ?>

    <div class="wrap content-page">

        <div class="main-column row">

            <?php

                include_once("html/common/site_sidenav.inc.php");
            ?>

            <div class="col-lg-9 col-md-12" id="content">

                <div class="main-content">

                    <div class="standard-page">

                        <h1><?php echo $LANG['page-profile-deactivation']; ?></h1>

                        <form accept-charset="UTF-8" action="/account/settings/deactivation" autocomplete="off" class="edit_user" id="settings-form" method="post">

                            <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">

                            <div class="tabbed-content m-0">

                                <div class="tab-container">
                                    <nav class="tabs">
                                        <a href="/account/settings"><span class="tab"><?php echo $LANG['page-profile-settings']; ?></span></a>
                                        <a href="/account/settings/privacy"><span class="tab"><?php echo $LANG['page-privacy-settings']; ?></span></a>
                                        <a href="/account/balance"><span class="tab"><?php echo $LANG['page-balance']; ?></span></a>
                                        <a href="/account/settings/services"><span class="tab"><?php echo $LANG['label-services']; ?></span></a>
                                        <a href="/account/settings/password"><span class="tab"><?php echo $LANG['label-password']; ?></span></a>
                                        <a href="/account/settings/referrals"><span class="tab"><?php echo $LANG['page-referrals']; ?></span></a>
                                        <a href="/account/settings/blacklist"><span class="tab"><?php echo $LANG['page-blacklist']; ?></span></a>
                                        <a href="/account/settings/otp"><span class="tab"><?php echo $LANG['page-otp']; ?></span></a>
                                        <a href="/account/settings/deactivation"><span class="tab active"><?php echo $LANG['page-deactivate-account']; ?></span></a>
                                    </nav>
                                </div>

                                <div class="alert alert-warning mt-3">
                                    <ul>
                                        <?php echo $LANG['page-profile-deactivation-sub-title']; ?>
                                    </ul>
                                </div>

                                <?php

                                    if ( isset($_GET['error']) ) {

                                        ?>

                                        <div class="alert alert-danger" style="margin-top: 15px;">
                                            <ul>
                                                <?php echo $LANG['msg-error-deactivation']; ?>
                                            </ul>
                                        </div>

                                        <?php
                                    }
                                ?>

                            </div>

                            <input name="commit" class="button primary mt-3" type="submit" value="<?php echo $LANG['action-deactivation-profile']; ?>">

                        </form>
                    </div>


                </div>
            </div>
        </div>


    </div>

    <?php

        include_once("html/common/site_footer.inc.php");
    ?>

    <script>

        $('#settings-form').submit(function(event) {

            if (constants.GOOGLE_RECAPTCHA_WEB) {

                event.preventDefault();

                grecaptcha.ready(function() {
                    grecaptcha.execute('<?php echo RECAPTCHA_SITE_KEY; ?>', {action: 'submit'}).then(function(token) {

                        $('#settings-form').prepend('<input type="hidden" name="recaptcha_token" value="'+ token + '">');
                        $('#settings-form').unbind('submit').submit();
                    });
                });
            }
        });
    </script>

</body>
</html>