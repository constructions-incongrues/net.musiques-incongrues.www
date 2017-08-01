$(document).ready(function() {
  // Show custom popups.
  $( '.EventPopup' ).on( 'click', function() {
    var infoContainer = document.getElementById( this.getAttribute( 'href' ).substr(1) );
    var html = infoContainer.innerHTML;

    $.popup (
      {},
      html
    );
  });

  // Prefill date dropdown with current date.
  if ($('#Form_EventCalendarDate_Day').val() == 0) {
    var now = new Date();
    $('#Form_EventCalendarDate_Day').val(now.getDate());
    $('#Form_EventCalendarDate_Month').val(now.getMonth() + 1);
    $('#Form_EventCalendarDate_Year').val(now.getFullYear());
  }

  // Toggle visibility of Calendar date depending on #Form_CategoryID.
  var EventCalendarCategoryIDs = gdn.definition('EventCalendarCategoryIDs');
  if ( typeof EventCalendarCategoryIDs !== 'undefined' ) {
    EventCalendarCategoryIDs = jQuery.parseJSON(EventCalendarCategoryIDs);
    $('#Form_CategoryID').change(function() {
      if (EventCalendarCategoryIDs.indexOf($('#Form_CategoryID').val()) > -1) {
        $('.EventCalendarInput').removeClass('Hidden');
      } else {
        $('.EventCalendarInput').addClass('Hidden');
      }
    });
  }
});
