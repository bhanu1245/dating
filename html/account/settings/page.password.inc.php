<?php

    /*!
     * https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2024 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    $accountId = auth::getCurrentUserId();
    $account = new account($dbo, $accountId);
    $accountInfo = $account->get();

    $error = false;

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $login = isset($_POST['clogin']) ? $_POST['clogin'] : '';
        $password = isset($_POST['cpassword']) ? $_POST['cpassword'] : '';

        $old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

        $login = helper::clearText($login);
        $password = helper::clearText($password);

        $login = helper::escapeText($login);
        $password = helper::escapeText($password);

        $old_password = helper::clearText($old_password);
        $new_password = helper::clearText($new_password);

        $old_password = helper::escapeText($old_password);
        $new_password = helper::escapeText($new_password);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if (!$error) {

            if ($accountInfo['account_free'] != 0) {

                // Create Login and Password

                $result = $account->createPassword($login, $password);

                if (!$result['error']) {

                    // Password and login created

                    auth::setCurrentUserLogin($login);

                    header("Location: /account/settings/password/?error=false&error_code=5");
                    exit;

                } else {

                    if ($result['error_type'] == 0) {

                        // Error login

                        header("Location: /account/settings/password?error=true&error_code=1");
                        exit;

                    } else {

                        // Error password

                        header("Location: /account/settings/password?error=true&error_code=2");
                        exit;
                    }
                }

            } else {

                if (helper::isCorrectPassword($new_password)) {

                    $result = array();

                    $result = $account->setPassword($old_password, $new_password);

                    if (!$result['error']) {

                        // New password saved

                        header("Location: /account/settings/password/?error=false&error_code=4");
                        exit;

                    } else {

                        // Error Old password

                        header("Location: /account/settings/password?error=true&error_code=3");
                        exit;
                    }

                } else {

                    // Error password

                    header("Location: /account/settings/password?error=true&error_code=2");
                    exit;
                }
            }
        }

        header("Location: /account/settings/password?error=true");
        exit;
    }

    auth::newAuthenticityToken();

    $page_id = "settings_password";

    $css_files = array("main.css", "my.css");
    $page_ctitle = $LANG['page-profile-password']." | ".APP_TITLE;

    if ($accountInfo['account_free'] != 0) {

        $page_ctitle = $LANG['label-login-create'];
    }

    $css_files = array();
    $page_title = $page_ctitle." | ".APP_TITLE;

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

                        <h1><?php echo $page_ctitle; ?></h1>

                        <form accept-charset="UTF-8" action="/account/settings/password" autocomplete="off" class="edit_user" id="settings-form" method="post">

                            <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">

                            <div class="tabbed-content m-0">

                                <div class="tab-container">
                                    <nav class="tabs">
                                        <a href="/account/settings"><span class="tab"><?php echo $LANG['page-profile-settings']; ?></span></a>
                                        <a href="/account/settings/privacy"><span class="tab"><?php echo $LANG['page-privacy-settings']; ?></span></a>
                                        <a href="/account/balance"><span class="tab"><?php echo $LANG['page-balance']; ?></span></a>
                                        <a href="/account/settings/services"><span class="tab"><?php echo $LANG['label-services']; ?></span></a>
                                        <a href="/account/settings/password"><span class="tab active"><?php echo $LANG['label-password']; ?></span></a>
                                        <a href="/account/settings/referrals"><span class="tab"><?php echo $LANG['page-referrals']; ?></span></a>
                                        <a href="/account/settings/blacklist"><span class="tab"><?php echo $LANG['page-blacklist']; ?></span></a>
                                        <a href="/account/settings/otp"><span class="tab"><?php echo $LANG['page-otp']; ?></span></a>
                                        <a href="/account/settings/deactivation"><span class="tab"><?php echo $LANG['page-deactivate-account']; ?></span></a>
                                    </nav>
                                </div>

                                <?php

                                    if (isset($_GET['error_code'])) {

                                        $error_code = $_GET['error_code'];

                                        switch ($error_code) {

                                            case 1: {

                                                // Error login

                                                ?>

                                                <div class="alert alert-danger">
                                                    <ul>
                                                        <?php echo $LANG['label-login-create-error']; ?>
                                                    </ul>
                                                </div>

                                                <?php

                                                break;
                                            }

                                            case 2: {

                                                ?>

                                                <div class="alert alert-danger">
                                                    <ul>
                                                        <?php echo $LANG['msg-password-incorrect']; ?>
                                                    </ul>
                                                </div>

                                                <?php

                                                break;
                                            }

                                            case 3: {

                                                ?>

                                                <div class="alert alert-danger">
                                                    <ul>
                                                        <?php echo $LANG['label-password-old-error']; ?>
                                                    </ul>
                                                </div>

                                                <?php

                                                break;
                                            }

                                            case 4: {

                                                // New password saved

                                                ?>

                                                <div class="alert alert-success">
                                                    <ul>
                                                        <?php echo $LANG['label-password-saved']; ?>
                                                    </ul>
                                                </div>

                                                <?php

                                                break;
                                            }

                                            case 5: {

                                                // Password and login created

                                                ?>

                                                <div class="alert alert-success">
                                                    <ul>
                                                        <?php echo $LANG['label-login-create-success']; ?>
                                                    </ul>
                                                </div>

                                                <?php

                                                break;
                                            }

                                            default: {

                                                // Error token

                                                ?>

                                                <div class="alert alert-danger">
                                                    <ul>
                                                        <?php echo $LANG['msg-error-unknown']; ?>
                                                    </ul>
                                                </div>

                                                <?php

                                                break;
                                            }
                                        }
                                    }
                                ?>

                                <div class="tab-pane active form-table">

                                    <?php

                                        if ($accountInfo['account_free'] != 0) {

                                            ?>
                                            <div class="profile-basics form-row">
                                                <div class="form-cell left">
                                                    <p class="info"><?php echo $LANG['label-login-current'].": ".$accountInfo['username']; ?></p>
                                                    <p class="info"><?php echo $LANG['label-login-create-sub-title']; ?></p>
                                                </div>

                                                <div class="form-cell">
                                                    <input id="clogin" name="clogin" placeholder="<?php echo $LANG['label-login-new']; ?>" maxlength="32" type="text" value="">
                                                    <input id="cpassword" name="cpassword" placeholder="<?php echo $LANG['label-new-password']; ?>" maxlength="32" type="password" value="">
                                                </div>
                                            </div>
                                            <?php

                                        } else {

                                            ?>
                                            <div class="profile-basics form-row">
                                                <div class="form-cell left">
                                                    <p class="info"><?php echo $LANG['label-settings-password-sub-title']; ?></p>
                                                </div>

                                                <div class="form-cell">
                                                    <input id="old_password" name="old_password" placeholder="<?php echo $LANG['label-old-password']; ?>" maxlength="32" type="password" value="">
                                                    <input id="new_password" name="new_password" placeholder="<?php echo $LANG['label-new-password']; ?>" maxlength="32" type="password" value="">

                                                </div>
                                            </div>
                                            <?php
                                        }
                                    ?>

                                </div>

                            </div>

                            <input name="commit" class="button primary mt-3" type="submit" value="<?php echo $LANG['action-save']; ?>">

                        </form>
                    </div>


                </div>
            </div>
        </div>


    </div>

        <?php

            include_once("html/common/site_footer.inc.php");
        ?>

</body>
</html>