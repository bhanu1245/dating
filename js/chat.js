window.App || ( window.App = {} );

App.hChatTimer = 0;
App.chat_time_ms = 2000;

App.chatInit = function(chat_id, user_id, access_token) {

    if (App.hChatTimer) clearTimeout(App.hChatTimer);
    App.chatRun(chat_id, user_id, access_token);
};

App.chatRun = function(chat_id, user_id, access_token) {

    if (typeof options.pageId !== typeof undefined && options.pageId === "chat") {

        Messages.update(chat_id, user_id, access_token)
    }
};


window.Messages || ( window.Messages = {} );

Messages.createInProgress = false;

Messages.updateChat = function (chat_id, chatFromUserId, chatToUserId) {

    if (Messages.createInProgress) { return; }
  Messages.createInProgress = true;

  $.ajax({
        type: 'POST',
        url: '/api/' + options.api_version + '/method/chat.update',
        data: 'accessToken=' + account.accessToken + "&accountId=" + account.id + "&chatId=" + chat_id + "&chatFromUserId=" + chatFromUserId + "&chatToUserId=" + chatToUserId,
        dataType: 'json',
        timeout: 30000,
        success: function(response){
            var res = Api.unwrap(response);
            var data = res.data || {};
            if (res.error) {
                $('.chat-error').text(data.msg || 'Chat sync failed').removeClass('hidden');
                return;
            }
        },
        error: function(xhr, status, error) {

        }
    });
};

Messages.update = function (chat_id, user_id, access_token) {

  var message_id = $("li.message-item").last().attr("data-id");

  $.ajax({
    type: 'POST',
    url: '/ajax/chat/update',
    data: 'access_token=' + access_token + "&chat_id=" + chat_id + "&user_id=" + user_id + "&message_id=" + message_id,
    dataType: 'json',
    timeout: 30000,
    success: function(response){

      var res = Api.unwrap(response);
            var data = res.data || {};

      if (res.error) {
          $('.chat-error').text(data.msg || 'Failed to update chat').removeClass('hidden');
      } else if (data.hasOwnProperty('html')) {

        $("ul.content-list").append(data.html);

          if (chat_from_user_id != 0 && chat_to_user_id != 0) {

              Messages.updateChat(chat_id, chat_from_user_id, chat_to_user_id);
          }
      }

      if (data.hasOwnProperty('items_all')) {

        items_all = data.items_all;
        items_loaded = $('li.message-item').length;
      }

      App.chat_time_ms = App.chat_time_ms + 1000;

      App.hChatTimer = setTimeout(function() {

        App.chatInit(chat_id, user_id, access_token);

      }, App.chat_time_ms);
    },
    error: function(xhr, status, error) {

      // var err = eval("(" + xhr.responseText + ")");
      // alert(err.Message);
    }
  });
};

Messages.create = function (chat_id, user_id) {


  var message_text = $('input[name=message_text]').val().trim();
  var message_img = $('input[name=message_image]').val().trim();
  var message_id = $("li.message-item").last().attr("data-id");

  if (message_text.length == 0 && message_img.length == 0) {

      return;
  }

  $.ajax({
    type: 'POST',
    url: '/ajax/chat/msg',
    data: 'message_text=' + encodeURIComponent(message_text) + '&message_img=' + message_img + '&access_token=' + account.accessToken + "&chat_id=" + chat_id + "&user_id=" + user_id + "&message_id=" + message_id,
    dataType: 'json',
    timeout: 30000,
    success: function(response){

      var res = Api.unwrap(response);
            var data = res.data || {};

      if (res.error) {
          $('.chat-error').text(res.msg || 'Message failed').removeClass('hidden');
          return;
      }

      if (data.hasOwnProperty('promode')) {

            $('#pro-mode-dlg').modal('show');
      }

      if (data.hasOwnProperty('html')) {

          if ($(".empty-list-banner").length) {

              $(".empty-list-banner").remove();
          }

        $("ul.content-list").append(data.html);
        $("input[name=message_text]").val("");


          $('div.image-upload-progress').addClass("hidden");
          $('div.image-upload-img').addClass('hidden');
          $('div.image-upload-button').removeClass('hidden');
          $("input[name=message_image]").val("");

          if (data.hasOwnProperty('chat_id') && chat_id == 0) {

              chat_id = data.chat_id;
              App.chatInit(chat_id, user_id, account.accessToken);
          }
      }

      if (data.hasOwnProperty('items_all')) {

        items_all = data.items_all;
        items_loaded = $('li.message-item').length;
      }
    },
    error: function(xhr, type){

        $('.chat-error').text('Network error').removeClass('hidden');
    },
    complete: function(){
        Messages.createInProgress = false;
    }
  });
};

