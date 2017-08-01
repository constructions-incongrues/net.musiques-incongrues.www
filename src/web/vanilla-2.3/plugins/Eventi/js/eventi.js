jQuery(document).ready(function($) {
   // On mouseenter of eventi flag, handle some text selection, and fix 
   // position of tooltip when near screen edge. 
   // 
   // Use event delegation to guarantee that eventi elements added to the 
   // page after load (asynchronously, for example) still perform 
   // their actions.
   $('body').on('mouseenter', '.eventi', function(e) {
      var tooltip = $(this).find('.eventi-tooltip');
      var eventiInputs = tooltip.find('input');
      var eventiName = tooltip.find('.eventi-event-name');
      
      // On mouseover of inputs, text select it. Remove event before 
      // re-attaching so that event does not multiply.
      eventiInputs.off('mouseover').on('mouseover', function(e) {
         $(this)[0].select();
      });
      
      // Delay so that text selection can be seen, thus providing a hint 
      // to the end-user that they can copy the event name text.
      setTimeout(function() {
         eventiName[0].select();
      }, 300);
      
      // Remove fragment identifier (#) in URL, if any (from linking), so that 
      // the linked to flag does not always stay visible.
      if (location.hash.indexOf('eventi') > -1) {
         // Only remove it if first hovered over it, then it will behave like 
         // all the others
         var stickiedFlagId = location.hash.substring(1).toString();
         var hoverFlagId = $(e.target).closest('.eventi').attr('id').toString();

         if (hoverFlagId == stickiedFlagId) {
            // Make sure scroll position is maintained
            var originalScrollTop = document.body.scrollTop;
            location.hash = '';
            document.body.scrollTop = originalScrollTop;
         }
      }
      
      // Determine whether tooltip is hitting upon the viewport edges.
      var tooltipWidth = tooltip.outerWidth();
      var tooltipHeight = tooltip.outerHeight();

      // Note: if both the left edge and bottom edge are hit, the styles will 
      // cascade just fine. Both classes will work together and the arrow 
      // will be set properly as well.

      // You've hit the right edge of the browser window, so push it to the 
      // left side, within the viewable viewport.
      if (Math.ceil(tooltip.offset().left + tooltipWidth) > $(window).width()) {
         tooltip.addClass('eventi-tooltip-left');
      }

      //console.log(tooltip.offset().top-e.pageY+tooltipHeight, $(window).height());
      // You've hit the bottom edge of the browser window, so push it to the 
      // top side, within the viewable viewport.
      // TODO at the moment this will correctly calculate at bottom of page, 
      // though it would be good to have it calculate within visible viewport, 
      // regardless of its scroll position in page.
      if (Math.ceil(tooltip.offset().top + tooltipHeight) >= $(document).height()) {
      //if (Math.ceil(tooltip.offset().top-e.pageY+tooltipHeight) >= $(window).height()) {
         tooltip.addClass('eventi-tooltip-top');
         tooltip.css({
            "top": -tooltipHeight - 30
         });
      }
   });
   
   /**
    * Allow Eventi flags to be hidden without disabling the plugin in dashboard.
    * This section is for the Eventi control panel, which is fixed to the 
    * bottom-right of the page.
    */ 
   $('body').append('<label class="eventi-cp" title="Check this box to hide or show the Eventi flags on the page."><span>Hide Eventi</span><input type="checkbox" id="eventi-cp-checkbox" /></label>');

   // On DOM ready check local storage to see Eventi flags are hidden or visible
   if (parseInt(localStorage.getItem('eventiShow')) == 1) {
      $('body').addClass('eventi-show');
      $('#eventi-cp-checkbox')[0].checked = false;
   } else {
      $('body').removeClass('eventi-show');
      $('#eventi-cp-checkbox')[0].checked = true;
   }

   // Changing whether Eventi flags are hidden or visible
   $('#eventi-cp-checkbox').on('change', function(e) {
      if (!e.target.checked) {
         $('body').addClass('eventi-show');
         localStorage.setItem('eventiShow', '1');
      } else {
         $('body').removeClass('eventi-show');
         localStorage.setItem('eventiShow', '0');
      }
   });
});