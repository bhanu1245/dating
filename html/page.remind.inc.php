<?php

    /*!
     * https://raccoonsquare.com, https://raccoonjohn.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2026 Demianchuk Dmytro and Raccoon John (raccoonsquare@gmail.com)
     */

    if (auth::isSession()) {

        header("Location: /");
        exit;
    }

    require_once 'sys/addons/vendor/autoload.php';

    $email = '';

    $error = false;
    $error_message = array();
    $sent = false;

    if ( isset($_GET['sent']) ) {

        $sent = isset($_GET['sent']) ? $_GET['sent'] : 'false';

        if ($sent === 'success') {

            $sent = true;

        } else {

            $sent = false;
        }
    }

    if (!empty($_POST)) {

        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
        $recaptcha_token = isset($_POST['recaptcha_token']) ? $_POST['recaptcha_token'] : '';

        $email = helper::clearText($email);
        $email = helper::escapeText($email);

        // Google Recaptcha

        $recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_SECRET_KEY);
        $resp = $recaptcha->verify($recaptcha_token, $_SERVER['REMOTE_ADDR']);

        if (!$resp->isSuccess()){

            $error = true;
            $error_message[] = "Google Recaptcha error";
        }

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
            $error_message[] = $LANG['msg-error-unknown'];
        }

        if (!helper::isCorrectEmail($email)) {

            $error = true;
            $error_message[] = $LANG['msg-email-incorrect'];
        }

        if ( !$error && !$helper->isEmailExists($email) ) {

            $error = true;
            $error_message[] = $LANG['msg-email-not-found'];
        }

        if (!$error) {

            $accountId = $helper->getUserIdByEmail($email);

            if ($accountId != 0) {

                $account = new account($dbo, $accountId);

                $accountInfo = $account->get();

                if ($accountInfo['error'] === false && $accountInfo['state'] != ACCOUNT_STATE_BLOCKED) {

                    $clientId = 0; // Desktop version

                    $restorePointInfo = $account->restorePointCreate($email, $clientId);

                    ob_start();

                    ?>

                    <html>
                    <body>
                    This is link <a href="<?php echo APP_URL;  ?>/restore?hash=<?php echo $restorePointInfo['hash']; ?>"><?php echo APP_URL;  ?>/restore/?hash=<?php echo $restorePointInfo['hash']; ?></a> to reset your password.
                    </body>
                    </html>

                    <?php

                    $from = SMTP_EMAIL;

                    $to = $email;

                    $html_text = ob_get_clean();

                    $subject = APP_TITLE." | Password reset";

                    $mail = new phpmailer();

                    $mail->isSMTP();                                      // Set mailer to use SMTP
                    $mail->Host = SMTP_HOST;                               // Specify main and backup SMTP servers
                    $mail->SMTPAuth = SMTP_AUTH;                               // Enable SMTP authentication
                    $mail->Username = SMTP_USERNAME;                      // SMTP username
                    $mail->Password = SMTP_PASSWORD;                      // SMTP password
                    $mail->SMTPSecure = SMTP_SECURE;                            // Enable TLS encryption, `ssl` also accepted
                    $mail->Port = SMTP_PORT;                                    // TCP port to connect to

                    $mail->From = $from;
                    $mail->FromName = APP_TITLE;
                    $mail->addAddress($to);                               // Name is optional

                    $mail->isHTML(true);                                  // Set email format to HTML

                    $mail->Subject = $subject;
                    $mail->Body    = $html_text;

                    $mail->send();
                }
            }

            $sent = true;
            header("Location: /remind?sent=success");
        }
    }

    auth::newAuthenticityToken();

    $page_id = "remind";

    $css_files = array("rje-landing.css?x11");
    $page_title = $LANG['page-restore']." | ".APP_TITLE;

    include_once("html/common/site_header.inc.php");
?>

<body class="rje-body">

<div class="rje-auth">

    <div class="rje-auth__left">
        <div class="rje-illustration">
            <div class="rje-illustration__content">
                <h2 class="rje-illustration__title"><?php echo APP_NAME; ?></h2>
                <p class="rje-illustration__text">
                    <?php echo $LANG['main-page-prompt-login']; ?>
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

                <?php

                    if ($sent) {

                        ?>

                            <h1 class="rje-title"><?php echo $LANG['page-restore']; ?></h1>

                            <div class="alert alert-success" style="">
                                <span class="title"><b><?php echo $LANG['msg-reset-password-sent']; ?></b></span>
                                <ul>
                                    <li><b><?php echo $LANG['msg-reset-password-sent']; ?></b></li>
                                </ul>
                            </div>

                            <p class="rje-footer">
                                Have you set a new password? <a href="/" class="rje-link rje-link--strong"><?php echo $LANG['action-login']; ?></a>
                            </p>

                        <?php

                    } else {

                        ?>

                            <h1 class="rje-title"><?php echo $LANG['page-restore']; ?></h1>

                            <form class="rje-form" accept-charset="UTF-8" action="/remind" id="rje-form" method="post">

                                <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                                <div class="rje-field">
                                    <label class="rje-label"><?php echo $LANG['label-email']; ?></label>
                                    <input class="rje-input" name="email" placeholder="<?php echo $LANG['label-email']; ?>" required="required" size="30" type="text" value="<?php echo $email; ?>" />
                                </div>

                                <button type="submit" class="rje-btn primary button"><?php echo $LANG['action-next']; ?></button>

                            </form>

                            <p class="rje-footer">
                                Don’t have an account? <a href="/signup" class="rje-link rje-link--strong"><?php echo $LANG['action-signup']; ?></a>
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