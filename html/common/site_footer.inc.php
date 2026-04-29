<?php

    /*!
     * https://raccoonsquare.com, https://raccoonjohn.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2026 Demianchuk Dmytro and Raccoon John (raccoonsquare@gmail.com)
     */

?>

    <?php

        if (auth::isSession()) {

            ?>
            <div class="modal modal-form fade spotlight-dlg" id="spotlight-dlg" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title placeholder-title"><?php echo $LANG['page-spotlight']; ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body pt-0">

                            <div class="loader-content p-10 m-10 d-block" style="height: 150px;">
                                <div class="loader">
                                    <i class="ic icon-spin icon-spin"></i>
                                </div>
                            </div>

                            <div class="spotlight-content">
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="button secondary" data-dismiss="modal"><?php echo $LANG['action-cancel']; ?></button>
                            <button type="button" onclick="Spotlight.add(this); return false;" class="button primary"><?php echo $LANG['action-accept']; ?></button>
                        </div>

                    </div>
                </div>
            </div>
            <?php
        }
    ?>

    <div class="modal modal-form fade" id="info-box" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title placeholder-title" id="info-box-title"><?php echo APP_TITLE; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>

                <div class="modal-body">

                    <div id="info-box-message" class="error-summary alert alert-danger"></div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="button primary" data-dismiss="modal"><?php echo $LANG['action-close']; ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="langModal" tabindex="-1" role="dialog" aria-labelledby="langModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="langModal"><?php echo $LANG['page-language']; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php

                    foreach ($LANGS as $name => $val) {

                        echo "<a onclick=\"App.setLanguage('$val'); return false;\" class=\"box-menu-item \" href=\"javascript:void(0)\">$name</a>";
                    }

                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button primary" data-dismiss="modal"><?php echo $LANG['action-close']; ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModal" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="photoModal"><?php echo $LANG['label-signup-complete']; ?></h5>
                </div>

                <div class="modal-body">

                    <div class="text-center standard-page">

                        <h5 class="mb-2"><?php echo $LANG['label-signup-title']; ?></h5>

                        <p>&nbsp</p>

                        <div class="alert alert-info text-left">
                            <ul>
                                <li>- <?php echo $LANG['label-signup-title-1']; ?></li>
                                <li>- <?php echo $LANG['label-signup-title-2']; ?></li>
                            </ul>
                        </div>

                        <a href="javascript: void(0);" class="profile_img_wrap my-4" style="cursor: default">
                            <span alt="Photo" class="profile-photo-complete profile-user-photo user_image profile-user-photo-bg" style="background-image: url('/img/profile_default_photo.png')"></span>
                        </a>

                        <div id="profile-photo-upload-block" class="d-block">
                                <span class="upload-button">
                                    <input type="file" id="profile-photo-upload" name="uploaded_file">
                                    <?php echo $LANG['label-signup-image-action-upload']; ?>
                                </span>
                        </div>

                        <div id="profile-photo-error-block" class="alert alert-danger text-center hidden">
                            <ul>
                                <li id="profile-photo-error-text"></li>
                            </ul>
                        </div>

                        <div id="profile-photo-success-block" class="alert alert-success text-center hidden">
                            <ul>
                                <li id="profile-photo-success-text"><?php echo $LANG['label-signup-image-error-success']; ?></li>
                            </ul>
                        </div>

                        <div id="profile-photo-progress-block" class="hidden">
                            <div class="profile-photo-upload-progress upload-progress">
                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button disabled id="profile-photo-finish-button" type="button" class="button primary" onclick="App.finishRegistration()"><?php echo $LANG['label-signup-image-action-finish']; ?></button>
                </div>

            </div>
        </div>
    </div>

    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls " style="display: none;">
        <div class="slides" style="width: 2048px;"></div>
        <h3 class="title hidden"></h3>
        <a class="prev text-light">‹</a>
        <a class="next text-light">›</a>
        <a class="close text-light">×</a>
        <a class="play-pause"></a>
        <ol class="indicator"></ol>
    </div>

    <div id="modal-section"></div>

    <div id="main-footer" >
        <div class="wrap">

            <ul id="footer-nav">
                <li><a href="/about"><?php echo $LANG['footer-about']; ?></a></li>
                <li><a href="/terms"><?php echo $LANG['footer-terms']; ?></a></li>
                <li><a href="/gdpr"><?php echo $LANG['footer-gdpr']; ?></a></li>
                <li><a href="/support"><?php echo $LANG['footer-support']; ?></a></li>
                <li><a class="lang_link" href="javascript:void(0)" data-toggle="modal" data-target="#langModal"><?php echo $LANG['lang-name']; ?></a></li>

                <li id="footer-copyright">
                    © <?php echo APP_YEAR; ?> <?php echo APP_TITLE; ?>
                </li>
            </ul>

        </div>
    </div>


    <script type="text/javascript" src="/js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="/js/jquery.cookie.js"></script>
    <script type="text/javascript" src="/js/my.js"></script>

    <script type="text/javascript" src="/js/jquery.autosize.js"></script>
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="/js/bootstrap-slider.min.js"></script>
    <script type="text/javascript" src="/js/load-image.all.min.js"></script>
    <script type="text/javascript" src="/js/jquery.ui.widget.js"></script>
    <script type="text/javascript" src="/js/jquery.iframe-transport.js"></script>
    <script type="text/javascript" src="/js/jquery.fileupload.js"></script>
    <script type="text/javascript" src="/js/jquery.fileupload-process.js"></script>
    <script type="text/javascript" src="/js/jquery.fileupload-image.js"></script>
    <script type="text/javascript" src="/js/jquery.fileupload-validate.js"></script>
    <script type="text/javascript" src="/js/blueimp-gallery.js"></script>
    <script type="text/javascript" src="/js/sidenav.min.js"></script>

    <script type="text/javascript">

        var options = {

            pageId: "<?php echo $page_id; ?>",
            api_version: "<?php echo API_VERSION; ?>"
        };

        var constants = {
            MAX_FILE_SIZE: 3145728,
            IMAGE_FILE_MAX_SIZE: <?php echo IMAGE_FILE_MAX_SIZE; ?>,
            VIDEO_FILE_MAX_SIZE: <?php echo VIDEO_FILE_MAX_SIZE; ?>,
            GOOGLE_CLIENT_ID: "<?php echo GOOGLE_CLIENT_ID; ?>",
            GOOGLE_RECAPTCHA_WEB: "<?php echo GOOGLE_RECAPTCHA_WEB; ?>"
        };

        var account = {

            id: "<?php echo auth::getCurrentUserId(); ?>",
            username: "<?php echo auth::getCurrentUserLogin(); ?>",
            accessToken: "<?php echo auth::getAccessToken(); ?>",
            balance: "<?php echo auth::getCurrentUserBalance(); ?>",
            registrationComplete: "<?php echo auth::getRegistrationComplete(); ?>"
        };

        var strings = {

            sz_action_block: "<?php echo $LANG['action-block']; ?>",
            sz_action_unblock: "<?php echo $LANG['action-unblock']; ?>",
            sz_action_close: "<?php echo $LANG['action-close']; ?>",
            sz_action_report: "<?php echo $LANG['action-report']; ?>",
            sz_report_reason_1: "<?php echo $LANG['label-profile-report-reason-1']; ?>",
            sz_report_reason_2: "<?php echo $LANG['label-profile-report-reason-2']; ?>",
            sz_report_reason_3: "<?php echo $LANG['label-profile-report-reason-3']; ?>",
            sz_report_reason_4: "<?php echo $LANG['label-profile-report-reason-4']; ?>",
            sz_report_reason_5: "<?php echo $LANG['label-profile-report-reason-5']; ?>",
            sz_action_remove_from_friends: "<?php echo $LANG['action-remove-from-friends']; ?>",
            sz_action_cancel_friends_request: "<?php echo $LANG['action-cancel-friend-request']; ?>",
            sz_action_add_to_friends: "<?php echo $LANG['action-add-to-friends']; ?>",
            sz_message_empty_list: "<?php echo $LANG['label-empty-list']; ?>"
        };

        $("#profile-photo-upload").fileupload({
            formData: {accountId: <?php echo auth::getCurrentUserId(); ?>, accessToken: "<?php echo auth::getAccessToken(); ?>", imgType: 0},
            name: 'image',
            url: "/api/" + options.api_version + "/method/profile.photo",
            dropZone:  '',
            dataType: 'json',
            singleFileUploads: true,
            multiple: false,
            maxNumberOfFiles: 1,
            maxFileSize: constants.MAX_FILE_SIZE,
            acceptFileTypes: "", // or regex: /(jpeg)|(jpg)|(png)$/i
            "files":null,
            minFileSize: null,
            messages: {
                "maxNumberOfFiles":"Maximum number of files exceeded",
                "acceptFileTypes":"File type not allowed",
                "maxFileSize": "File is too big",
                "minFileSize": "File is too small"},
            process: true,
            start: function (e, data) {

                console.log("start");

                $('div.profile-photo-upload-progress').css("display", "block");
                $('div#profile-photo-upload-block').addClass('hidden');
                $('div#profile-photo-error-block').addClass('hidden');
                $('div#profile-photo-progress-block').removeClass('hidden');
                $('div#profile-photo-success-block').addClass('hidden');
                $("button#profile-photo-finish-button").attr("disabled", "disabled");

                $("#profile-photo-upload").trigger('start');
            },
            processfail: function(e, data) {

                console.log("processfail");

                if (data.files.error) {

                    $('div#profile-photo-error-block').removeClass('hidden');
                    $('div#profile-photo-error-text').text(data.files[0].error);
                }
            },
            progressall: function (e, data) {

                console.log("progressall");

                //var result = jQuery.parseJSON(data.jqXHR.responseText);
                //console.log(result);

                var progress = parseInt(data.loaded / data.total * 100, 10);

                $('div.profile-photo-upload-progress').find('.progress-bar').attr('aria-valuenow', progress).css('width', progress + '%').text(progress + '%');
            },
            done: function (e, data) {

                console.log("done");
                console.log(data.jqXHR.responseText);

                var result = jQuery.parseJSON(data.jqXHR.responseText);

                if (result.hasOwnProperty('error')) {

                    if (result.error === false) {

                        if (result.hasOwnProperty('lowPhotoUrl')) {

                            $('div#profile-photo-error-block').addClass('hidden');
                            $('div#profile-photo-success-block').removeClass('hidden');

                            $("span.profile-photo-complete").css("background-image", "url(" + result.lowPhotoUrl + ")");
                            $("span.profile-user-photo").css("background-image", "url(" + result.lowPhotoUrl + ")");
                            $("span.avatar").css("background-image", "url(" + result.lowPhotoUrl + ")");
                            $("a.profile-user-photo-link").attr("href", result.originPhotoUrl);
                            $("img.profile-photo-avatar").attr("src", result.lowPhotoUrl);

                            $('#welcome-block').remove();

                            account.registrationComplete = 1;

                            $("button#profile-photo-finish-button").removeAttr("disabled");
                        }

                    } else {

                        console.log(result.error_description);

                        $('div#profile-photo-error-block').removeClass('hidden');
                        $('li#profile-photo-error-text').text(result.error_description);
                    }
                }

                $("#profile-photo-upload").trigger('done');
            },
            fail: function (e, data) {

                console.log(data.errorThrown);
            },
            always: function (e, data) {

                console.log("always");

                $('div#profile-photo-upload-block').removeClass('hidden');
                $('div#profile-photo-progress-block').addClass('hidden');
                $('div#profile-photo-upload-progress').css("display", "none");

                if (account.registrationComplete == 1) {

                    $("button#profile-photo-finish-button").removeAttr("disabled");
                }

                $("#profile-photo-upload").trigger('always');
            }
        });

    </script>

    <script type="text/javascript" src="/js/common.js"></script>

    <script type="text/javascript">

        <?php

            if (auth::isSession()) {

            ?>

                App.init();

            <?php
        }

        ?>

        window.App || ( window.App = {} );

        App.finishRegistration = function() {

            account.registrationComplete = 0;

            $("#photoModal").modal("hide");
        }

        $(document).ready(function() {

            if (account.registrationComplete == 0 && account.id != 0) {

                $('#photoModal').modal('show');
            }
        });

    </script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-P7WYHNLZGC"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-P7WYHNLZGC');
    </script>