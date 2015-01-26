test( 'AddLabelValuePair()', function() {
	var container, fixture = new Fixture('AddLabelValuePair');

	fixture.set('<form action="#">' +
			'<div><input type="hidden" name="LabelValuePairCount" id="LabelValuePairCount" value="1"/></div>' +
			'<ul id="CustomInfo">' +
			'<li><input type="text" name="Label1" maxlength="20" class="LVLabelInput"/></li>' +
			'<li><input type="text" name="Value1" maxlength="200" class="LVValueInput"/></li>' +
			'</ul>' +
			'</form>');

	AddLabelValuePair();
	expect(13);

	equals($('#LabelValuePairCount').attr('value'), 2, 'test counter');

	$('#CustomInfo li input').each(function(i, el){

		function check(name, maxLength, className){
			equals(el.name, name, 'check name');
			equals(el.maxLength, maxLength, 'check max length');
			equals(el.className, className, 'check class name');
		}

		switch(i){
			case 0:
				check('Label1', 20, 'LVLabelInput');
				break;
			case 1:
				check('Value1', 200, 'LVValueInput');
				break;
			case 2:
				check('Label2', 20, 'LVLabelInput');
				break;
			case 3:
				check('Value2', 200, 'LVValueInput');
				break;
			default:
				ok(false, 'there should not have more than 4 element');
				break;
		}

	});

	fixture.empty();

});

test( 'DiscussionSwitch()', function() {
	var fixture = new Fixture('DiscussionSwitch');

	fixture.set('<p><a href="#" id="HideDiscussion">test</a></p>');

	setFauxDataManager(
		'ajax/switch.php?Type=Active&DiscussionID=SomeId&Switch=1&PostBackKey=TestPostBackKey',
		'', function(){
			equals(this.RequestCompleteEvent, RefreshPageWhenAjaxComplete, 'test complete event');
			equals(this.RequestFailedEvent, HandleFailure, 'test fail event');
			ok($('#HideDiscussion').hasClass('Progress'), 'test sender class');
			fixture.empty();
		});

	expect(4);
	DiscussionSwitch(
		'ajax/switch.php', 'Active', 'SomeId', '1', 'HideDiscussion', 'TestPostBackKey'
	);
});

test( 'HideComment()', function() {
	var fixture = new Fixture('DiscussionSwitch'), confirmBackUp;

	fixture.set('<p><a href="#" id="testHideComment">test</a></p>');

	confirmBackUp = window.confirm;
	window.confirm = function(text){
		equals(text, 'hide', 'confirme text');
		return true;
	}

	setFauxDataManager(
		'hide.php?Type=Comment&Switch=1&DiscussionID=SomeId&CommentID=OtherId&PostBackKey=TestPostBackKey',
		'',
		function(){
			equals(this.RequestCompleteEvent, RefreshPageWhenAjaxComplete, 'test complete event');
			equals(this.RequestFailedEvent, HandleFailure, 'test fail event');
			ok($('#testHideComment').hasClass('HideProgress'), 'test sender class');
			equals($('#HideDiscussion').text(), '', 'test sender content');
			fixture.empty();
			window.confirm = confirmBackUp;
		}
	);

	expect(6);
	HideComment('hide.php', '1', 'SomeId', 'OtherId', 'show', 'hide', 'testHideComment', 'TestPostBackKey');
});

