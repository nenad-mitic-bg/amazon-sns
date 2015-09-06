
/* global ajaxurl */

jQuery(document).ready(function ($) {

  wp.asns = {
    notificationId: 0
  };

  function sendNotification(postId, topicKey) {
    $.post(ajaxurl, {
      action: 'asns_send',
      id: postId,
      topicKey: topicKey
    }).done(function (resp) {
      console.log(resp);
      wp.asns.dialog.dialog('close');
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
              $('#asns-send-topic').hide();
              $('#asns-send-progress').show();
              $(this).closest('.ui-dialog').find('button').attr('disabled', 1);
              sendNotification(wp.asns.notificationId, $('#asns-topic-id').val());
            }
          }
        ],
        close: function () {
          wp.asns.notificationId = 0;
          $('#asns-send-topic').show();
          $('#asns-send-progress').hide();
          $(this).closest('.ui-dialog').find('button').removeAttr('disabled');
        },
        create: function () {
          $('#asns-send-progress').progressbar({
            value: false
          });
        }
      });

      $('a.asns-send').on('click', function (e) {
        wp.asns.dialog.dialog('open');
        wp.asns.notificationId = $(this).data('id');
      });

    });
  }

  setupDialog();

});