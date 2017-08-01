;(function ($, window, document, undefined) {
  'use strict';

  $(function () {
    var definitions = $.parseJSON(gdn.definition('Timeago'));

    $.timeago.settings.strings = definitions.locale;

    var attachTimeago = function (e) {
      $('time').timeago();
    };

    $(document).on('ready ajaxSuccess', attachTimeago);
  });

})(jQuery, window, document);