test( 'SetBookmark()', function() {
	var fixture = new Fixture('testSetBookmark');

	fixture.set('<a href="#" id="SetBookmark">Bookmark it</a>');

	// build a call back function for setFauxDataManager
	function check(switchValue, comments) {
			var text;
			switchValue = switchValue || '1';
			comments = comments || '';
			text = switchValue === '1' ? 'Unbookmark it' : 'Bookmark it';
			return function(){
				equals($('#SetBookmark').attr('name'), switchValue, 'check link name attribute' + comments);
				equals(this.Param, text, 'check Parameters' + comments);
				equals(this.RequestCompleteEvent, BookmarkComplete, 'check complete event' + comments);
				equals(this.RequestFailedEvent, BookmarkFailed, 'check fail event' + comments);
			}
	}

	expect(18);
	// bookmark it test
	setFauxDataManager(
		'bookmark.php?Type=Bookmark&Switch=1&DiscussionID=100&PostBackKey=TestPostBackKey',
		'Unbookmark it',
		check('1')
	);

	SetBookmark('bookmark.php', '0', '100', 'Bookmark it', 'Unbookmark it', 'TestPostBackKey');

	// unbookmark it using the link name attribute
	setFauxDataManager(
		'bookmark.php?Type=Bookmark&Switch=0&DiscussionID=100&PostBackKey=TestPostBackKey',
		'Bookmark it',
		check('0', ' (using link name attribute)')
	);
	SetBookmark('bookmark.php', '0', '100', 'Bookmark it', 'Unbookmark it', 'TestPostBackKey');

	// bookmark it using the link name attribute
	setFauxDataManager(
		'bookmark.php?Type=Bookmark&Switch=1&DiscussionID=100&PostBackKey=TestPostBackKey',
		'Unbookmark it',
		function() {
			check('1', ' (using link name attribute)').call(this);
			fixture.empty();
		}
	);
	SetBookmark('bookmark.php', '0', '100', 'Bookmark it', 'Unbookmark it', 'TestPostBackKey');

});

test( 'ApplyBookmark()', function() {
	var el, fixture = new Fixture('ApplyBookmark');

	fixture.set('<a href="#" id="SetBookmark" class="someClass">Bookmark it</a>');

	// set the class and text of bookmark/unbookmark link
	ApplyBookmark('SetBookmark', 'completed', 'Unbookmark it');

	el = $('#SetBookmark');
	equals(el.attr('class'), 'completed', 'check class of the bookmark link');
	equals(el.text(), 'Unbookmark it', 'check label of link');

	fixture.empty();
});

test( 'BookmarkComplete()', function() {
	var el, fixture = new Fixture('BookmarkComplete');

	fixture.set('<a href="#" id="SetBookmark" class="someClass">Bookmark it</a>');

	expect(2);
	stop();

	function check() {
		el = $('#SetBookmark');
		equals(el.attr('class'), 'Complete', 'check class of the bookmark link');
		equals(el.text(), 'Unbookmark it', 'check label of link');
		fixture.empty();
		start();
	}

	BookmarkComplete.call({Param: 'Unbookmark it'});

	setTimeout(check, 500);
});

test( 'BookmarkFailed()', function() {
	var alert, fixture = new Fixture('BookmarkFailed');

	fixture.set('<a href="#" id="SetBookmark" class="someClass">Bookmark it</a>');


	// BookmarkFailed should alert the user about an error,
	// And change the bookmark/unbookmark class
	expect(2);

	// Overwrtite window.alert
	alert = window.alert;
	window.alert = function(msg) {
		equals(msg, 'Failed: (500) Internal server error', 'test alert message');
	};
	BookmarkFailed({status: '500', statusText: 'Internal server error'});

	equals($('#SetBookmark').attr('class'), 'Complete', 'check class of the bookmark link');

	window.alert = alert;
	fixture.empty();
});

test( 'ShowAdvancedSearch()', function() {
	var ft = new Fixture('ShowAdvancedSearch');

	ft.set(	'<div id="testShowAdvancedSearch">' +
			'<div id="SearchSimpleFields">simple</div>' +
			'<div id="SearchDiscussionFields" style="display:none">discussion</div>' +
			'<div id="SearchCommentFields" style="display:none">conmment</div>' +
			'<div id="SearchUserFields" style="display:none">user</div>' +
			'</div>');

	expect(4);
	ShowAdvancedSearch();
	$("#testShowAdvancedSearch div").each(function(i, el){
		if (el.id == 'SearchSimpleFields') {
			ok($(el).is(':hidden'), 'check simple form is hidden');
		} else {
			ok($(el).is(':visible'), 'check advanced forms are visible');
		}
	});

	ft.empty();
});

