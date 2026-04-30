window.App || ( window.App = {} );
window.Api || (window.Api = {});

Api.unwrap = function (response) {
    if (!response || typeof response !== 'object') return {};
    if (response.data && typeof response.data === 'object') return response.data;
    return response;
};

Api.isOk = function (response) {
    return !!(response && response.error === false && response.data);
};

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

            var data = Api.unwrap(response);
            if (Api.isOk(response) || data.error === false) {

                if (data.hasOwnProperty("registrationComplete")) {
                    account.registrationComplete = parseInt(data.registrationComplete, 10) || 0;
                }
                if (data.hasOwnProperty("lowPhotoUrl") && data.lowPhotoUrl.length > 0) {
                    account.photoUrl = data.lowPhotoUrl;
                }

                if (typeof App.hasProfilePhoto === "function") {

                    if (account.registrationComplete === 1 || App.hasProfilePhoto()) {
                        $("#photoModal").modal("hide");
                    } else if (account.registrationComplete === 0 && account.id != 0) {
                        $('#photoModal').modal('show');
                    }
                }

                if ((data.notificationsCount || 0) < 1) {
                    $("span.notifications-badge").addClass("hidden");
                    $('span.notifications-primary-badge').text("");
                } else {
                    $("span.notifications-badge").removeClass("hidden");
                    $('span.notifications-primary-badge').text(data.notificationsCount);
                }

                if ((data.messagesCount || 0) < 1) {
                    $("span.messages-badge").addClass("hidden");
                    $('span.messages-primary-badge').text("");
                } else {
                    $("span.messages-badge").removeClass("hidden");
                    $('span.messages-primary-badge').text(data.messagesCount);
                }

                if ((data.guestsCount || 0) < 1) {
                    $("div.guests-badge").addClass("hidden");
                    $('span.guests-count').text("");
                } else {
                    $("div.guests-badge").removeClass("hidden");
                    $('span.guests-count').text(data.guestsCount);
                }

                if ((data.newMatchesCount || 0) < 1) {
                    $("div.matches-badge").addClass("hidden");
                    $('span.matches-count').text("");
                } else {
                    $("div.matches-badge").removeClass("hidden");
                    $('span.matches-count').text(data.newMatchesCount);
                }

                if ((data.newFriendsCount || 0) < 1) {
                    $("div.friends-badge").addClass("hidden");
                    $("span.friends-badge").addClass("hidden");
                } else {
                    $("div.friends-badge").removeClass("hidden");
                    $("span.friends-badge").removeClass("hidden");
                    $('span.friends-primary-badge').text(data.newFriendsCount);
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
            var data = Api.unwrap(response);
            if (Api.isOk(response) || data.error === false) {
                var imageUrl = data.imgUrl || itemImg;
                if ($('div.gallery-item img[src="' + imageUrl + '"]').length) {
                    return;
                }
                var html = '<div class="gallery-item" data-id="0"><img src="' + imageUrl + '" alt=""></div>';
                $('div.gallery-content, div.items-view').first().prepend(html);
                $('.gallery-empty-state').addClass('hidden');
            } else {
                $('.gallery-upload-error').text(data.msg || 'Upload failed').removeClass('hidden');
            }
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
            if (!$('div.gallery-item').length) $('.gallery-empty-state').removeClass('hidden');
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
            var data = Api.unwrap(response);
            if (data.error === true) {
                $('.action-error').text(data.msg || 'Request failed').removeClass('hidden');
                return;
            }
            if (data.myLike) {
                $('.item-like-button[data-id=' + itemId + ']').addClass("active");
            } else {
                $('.item-like-button[data-id=' + itemId + ']').removeClass("active");
            }

            if (data.hasOwnProperty('nextUser') && data.nextUser) {
                var next = data.nextUser;
                $('[data-hot-profile-name]').text(next.fullname || '');
                $('[data-hot-profile-photo]').attr('src', next.lowPhotoUrl || next.photoUrl || '');
                $('[data-hot-profile-id]').attr('data-hot-profile-id', next.id || 0);
            } else if (typeof window.HotGame !== 'undefined' && typeof HotGame.get === 'function') {
                HotGame.get();
            }
        }
    });
};

$(document).off('click', '.item-like-button').on('click', '.item-like-button', function (e) {
    e.preventDefault();
    Item.like($(this).data('id'), $(this).data('type') || 0);
});

$(document).off('click', '.friend-add-button').on('click', '.friend-add-button', function (e) {
    e.preventDefault();
    var profileId = $(this).data('profile-id');
    var $btn = $(this);
    $.post('/api/' + options.api_version + '/method/friends.sendRequest', {accountId: account.id, accessToken: account.accessToken, profileId: profileId}, function (response) {
        var data = Api.unwrap(response);
        if (!(data.error === true)) {
            $btn.addClass('disabled').text('Requested');
        } else {
            alert(data.msg || 'Unable to add friend');
        }
    }, 'json');
});

$(document).off('click', '.gift-send-button').on('click', '.gift-send-button', function (e) {
    e.preventDefault();
    var payload = {
        accountId: account.id,
        accessToken: account.accessToken,
        giftId: $(this).data('gift-id'),
        giftTo: $(this).data('gift-to'),
        giftAnonymous: $(this).data('gift-anonymous') || 0,
        message: $('textarea[name=gift_message]').val() || ''
    };
    $.post('/api/' + options.api_version + '/method/gifts.send', payload, function (response) {
        var data = Api.unwrap(response);
        if (!(data.error === true)) {
            if (data.balance !== undefined) {
                account.balance = data.balance;
                $('.account-balance').text(data.balance);
            }
            $('.gift-send-status').text('Gift sent').removeClass('hidden');
        } else {
            alert(data.msg || 'Gift failed');
        }
    }, 'json');
});



$(document).ajaxSuccess(function(event, xhr) {
    try {
        var response = JSON.parse(xhr.responseText);
        var payload = Api.unwrap(response);

        if (payload && payload.error === false && payload.photoUrl) {
            $("img.main-profile-photo, img.avatar").attr("src", payload.photoUrl + "?t=" + Date.now());
            if (typeof $ !== "undefined") {
                $('#photoModal').modal('hide');
            }
        }
    } catch (e) {}
});

window.Spotlight || (window.Spotlight = {});

Spotlight.prepare = function () {
    $('#spotlight-dlg .loader-content').removeClass('hidden');
    $('#spotlight-dlg .spotlight-content').html('');
    $('#spotlight-dlg').modal('show');
    $.post('/api/' + options.api_version + '/method/settings.get', {accountId: account.id, accessToken: account.accessToken}, function () {
        $('#spotlight-dlg .loader-content').addClass('hidden');
    }, 'json');
};

Spotlight.add = function (btn) {
    var $btn = $(btn);
    $btn.prop('disabled', true);
    $.post('/api/' + options.api_version + '/method/spotlight.add', {accountId: account.id, accessToken: account.accessToken}, function (response) {
        var data = Api.unwrap(response);
        if (data.error === true) {
            alert(data.msg || 'Unable to activate spotlight');
        } else {
            if (data.balance !== undefined) $('.account-balance').text(data.balance);
            $('#spotlight-dlg').modal('hide');
            $('.action-button[onclick*="Spotlight.prepare"]').prop('disabled', true).addClass('disabled').text('Added');
        }
        $btn.prop('disabled', false);
    }, 'json').fail(function () {
        alert('Network error');
        $btn.prop('disabled', false);
    });
};