Messages.sendSticker = function (chat_id, user_id, stickerId, stickerImgUrl) {


    var message_id = $("li.message-item").last().attr("data-id");

    $.ajax({
        type: 'POST',
        url: '/ajax/chat/msg',
        data: "message_id=" + message_id  + "&chat_id=" + chat_id + "&user_id=" + user_id + "&stickerId=" + stickerId + "&stickerImgUrl=" + stickerImgUrl + '&access_token=' + account.accessToken,
        dataType: 'json',
        timeout: 30000,
        success: function(response){

            var res = Api.unwrap(response);
            var data = res.data || {};

            if (res.error) {
                $('.chat-error').text(data.msg || 'Sticker send failed').removeClass('hidden');
                return;
            }

      if (data.hasOwnProperty('html')) {

                if ($(".empty-list-banner").length) {

                    $(".empty-list-banner").remove();
                }

                $("ul.content-list").append(data.html);
            }

            if (data.hasOwnProperty('items_all')) {

                items_all = data.items_all;
                items_loaded = $('li.message-item').length;
            }
        },
        error: function(xhr, type){


        }
    });
};

Messages.more = function (chat_id, user_id) {

  var message_id = $("li.message-item").first().attr("data-id");

  $('header.loading-banner').hide();

  $.ajax({
    type: 'POST',
    url: '/ajax/chat/more',
    data: "chat_id=" + chat_id + "&user_id=" + user_id + "&message_id=" + message_id + "&messages_loaded=" + items_loaded,
    dataType: 'json',
    timeout: 30000,
    success: function(response){

        $('header.loading-banner').remove();

      var res = Api.unwrap(response);
            var data = res.data || {};

      if (data.hasOwnProperty('html')) {

        $("ul.content-list").prepend(data.html);
      }

      if (data.hasOwnProperty('html2')) {

        $("div.content-list-page").prepend(data.html2);
      }

      if (data.hasOwnProperty('items_all')) {

        items_all = data.items_all;
        items_loaded = $('li.message-item').length;
      }
    },
    error: function(xhr, type){

        alert("error");
        $('header.loading-banner').show();
    }
  });
};

Messages.removeChat = function(chat_id, user_id) {

  $.ajax({
    type: 'POST',
    url: '/api/' + options.api_version + '/method/chat.remove',
    data: 'accessToken=' + account.accessToken + "&accountId=" + account.id + "&chatId=" + chat_id + "&profileId=" + user_id,
    dataType: 'json',
    timeout: 30000,
    success: function(response){

      $('li.chat-item[data-id=' + chat_id + ']').remove();
    },
    error: function(xhr, type){


    }
  });
};

Messages.changeChatImg = function(title, accountId, accessToken) {

  var img_url = $("img.msg_img_preview").attr("src");
  var def_url = "/img/camera.png";

  var i = img_url.localeCompare(def_url);

  if (i != 0) {

    $('input[name=message_image]').val("");
    $("img.msg_img_preview").attr("src", def_url);

    return;
  }

  var url = "/ajax/msg/method/uploadImg.php?action=get-box";

    $.colorbox({width:"450px", href: url, title: title, overlayClose: false, fixed:true, onComplete: function(){

        $('.file_select_btn').upload({
            name: 'uploaded_file',
            method: 'post',
            params: {"accountId": accountId, "accessToken": accessToken},
            enctype: 'multipart/form-data',
            action: '/api/v2/method/msg.uploadImg.inc.php',
            onComplete: function(text) {

                var response = JSON.parse(text);

                if (response.hasOwnProperty('error')) {

                    if (response.error === false) {

                        $.colorbox.close();

                        var data = Messages.unwrap(response);

                        if (data.hasOwnProperty('imgUrl')) {

                            $("input[name=message_image]").val(data.imgUrl);
                            $("img.msg_img_preview").attr("src", data.imgUrl);
                        }
                    }
                }

                $("div.file_loader_block").hide();
                $("div.file_select_block").show();
            },
            onSubmit: function() {

                $("div.file_select_block").hide();
                $("div.file_loader_block").show();
            }
        });
    }});
};

$(document).off('click', '.message-send-button').on('click', '.message-send-button', function (e) {
    e.preventDefault();
    Messages.create(typeof chat_id !== 'undefined' ? chat_id : 0, $(this).data('user-id') || 0);
});
