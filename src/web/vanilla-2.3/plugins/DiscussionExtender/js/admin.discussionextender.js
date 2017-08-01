/* Copyright 2014 Zachary Doll */
jQuery(document).ready(function($) {

	function GetDiscussionPreview() {
		var url = gdn.url('/post/discussion');
		
		$.ajax({
			url: url,
			global: false,
			type: "GET",
			data: {DeliveryType: 'VIEW'},
			success: function(Data){
        $('#NewDiscussionPreview').html(Data);
        $('#DiscussionForm form').attr('action', '#');
			}
		});
	}

  GetDiscussionPreview();
  
  $(document).on('click', '#WipeTick input', function() {
    $('#WipeWarning').toggleClass('Hidden');
  });
  
  // Hide the options field unless drop down is selected as type
  $('body').on('popupReveal', function() {
    if($("select[name='Type']").val() != "Dropdown") {
      $('form .DE_Options').hide();
    }
    
    $("select[name='Type']").change(function() {
      if($("select[name='Type']").val() != "Dropdown") {
        $('form .DE_Options').hide('slow');
      }
      else {
        $('form .DE_Options').show('slow');
      }
    });
  });
  
});