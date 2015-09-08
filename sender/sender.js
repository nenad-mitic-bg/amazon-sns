
/* global ajaxurl */

jQuery(document).ready(function ($) {

  wp.asns = {
    notificationId: 0,
    minLoadMilis: 2000
  };

  function sendNotification(postId, topicKey) {
    wp.asns.start = new Date().getTime();

    $.post(ajaxurl, {
      action: 'asns_send',
      id: postId,
      topicKey: topicKey
    }).done(function (resp) {
      var timeout = wp.asns.minLoadMilis + wp.asns.start - (new Date()).getTime();

      setTimeout(function () {
        location.reload();
      }, timeout);
    });
  }

  function setupDialog() {
    $.get(ajaxurl, {
      action: 'asns_get_modal'
    }).done(function (data) {

      $('.wrap form:first').after(data);

      wp.asns.dialog = $('#asns-send-dialog').dialog({
        autoOpen: false,
        modal: true,
        draggable: false,
        width: 400,
        buttons: [
          {
            text: 'Cancel',
            click: function () {
              wp.asns.dialog.dialog('close');
            }
          },
          {
            text: 'Send',
            class: 'button button-primary',
            click: function () {
              var topicKey = $('#asns-topic-id').val();

              if (!topicKey) {
                alert('You must select a topic to push the message to');
                return;
              }

              $('#asns-send-topic').hide();
              $('#asns-send-progress').show();
              $(this).closest('.ui-dialog').find('button').attr('disabled', 1);
              sendNotification(wp.asns.notificationId, topicKey);
            }
          }
        ],
        close: function () {
          wp.asns.notificationId = 0;
          $('#asns-send-topic').show();
          $('#asns-send-progress').hide();
          $(this).closest('.ui-dialog').find('button').removeAttr('disabled');
          $('#asns-topic-id').val('');
        },
        create: function () {
          $('#asns-send-progress').progressbar({
            value: false
          });
        }
      });

      $('a.asns-send').on('click', function (e) {
        e.preventDefault();
        wp.asns.dialog.dialog('open');
        wp.asns.notificationId = $(this).data('id');
      });

    });
  }

  setupDialog();

});