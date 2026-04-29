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

    $settings = new settings($dbo);
    $settingsList = $settings->get();
    unset($settings);

    if ($settingsList['pl_enabled']['intValue'] == 0) {

        header("Location: /");
        exit;
    }

    auth::newAuthenticityToken();

    $page_id = "phone";

    $css_files = array("rje-landing.css?x6");
    $page_title = $LANG['label-auth-phone-title']." | ".APP_TITLE;

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

                    <h1 class="rje-title"><?php echo $LANG['label-auth-phone-title']; ?></h1>

                    <form class="rje-form" accept-charset="UTF-8" action="/phone" id="rje-form" method="post">

                        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                        <div class="rje-field loading-block hidden d-block text-center" style="width: 100%; padding-top: 155px; padding-bottom: 155px;">
                            <div class="loader" style="position: relative"><i class="ic icon-spin icon-spin"></i></div>
                        </div>

                        <div class="content-block rje-form">

                            <div class="rje-field">
                                <p class="info alert alert-info"><?php echo $LANG['label-auth-phone-subtitle']; ?></p>
                            </div>

                            <div class="rje-field">
                                <select name="c_code" id="c_code" style="width: 100%">

                                    <?php

                                    $phone = new phone($dbo);
                                    $c_list = $phone->c_getList(0);
                                    unset($phone);

                                    for ($i = 0; $i < count($c_list['items']); $i++) {

                                        ?>

                                        <option value="<?php echo "+".$c_list['items'][$i]['p_code'] ?>" <?php if ($i == 0) echo "selected=\"selected\""; ?>><?php echo $c_list['items'][$i]['c_name']." (+".$c_list['items'][$i]['p_code'].")"; ?></option>

                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="rje-field">
                                <input class="rje-input" type="text" id="inputRow" name="inputRow" placeholder="<?php echo $LANG['placeholder-otp-phone-number']; ?>" maxlength="15" pattern="[0-9]{1,12}" required>
                            </div>

                            <div class="rje-field">
                                <label for="user_receive_digest">
                                    <b><?php echo $LANG['label-auth-terms-promo']; ?></b>
                                    <a href="/terms"><?php echo $LANG['page-terms']; ?></a>
                                </label>
                            </div>

                            <button disabled id="send-code" class="rje-btn primary button" name="commit" onclick="auth();"><?php echo $LANG['action-send-code']; ?></button>

                            <p class="rje-footer">
                                You have an account? <a href="/" class="rje-link rje-link--strong"><?php echo $LANG['action-login']; ?></a>
                            </p>

                        </div>

                    </form>

                </div>

                <script type="text/javascript" src="/js/jquery-3.3.1.min.js"></script>

                <?php

                    include_once("html/common/site_footer.inc.php");
                ?>

            </div>

        </div>

    </div>

    <script type="text/javascript" src="/js/firebase/config.js"></script>

    <script>

        // Strings

        var strings = {

            sz_action_sent_code: "<?php echo $LANG['action-send-code']; ?>",
            sz_action_check_code: "<?php echo $LANG['action-check-code']; ?>",
            sz_placeholder_phone_number: "<?php echo $LANG['placeholder-otp-phone-number']; ?>",
            sz_placeholder_sms_code: "<?php echo $LANG['placeholder-otp-code']; ?>",
            sz_label_phone_number: "<?php echo $LANG['label-otp-phone-number-msg']; ?>",
            sz_label_sms_code: "<?php echo $LANG['label-otp-code-msg']; ?>",
            sz_label_sms_code_error: "<?php echo $LANG['label-otp-verification-code-error']; ?>",
            sz_label_sms_code_sent: "<?php echo $LANG['label-otp-verification-code-sent']; ?>",
            sz_label_verification_success: "<?php echo $LANG['label-otp-verification-success']; ?>",
            sz_label_verification_error: "<?php echo $LANG['label-otp-verification-error']; ?>",
            sz_label_phone_format_error: "<?php echo $LANG['label-otp-phone-format-error']; ?>",
            sz_label_many_requests_error: "<?php echo $LANG['label-otp-many-requests-error']; ?>",
            sz_label_captcha_error: "<?php echo $LANG['label-otp-captcha-error']; ?>",
            sz_label_phone_number_taken_error: "<?php echo $LANG['label-otp-phone-number-taken-error']; ?>",
            sz_action_close: "<?php echo $LANG['action-close']; ?>",
            sz_alert_title: "<?php echo $LANG['label-auth-phone-title']; ?>"
        };

        // Save Phone Number

        var phoneNumber = "";
        var codeSent = false;

        // Html elements

        $loadingBlock = $('div.loading-block');
        $contentBlock = $('div.content-block');

        $inputRow = $('input[name=inputRow]');
        $selectRow = $('select[name=c_code]');
        $actionButton = $('button[name=commit]');
        $infoLabel = $('p.info');

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);

        // Create a Recaptcha verifier instance globally
        // Calls submitPhoneNumberAuth() when the captcha is verified

        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('send-code', {
            'size': 'invisible',
            'callback': function(response) {

                // reCAPTCHA solved, allow signInWithPhoneNumber.

                console.log('window.recaptchaVerifier' + " " + response.toString());
            }
        });

        //

        function auth() {

            if (!codeSent) {

                submitPhoneNumberAuth();

            } else {

                submitPhoneNumberAuthCode();
            }
        }

        // This function runs when the 'sign-in-button' is clicked
        // Takes the value from the 'phoneNumber' input and sends SMS to that phone number

        function submitPhoneNumberAuth() {

            $loadingBlock.removeClass("hidden");
            $contentBlock.addClass("hidden");

            $selectRow.addClass("hidden");
            $actionButton.attr('disabled', 'disabled');

            console.log('submitPhoneNumberAuth start');

            phoneNumber = $('#c_code').find(":selected").text() + $inputRow.val();

            var appVerifier = window.recaptchaVerifier;

            firebase
                .auth()
                .signInWithPhoneNumber(phoneNumber, appVerifier)
                .then(function(confirmationResult) {

                    $loadingBlock.addClass("hidden");

                    window.confirmationResult = confirmationResult;

                    codeSent = true;

                    console.log('submitPhoneNumberAuth success');

                    $inputRow.attr("placeholder", strings.sz_placeholder_sms_code);
                    $inputRow.val('');
                    $actionButton.text(strings.sz_action_check_code);
                    $actionButton.attr("disabled", "disabled");
                    $infoLabel.text(strings.sz_label_sms_code);

                    Alert.show("alert-success", strings.sz_label_sms_code_sent);

                    $contentBlock.removeClass("hidden");
                })
                .catch(function(error) {

                    $loadingBlock.addClass("hidden");
                    $selectRow.removeClass("hidden");
                    $contentBlock.removeClass("hidden");

                    console.log('submitPhoneNumberAuth error');

                    console.log(error.message);
                    console.log(error.code);

                    $('div.errors-container').fadeOut( "slow", function() {

                        $(this).remove();
                    });

                    // error codes
                    // auth/invalid-phone-number
                    // auth/too-many-requests
                    // auth/captcha-check-failed

                    if (error.code === "auth/invalid-phone-number") {

                        Alert.show("alert-danger", strings.sz_label_phone_format_error);

                    } else if (error.code === "auth/too-many-requests") {

                        Alert.show("alert-danger", strings.sz_label_many_requests_error);

                        $actionButton.remove();

                    } else if (error.code === "auth/captcha-check-failed") {


                    }
                });
        }

        // This function runs when the 'confirm-code' button is clicked
        // Takes the value from the 'code' input and submits the code to verify the phone number
        // Return a user object if the authentication was successful, and auth is complete

        function submitPhoneNumberAuthCode() {

            $loadingBlock.removeClass("hidden");
            $contentBlock.addClass("hidden");

            $actionButton.attr('disabled', 'disabled');

            console.log('submitPhoneNumberAuthCode start');

            var code = $inputRow.val();

            confirmationResult
                .confirm(code)
                .then(function(result) {

                    var user = result.user;
                    console.log(user);
                    console.log(user.getIdToken(true).token);

                    firebase.auth().currentUser.getIdToken(/* forceRefresh */ true).then(function(idToken) {

                        // Send token to your backend via HTTPS
                        console.log(idToken);

                        $.ajax({
                            type: 'POST',
                            url: "/api/" + options.api_version + "/method/account.phoneLogin",
                            data: 'token=' + idToken + "&app_type=1",
                            dataType: 'json',
                            timeout: 30000,
                            success: function(response) {

                                if (response.hasOwnProperty('account_id')) {

                                    window.location.href = "/";

                                } else {

                                    $loadingBlock.addClass("hidden");
                                    $contentBlock.removeClass("hidden");

                                    window.location.href = "/";
                                }
                            },
                            error: function(xhr, type){

                                $loadingBlock.addClass("hidden");
                                $contentBlock.removeClass("hidden");
                            }
                        });

                    }).catch(function(error) {

                        $loadingBlock.addClass("hidden");
                        $contentBlock.removeClass("hidden");

                        // Handle error

                        //console.log(error.message);
                        //console.log(error.code);
                    });
                })
                .catch(function(error) {

                    $loadingBlock.addClass("hidden");
                    $contentBlock.removeClass("hidden");

                    // Handle error

                    console.log(error.message);
                    console.log(error.code);

                    // error codes
                    // auth/invalid-verification-code

                    if (error.code === "auth/invalid-verification-code") {

                        $inputRow.val("");

                        $infoLabel.text(strings.sz_label_sms_code_error);
                        $actionButton.attr("disabled", "disabled");
                    }
                });
        }

        //This function runs everytime the auth state changes. Use to verify if the user is logged in

        firebase.auth().onAuthStateChanged(function(user) {

            if (user) {

                console.log("USER LOGGED IN");

            } else {

                // No user is signed in.
                console.log("USER NOT LOGGED IN");
            }
        });

        window.Alert || ( window.Alert = {} );

        Alert.show = function (szAlert, szText) {

            var html = '<div id="alertModal" class="modal fade">';
            html +=' <div class="modal-dialog modal-dialog-centered" role="document">';
            html += '<div class="modal-content">';
            html += '<div class="modal-header">';
            html += '<h5 class="modal-title" id="alertModal">' + strings.sz_alert_title + '</h5>'
            html += '<button class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            html += '</div>'; // modal-header
            html += '<div class="modal-body">';

            html += '<p class="alert ' + szAlert +'">' + szText + '</p>';

            html += '</div>'; // modal-body
            html += '<div class="modal-footer">';
            html += '<button type="button" class="button primary" data-dismiss="modal">' + strings.sz_action_close + '</button>';
            html += '</div>';  // footer
            html += '</div>';  // modal-content
            html += '</div>';  // modal-dialog
            html += '</div>';  // reportModal
            $("#modal-section").html(html);
            $("#alertModal").modal();
        };

        $(document).ready(function() {

            if (!firebase.auth().currentUser) {

                firebase.auth().signOut().then(function() {

                    console.log('Signed Out');
                });
            }

            $inputRow.focus(function() {

                $('div.errors-container').fadeOut( "slow", function() {

                    $(this).remove();
                });

                $('div.success-container').fadeOut( "slow", function() {

                    $(this).remove();
                });
            });

            $inputRow.keyup(function(event) {

                if ($inputRow.val().length > 5) {

                    $actionButton.removeAttr("disabled");

                } else {

                    $actionButton.attr('disabled', 'disabled');
                }
            });
        });

    </script>

</body>
</html>