test( 'ShowSimpleSearch()', function() {
	var ft = new Fixture('ShowSimpleSearch');

	ft.set(	'<div id="testShowSimpleSearch">' +
			'<div id="SearchSimpleFields" style="display:none">simple</div>' +
			'<div id="SearchDiscussionFields">discussion</div>' +
			'<div id="SearchCommentFields">conmment</div>' +
			'<div id="SearchUserFields">user</div>' +
			'</div>');

	expect(4);
	ShowSimpleSearch();
	$("#testShowSimpleSearch div").each(function(i, el){
		if (el.id == 'SearchSimpleFields') {
			ok($(el).is(':visible'), 'check simple form is visible');
		} else {
			ok($(el).is(':hidden'), 'check advanced forms are hidden');
		}
	});

	ft.empty();
});

test( 'ToggleCategoryBlock()', function() {
	var ft = new Fixture('ToggleCategoryBlock');

	ft.set('<a id="blockCat1" href="#">Block Category</a>');

	setFauxDataManager(
		'block.php?BlockCategoryID=10&Block=1&PostBackKey=TestPostBackKey',
		'',
		function(){
			equals(this.RequestCompleteEvent, RefreshPageWhenAjaxComplete, 'check complete event');
			equals(this.RequestFailedEvent, HandleFailure, 'check fail event');
		}
	);

	expect(5);
	ToggleCategoryBlock('block.php', 10, 1, 'blockCat1', 'TestPostBackKey');

	equals($('#blockCat1').get(0).innerHTML, '&nbsp;', 'check content of blocker link');
	equals($('#blockCat1').attr('class'), 'HideProgress', 'check class of blocker link');
});

test( 'ToggleCommentBox()', function() {
	var ft = new Fixture('ToggleCommentBox');

	ft.set(	'<a href="#" id="CommentBoxController">bigger<a/>' +
			'<textarea id="CommentBox" class="SmallCommentBox"></textarea>'
	);
	expect(10);

	setFauxDataManager(
		'switch.php?Type=ShowLargeCommentBox&Switch=1&PostBackKey=PostBackKey',
		'',
		function() {
			equals(this.RequestCompleteEvent, ToggleCommentBoxComplete, 'check complete event');
			equals(this.RequestFailedEvent, HandleFailure, 'check fail event');
		}
	);


	ToggleCommentBox('switch.php', 'smaller', 'bigger', 'PostBackKey');
	equals($('#CommentBoxController').text(), 'smaller', 'check controller text');
	equals($('#CommentBox').attr('class'), 'LargeCommentBox', 'check box class');

	setFauxDataManager(
		'switch.php?Type=ShowLargeCommentBox&Switch=0&PostBackKey=PostBackKey',
		'',
		function() {
			equals(this.RequestCompleteEvent, ToggleCommentBoxComplete, 'check complete event');
			equals(this.RequestFailedEvent, HandleFailure, 'check fail event');
		}
	);


	ToggleCommentBox('switch.php', 'smaller', 'bigger', 'PostBackKey');
	equals($('#CommentBoxController').text(), 'bigger', 'check controller text');
	equals($('#CommentBox').attr('class'), 'SmallCommentBox', 'check box class');

	ft.empty();
});

test( 'ToggleCommentBoxComplete()', function() {
	// this is doing nothing
});

test( 'WhisperBack()', function() {
	var ft = new Fixture('WhisperBack')

	ft.set('<form id="frmPostComment" action="#">' +
			'<input type="text" name="WhisperUsername" id="frmInput"/>' +
			'<textarea name="Body" id="frmBody"></textarea>' +
			'</form>');
	expect(2);
	stop();
	// test textarea is in focus
	$('#frmBody').bind('focus', function(){ ok(true, 'test the textarea is in focus'); });

	WhisperBack('20', 'Mark', '/foo/');
	equals($('#frmInput').attr('value'), 'Mark', 'test input value');

	function cleanUp() { ft.empty(); start(); }

	setTimeout(cleanUp, 100);
	// how could I check the redirect part of this fonction (when the reply form is not on the page)
});