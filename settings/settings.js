
jQuery(document).ready(function ($) {

  function getRow(keyName, keyPlaceholder, valueName, valuePlaceholder) {
    return '<tr>'
            + '<td>'
            + '<input type="text" placeholder="' + keyPlaceholder + '" name="asns_settings[' + keyName + '][]" />'
            + '&nbsp;<input type="text" class="regular-text" placeholder="' + valuePlaceholder + '" name="asns_settings[' + valueName + '][]" />'
            + '&nbsp;<a href="#" class="remove-row">Remove</a>'
            + '</td>'
            + '</tr>';
  }

  function initRemoveLinks() {
    $('a.remove-row')
            .off('click')
            .on('click', function (e) {
              e.preventDefault();
              $(this).parent().remove();
            });
  }

  $('#add-app').on('click', function (e) {
    e.preventDefault();
    $('#apps-parent').append(getRow('app_keys', 'App Name', 'app_arns', 'App ARN'));
    initRemoveLinks();
  });

  $('#add-topic').on('click', function (e) {
    e.preventDefault();
    $('#topics-parent').append(getRow('topic_keys', 'Topic Name', 'topic_arns', 'Topic ARN'));
    initRemoveLinks();
  });

  initRemoveLinks();

});
