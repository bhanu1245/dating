<?php

    /*!
     * raccoonsquare.com
     *
     * https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2022 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    if (!defined("APP_SIGNATURE")) {

        header("Location: /");
        exit;
    }

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    $gallery = new gallery($dbo);
    $gallery->setRequestFrom(auth::getCurrentUserId());

    $items_all = $gallery->count(false);
    $items_loaded = 0;

    $auto_moderate = false;

    $settings = new settings($dbo);
    $app_settings = $settings->get();
    unset($settings);

    if ($app_settings['galleryModeration']['intValue'] != 1) {

        // Auto moderate enabled

        $auto_moderate = true;
    }

    auth::newAuthenticityToken();

    $page_id = "my-gallery";

    $css_files = array();
    $page_title = $LANG['page-gallery']." | ".APP_TITLE;

    include_once("html/common/site_header.inc.php");

?>

<body class="page-gallery">

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

                    <div class="gallery-intro-header">
                        <h1 class="gallery-title"><?php echo $LANG['page-gallery']; ?></h1>
                        <p class="gallery-sub-title"><?php echo $LANG['page-gallery-sub-title']; ?></p>
                    </div>
                </div>

                <div class="card mb-2 new-post-form-container">

                    <div class="card-header">
                        <h3 class="card-title"><?php echo $LANG['label-post-form-title']; ?></h3>

                        <?php

                            if (!$auto_moderate) {

                                ?>
                                    <h6 class="card-subtitle mt-2"><i class="iconfont icofont-warning-alt mr-1"></i><?php echo $LANG['label-post-form-subtitle']; ?></h6>
                                <?php
                            }
                        ?>
                    </div>

                    <form onsubmit="create_item(); return false;" class="new-post-form" action="/" method="post">

                        <input autocomplete="off" type="hidden" name="accountId" value="<?php echo auth::getCurrentUserId(); ?>">
                        <input autocomplete="off" type="hidden" name="accessToken" value="<?php echo auth::getAccessToken(); ?>">
                        <input autocomplete="off" type="hidden" name="accessMode" value="0">
                        <input autocomplete="off" type="hidden" name="itemType" value="0">
                        <input autocomplete="off" type="hidden" name="itemShowInStream" value="1">
                        <input autocomplete="off" type="hidden" name="originImgUrl" value="">
                        <input autocomplete="off" type="hidden" name="previewImgUrl" value="">
                        <input autocomplete="off" type="hidden" name="imgUrl" value="">
                        <input autocomplete="off" type="hidden" name="videoUrl" value="">

                        <div class="editor-block">
                            <a href="/<?php echo auth::getCurrentUserLogin(); ?>" class="avatar" style="background-image:url(<?php echo auth::getCurrentUserPhotoUrl(); ?>)"></a>

                            <textarea name="comment" maxlength="1000" placeholder="<?php echo $LANG['placeholder-gallery-item-description']; ?>" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 65px;"></textarea>

                            <div class="dropdown emoji-dropdown dropup" style="">

                                <span class="smile-button btn-emoji-picker" data-toggle="dropdown" aria-expanded="false">
                                    <i class="btn-emoji-picker-icon iconfont icofont-slightly-smile"></i>
                                </span>

                                <div class="dropdown-menu dropdown-menu-right mt-2" x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(443px, -26px, 0px);">
                                    <div class="emoji-items">
                                        <div class="emoji-item">😀</div>
                                        <div class="emoji-item">😁</div>
                                        <div class="emoji-item">😂</div>
                                        <div class="emoji-item">😃</div>
                                        <div class="emoji-item">😄</div>
                                        <div class="emoji-item">😅</div>
                                        <div class="emoji-item">😆</div>
                                        <div class="emoji-item">😉</div>
                                        <div class="emoji-item">😊</div>
                                        <div class="emoji-item">😋</div>
                                        <div class="emoji-item">😎</div>
                                        <div class="emoji-item">😍</div>
                                        <div class="emoji-item">😘</div>
                                        <div class="emoji-item">🤗</div>
                                        <div class="emoji-item">🤩</div>
                                        <div class="emoji-item">🤔</div>
                                        <div class="emoji-item">🤨</div>
                                        <div class="emoji-item">😐</div>
                                        <div class="emoji-item">🙄</div>
                                        <div class="emoji-item">😏</div>
                                        <div class="emoji-item">😣</div>
                                        <div class="emoji-item">😥</div>
                                        <div class="emoji-item">😮</div>
                                        <div class="emoji-item">🤐</div>
                                        <div class="emoji-item">😯</div>
                                        <div class="emoji-item">😪</div>
                                        <div class="emoji-item">😫</div>
                                        <div class="emoji-item">😴</div>
                                        <div class="emoji-item">😌</div>
                                        <div class="emoji-item">😜</div>
                                        <div class="emoji-item">🤤</div>
                                        <div class="emoji-item">😓</div>
                                        <div class="emoji-item">😔</div>
                                        <div class="emoji-item">🤑</div>
                                        <div class="emoji-item">😲</div>
                                        <div class="emoji-item">🙁</div>
                                        <div class="emoji-item">😖</div>
                                        <div class="emoji-item">😞</div>
                                        <div class="emoji-item">😟</div>
                                        <div class="emoji-item">😤</div>
                                        <div class="emoji-item">😢</div>
                                        <div class="emoji-item">😭</div>
                                        <div class="emoji-item">😦</div>
                                        <div class="emoji-item">😧</div>
                                        <div class="emoji-item">😨</div>
                                        <div class="emoji-item">😩</div>
                                        <div class="emoji-item">😰</div>
                                        <div class="emoji-item">😱</div>
                                        <div class="emoji-item">😳</div>
                                        <div class="emoji-item">🤪</div>
                                        <div class="emoji-item">😵</div>
                                        <div class="emoji-item">😡</div>
                                        <div class="emoji-item">😠</div>
                                        <div class="emoji-item">🤬</div>
                                        <div class="emoji-item">😷</div>
                                        <div class="emoji-item">🤒</div>
                                        <div class="emoji-item">🤕</div>
                                        <div class="emoji-item">🤢</div>
                                        <div class="emoji-item">🤮</div>
                                        <div class="emoji-item">🤧</div>
                                        <div class="emoji-item">😇</div>
                                        <div class="emoji-item">🤠</div>
                                        <div class="emoji-item">🤡</div>
                                        <div class="emoji-item">🤥</div>
                                        <div class="emoji-item">🤫</div>
                                        <div class="emoji-item">🤭</div>
                                        <div class="emoji-item">🧐</div>
                                        <div class="emoji-item">🤓</div>
                                        <div class="emoji-item">😈</div>
                                        <div class="emoji-item">👿</div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="img_container d-block hidden">

                            <div class="img-items-list-page d-inline-block w-100" style="">

                            </div>

                        </div>

                        <div class="form_actions">

                            <div class="upload-progress hidden">
                                <div class="progress-bar " role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">100%</div>
                            </div>

                            <div class="item-actions">

                                <div class="post-addons-block">

                                    <div class="btn btn-secondary item-upload-button image-upload-button item-add-image">
                                        <input type="file" id="item-image-upload" name="uploaded_file">
                                        <i class="iconfont icofont-ui-image mr-2"></i><?php echo $LANG['action-add-photo']; ?>
                                    </div>

                                    <div class="btn btn-secondary item-upload-button video-upload-button item-add-video">
                                        <input type="file" id="item-video-upload" name="uploaded_video_file">
                                        <i class="iconfont icofont-video-alt mr-2"></i><?php echo $LANG['action-add-video']; ?>
                                    </div>

                                </div>


                                <div class="post-options-block">

                                    <div class="d-inline-block align-top">

                                        <span class="dropdown" style="display: inline-block;">
                                            <button type="button" class="button flat_btn change-post-mode-button dropdown-toggle mb-sm-0" data-toggle="dropdown" style="padding: 10px; font-size: 12px;" aria-expanded="false">
                                                <i class="iconfont icofont-news mr-1"></i>
                                                <span><?php echo $LANG['label-show-on-stream']; ?></span>
                                            </button>

                                            <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 36px, 0px);">
                                                <a class="dropdown-item access-mode-show-on-stream" onclick="change_item_mode(1); return false;"><?php echo $LANG['label-show-on-stream']; ?></a>
                                                <a class="dropdown-item access-mode-not-show-on-stream" onclick="change_item_mode(0); return false;"><?php echo $LANG['label-not-show-on-stream']; ?></a>
                                            </div>
                                        </span>

                                    </div>

                                    <button style="padding: 7px 16px;" class="primary_btn blue" value="ask">Post</button>

                                </div>

                            </div>

                        </div>
                    </form>

                </div>

                <div class="standard-page cardview-container p-0 items-container">

                    <?php

                    $result = $gallery->get(0, auth::getCurrentUserId(), false, true);

                    $items_loaded = count($result['items']);

                    if ($items_loaded != 0) {

                        ?>
                        <div class="cardview items-view">
                            <?php

                            foreach ($result['items'] as $key => $value) {

                                draw::galleryItem($value, $LANG, $helper);
                            }

                            ?>
                        </div>
                        <?php

                        if ($items_loaded >= 20) {

                            ?>

                            <header class="top-banner loading-banner p-0 pt-3">

                                <div class="prompt">
                                    <button onclick="Items.more('/<?php echo auth::getCurrentUserLogin(); ?>/gallery', '<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                                </div>

                            </header>

                            <?php
                        }

                    } else {

                        ?>

                        <div class="card information-banner">
                            <div class="card-header">
                                <div class="card-body">
                                    <h5 class="m-0"><?php echo $LANG['label-empty-list']; ?></h5>
                                </div>
                            </div>
                        </div>

                        <?php
                    }
                    ?>

                </div>

            </div>
        </div>

    </div>

    <?php

        include_once("html/common/site_footer.inc.php");
    ?>

        <script type="text/javascript">

            var items_all = <?php echo $items_all; ?>;
            var items_loaded = <?php echo $items_loaded; ?>;

            var auth_token = "<?php echo auth::getAuthenticityToken(); ?>";
            var username = "<?php echo auth::getCurrentUserLogin(); ?>";

            var $video_upload_button = $('div.video-upload-button');
            var $image_upload_button = $('div.image-upload-button');
            var $image_container = $('div.img_container');
            var $item_actions = $('div.item-actions');
            var $upload_progress = $('div.upload-progress ');

            var $infobox = $('div#info-box');

            $("#item-image-upload").fileupload({
                formData: {accountId: account.id, accessToken: account.accessToken},
                name: 'image',
                url: "/api/" + options.api_version + "/method/gallery.uploadImg",
                dropZone:  '',
                dataType: 'json',
                singleFileUploads: true,
                multiple: false,
                maxNumberOfFiles: 1,
                maxFileSize: constants.IMAGE_FILE_MAX_SIZE,
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

                    $item_actions.addClass("hidden");
                    $upload_progress.removeClass("hidden");

                    $("#item-image-upload").trigger('start');
                },
                processfail: function(e, data) {

                    console.log("processfail");

                    if (data.files.error) {

                        $infobox.find('#info-box-message').text(data.files[0].error);
                        $infobox.modal('show');
                    }
                },
                progressall: function (e, data) {

                    console.log("progressall");

                    var progress = parseInt(data.loaded / data.total * 100, 10);

                    $upload_progress.find('.progress-bar').attr('aria-valuenow', progress).css('width', progress + '%').text(progress + '%');
                },
                done: function (e, data) {

                    console.log("done");

                    var response = data && data.result ? data.result : (data && data.jqXHR ? jQuery.parseJSON(data.jqXHR.responseText) : {});
                    const res = Api.unwrap(response);
                    if (!res.error) {
                            const item = res.data.items && res.data.items[0];
                            if (item && (item.originUrl || item.originPhotoUrl)) {
                                const previewUrl = item.previewUrl || item.previewPhotoUrl || item.normalUrl || item.normalPhotoUrl || item.originUrl || item.originPhotoUrl;
                                const normalUrl = item.normalUrl || item.normalPhotoUrl || previewUrl;
                                const originUrl = item.originUrl || item.originPhotoUrl || normalUrl;

                                var html = '<div class="gallery-item new-post-media-item">';
                                html +=' <div class="item-inner">';
                                html += '<div class="gallery-item-preview" style="background-image:url(' + previewUrl + ')">';
                                html += '<span class="action" onclick="delete_item($(this))">×</span>';
                                html += '</div>';  // gallery-item-preview
                                html += '</div>';  // item-inner
                                html += '</div>';  // gallery-item
                                $image_container.find('.img-items-list-page').html(html);

                                $('input[name=previewImgUrl]').val(previewUrl);
                                $('input[name=originImgUrl]').val(originUrl);
                                $('input[name=imgUrl]').val(normalUrl);
                                $('input[name=itemType]').val("0");
                                update_ui();
                            } else {
                                $infobox.find('#info-box-message').text('Upload succeeded but image data is incomplete');
                                $infobox.modal('show');
                            }

                    } else {
                        $infobox.find('#info-box-message').text(res.msg || 'Upload failed');
                        $infobox.modal('show');
                    }

                    $("#item-image-upload").trigger('done');
                },
                fail: function (e, data) {

                    console.log("fail");

                    console.log(data.errorThrown);
                    $infobox.find('#info-box-message').text('Image upload failed');
                    $infobox.modal('show');
                },
                always: function (e, data) {

                    console.log("always");

                    update_ui();

                    $upload_progress.addClass("hidden");

                    $("#item-image-upload").trigger('always');
                }
            });

            $("#item-video-upload").fileupload({
                formData: {accountId: account.id, accessToken: account.accessToken},
                name: 'image',
                url: "/api/" + options.api_version + "/method/gallery.uploadVideo",
                dropZone:  '',
                dataType: 'json',
                singleFileUploads: true,
                multiple: false,
                maxNumberOfFiles: 1,
                maxFileSize: constants.VIDEO_FILE_MAX_SIZE,
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

                    $item_actions.addClass("hidden");
                    $upload_progress.removeClass("hidden");

                    $("#item-video-upload").trigger('start');
                },
                processfail: function(e, data) {

                    console.log("processfail");

                    if (data.files.error) {

                        $infobox.find('#info-box-message').text(data.files[0].error);
                        $infobox.modal('show');
                    }
                },
                progressall: function (e, data) {

                    console.log("progressall");

                    var progress = parseInt(data.loaded / data.total * 100, 10);

                    $upload_progress.find('.progress-bar').attr('aria-valuenow', progress).css('width', progress + '%').text(progress + '%');
                },
                done: function (e, data) {

                    console.log("done");

                    var response = data && data.result ? data.result : (data && data.jqXHR ? jQuery.parseJSON(data.jqXHR.responseText) : {});
                    const res = Api.unwrap(response);
                    if (!res.error) {
                            const videoUrl = res.data.videoUrl || '';
                            const videoMimeType = res.data.videoMimeType || 'video/mp4';
                            if (videoUrl) {

                                var html = '<div class="video-item new-post-media-item">';
                                html +=' <div class="video-item-inner">';
                                html += '<span class="action" onclick="delete_item($(this))">×</span>';
                                html += '<video controls preload="metadata">';
                                html += '<source src="' + videoUrl + '" type="' + videoMimeType + '">';
                                html += '</video>';  // video
                                html += '</div>';  // video-item-inner
                                html += '</div>';  // video-item
                                $image_container.find('.img-items-list-page').html(html);

                                $('input[name=itemType]').val("1");
                                $('input[name=videoUrl]').val(videoUrl);
                                update_ui();
                            } else {
                                $infobox.find('#info-box-message').text('Upload succeeded but video data is incomplete');
                                $infobox.modal('show');
                            }
                    } else {
                        $infobox.find('#info-box-message').text(res.msg || 'Upload failed');
                        $infobox.modal('show');
                    }

                    $("#item-video-upload").trigger('done');
                },
                fail: function (e, data) {

                    console.log("fail");

                    console.log(data.errorThrown);
                    $infobox.find('#info-box-message').text('Video upload failed');
                    $infobox.modal('show');
                },
                always: function (e, data) {

                    console.log("always");

                    update_ui();

                    $upload_progress.addClass("hidden");

                    $("#item-video-upload").trigger('always');
                }
            });

            function update_ui() {

                $item_actions.removeClass('hidden');

                if ($image_container.find('.gallery-item').length != 0 || $image_container.find('.video-item').length != 0) {

                    $image_container.removeClass('hidden');

                    $video_upload_button.addClass('hidden');
                    $image_upload_button.addClass('hidden');

                } else {

                    $image_container.addClass('hidden');

                    $video_upload_button.removeClass('hidden');
                    $image_upload_button.removeClass('hidden');
                }
            }

            function delete_item(thisObj) {

                thisObj.parents('div.new-post-media-item').remove();

                $('input[name=videoUrl]').val("");
                $('input[name=previewImgUrl]').val("");
                $('input[name=originImgUrl]').val("");
                $('input[name=imgUrl]').val("");
                $('input[name=itemType]').val("0");

                update_ui();
            }

            function change_item_mode(access_mode) {

                $("input[name=access_mode]").val(access_mode);
                $("button.change-post-mode-button").find("i").removeClass("icofont-news");

                switch (access_mode) {

                    case 1: {

                        $("button.change-post-mode-button").find("span").text($("a.access-mode-show-on-stream").text());
                        $("button.change-post-mode-button").find("i").addClass("icofont-news");

                        $('input[name=itemShowInStream]').val(1);

                        break;
                    }

                    default: {

                        $("button.change-post-mode-button").find("span").text($("a.access-mode-not-show-on-stream").text());
                        $("button.change-post-mode-button").find("i").addClass("icofont-news");

                        $('input[name=itemShowInStream]').val(0);

                        break;
                    }
                }
            };

            function create_item() {

                if ($image_container.find('.gallery-item').length == 0 && $image_container.find('.video-item').length == 0) {

                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: "/api/" + options.api_version + "/method/gallery.new",
                    data: $("form.new-post-form").serialize(),
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response) {

                        const res = Api.unwrap(response);
                        const data = res.data || {};
                        if (res.error) {
                            alert(res.msg || 'Failed to create post');
                            return;
                        }
                        var mediaHtml = '';
                        if ($('input[name=itemType]').val() === "1") {
                            mediaHtml = '<div class="video-item"><div class="video-item-inner"><video controls preload="metadata"><source src="' + $('input[name=videoUrl]').val() + '" type="video/mp4"></video></div></div>';
                        } else {
                            mediaHtml = '<img src="' + $('input[name=imgUrl]').val() + '" alt="">';
                        }
                        var card = '<div class="gallery-item" data-id="' + (data.itemId || 0) + '">' + mediaHtml + '</div>';
                        $('div.items-view').prepend(card);
                        delete_item($image_container.find('.action'));
                        $('textarea[name=comment]').val('');
                        update_ui();
                    },
                    error: function(xhr, type){
                        $infobox.find('#info-box-message').text('Failed to create post');
                        $infobox.modal('show');
                    }
                });
            }

        </script>


</body
</html>
