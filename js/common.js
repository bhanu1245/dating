window.App || ( window.App = {} );

App.hTimer = 0;
App.time_ms = 7000;

App.init = function() {

    if (App.hTimer) clearTimeout(App.hTimer);
    App.run();
};

App.run = function() {

    $.ajax({
        type: "POST",
        url: "/api/" + options.api_version + "/method/account.getSettings",
        data: "accountId=" + account.id + "&accessToken=" + account.accessToken,
        success: function(response) {

            if (response.error === false) {

                if (response.notificationsCount < 1) {
                    $("span.notifications-badge").addClass("hidden");
                    $('span.notifications-primary-badge').text("");
                } else {
                    $("span.notifications-badge").removeClass("hidden");
                    $('span.notifications-primary-badge').text(response.notificationsCount);
                }

                if (response.messagesCount < 1) {
                    $("span.messages-badge").addClass("hidden");
                    $('span.messages-primary-badge').text("");
                } else {
                    $("span.messages-badge").removeClass("hidden");
                    $('span.messages-primary-badge').text(response.messagesCount);
                }

                if (response.guestsCount < 1) {
                    $("div.guests-badge").addClass("hidden");
                    $('span.guests-count').text("");
                } else {
                    $("div.guests-badge").removeClass("hidden");
                    $('span.guests-count').text(response.guestsCount);
                }

                if (response.newMatchesCount < 1) {
                    $("div.matches-badge").addClass("hidden");
                    $('span.matches-count').text("");
                } else {
                    $("div.matches-badge").removeClass("hidden");
                    $('span.matches-count').text(response.newMatchesCount);
                }

                if (response.newFriendsCount < 1) {
                    $("div.friends-badge").addClass("hidden");
                    $("span.friends-badge").addClass("hidden");
                } else {
                    $("div.friends-badge").removeClass("hidden");
                    $("span.friends-badge").removeClass("hidden");
                    $('span.friends-primary-badge').text(response.newFriendsCount);
                }
            }
        },
        complete: function() {

            App.time_ms = App.time_ms + 4000;

            App.hTimer = setTimeout(function() {
                App.init();
            }, App.time_ms);
        }
    });
};

App.setLanguage = function(language) {

    $('#langModal').modal('toggle');
    $.cookie("lang", language, { expires : 7, path: '/' });
    location.reload();
};

window.Gallery || (window.Gallery = {});

Gallery.add = function (itemImg, itemPreviewImg, itemOriginImg) {

    $.ajax({
        type: 'POST',
        url: '/api/' + options.api_version + '/method/gallery.new',
        data: 'accessToken=' + account.accessToken + "&accountId=" + account.id + "&imgUrl=" + itemImg,
        dataType: 'json',
        success: function(response) {
            location.reload();
        }
    });
};

Gallery.remove = function (itemId) {

    $('div.gallery-item[data-id=' + itemId + ']').hide();

    $.ajax({
        type: 'POST',
        url: '/api/' + options.api_version + "/method/gallery.remove",
        data: 'accountId=' + account.id + '&accessToken=' + account.accessToken + '&itemId=' + itemId,
        success: function(response){
            $('div.gallery-item[data-id=' + itemId + ']').remove();
        }
    });
};

window.Items || (window.Items = {});

Items.more = function (url, offset) {

    $.ajax({
        type: 'POST',
        url: url,
        data: 'itemId=' + offset,
        success: function(response){
            if (response.html){
                $("div.items-view").append(response.html);
            }
        }
    });
};

window.Item || ( window.Item = {} );

Item.like = function (itemId, itemType) {

    $.ajax({
        type: 'POST',
        url: '/api/v2/method/gallery.like',
        data: 'accessToken=' + account.accessToken + "&accountId=" + account.id + "&itemId=" + itemId,
        success: function(response){
            if (response.myLike) {
                $('.item-like-button[data-id=' + itemId + ']').addClass("active");
            } else {
                $('.item-like-button[data-id=' + itemId + ']').removeClass("active");
            }
        }
    });
};


// =========================================================
// 🚀 FINAL PROFILE UPLOAD + POPUP FIX (CLEAN VERSION)
// =========================================================

$(document).ajaxSuccess(function(event, xhr, settings) {

    try {
        var response = JSON.parse(xhr.responseText);

        if (response && response.photoUrl && response.error === false) {

            console.log("🔥 PROFILE PHOTO UPLOADED");

            // ✅ Update profile image everywhere
            document.querySelectorAll("img").forEach(img => {

                if (
                    img.src.includes("default") ||
                    img.src.includes("avatar") ||
                    img.src.includes("profile")
                ) {
                    img.src = response.photoUrl + "?t=" + new Date().getTime();
                }
            });

            // ✅ Close modal safely
            if (typeof $ !== "undefined") {
                $('.modal').modal('hide');
            }

            // ✅ Remove overlays completely
            document.querySelectorAll(".modal, .modal-backdrop, .overlay, .dialog, .popup")
                .forEach(el => el.remove());

            document.body.classList.remove("modal-open");

            console.log("🔥 POPUP REMOVED");

            // ✅ FINAL GUARANTEED FIX → reload page
            setTimeout(() => {
                console.log("🔥 RELOADING PAGE TO SYNC STATE");
                window.location.reload();
            }, 800);
        }

    } catch (e) {
        // ignore non-json responses
    }
});