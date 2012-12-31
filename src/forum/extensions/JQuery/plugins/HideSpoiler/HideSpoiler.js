jQuery(document).ready(function($){ // allows compatibility with prototype and other libraries
 $(function(){
  // create link and hide spoiler
  $('.Hidden').before('<span class="HiddenLabel" title="Click to view Spoiler">[Hide/Show]</span>').addClass('HiddenHide');
  // toggle the spoilers
  $('span.HiddenLabel').toggle(
   function(){ $(this).next('.Hidden').removeClass('HiddenHide').addClass('HiddenShown'); },
   function(){ $(this).next('.Hidden').addClass('HiddenHide').removeClass('HiddenShown'); }
   )
 });
}); // allows compatibility with prototype and other libraries