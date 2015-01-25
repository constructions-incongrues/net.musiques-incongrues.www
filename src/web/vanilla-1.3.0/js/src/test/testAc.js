test( 'AutoComplete ()', function() {
	// Can't work on ie, can't trigger the keybord event on the element
	if (window.event) {
		var ac, i, fixture = new Fixture('AutoComplete');

		fixture.set('<form action="#"><input id="testAutoComplete" name="testAutoComplete" type="text"/></form>');

		expect(1);
		stop();
		ac = new AutoComplete('testAutoComplete', false);
		ac.TableID = 'resultAutoComplete';
		ac.KeywordSourceUrl = 'getNames.html?search='

		// Simulate user input
		i = $('#testAutoComplete').get(0);
		i.focus();
		$(i).attr('value', 'm');

		triggerEvent(i, 'keydown', 'KeyboardEvent', 77);

		// check the list of name appear in the list
		function checkTable(){
			equals($('#resultAutoComplete').text(), 'MarcoMarkMax_B');
			start()
		}
		// delay need to be long enought.
		setTimeout(checkTable, 3000);
	}

});

test('String.prototype.addslashes()', function(){
	equals("\".|[]^*+?$()\\".addslashes(), "\\\"\\.\\|\\[\\]\\^\\*\\+\\?\\$\\(\\)\\\\");
	equals("Mark O'Sullivan".addslashes(), "Mark O\\\'Sullivan")
});

test('String.prototype.trim()', function(){
	equals(' test 	'.trim(), 'test');
	equals('test test'.trim(), 'test test');
});

test( 'addEvent()', function() {
	var t, fixture = new Fixture('addEvent');

	fixture.set('<input id="testAddEvent" name="testAddEvent" value="test addEvent"/>');
	t = $('#testAddEvent').get(0);

	expect(4);
	stop();

	// event action
	function check(){
		ok(true);
	}

	// test click event
	addEvent(t,'click', check);
	triggerEvent(t, 'click');

	// test focus even
	addEvent(t, 'focus', check);
	t.focus();


	// test keydown event
	addEvent(document, 'keydown', function(e){
		equals(e.keyCode, 77, 'check keyCode');
		check();
	});
	triggerEvent(document, 'keydown', 'KeyboardEvent', 77);

	function cleanUp(){
		start();
		fixture.empty();
	}
	setTimeout(cleanUp, 200);

});

test( 'removeEvent()', function() {
	var t, fixture = new Fixture('removeEvent');

	fixture.set('<div id="testAddEvent">test addEvent</div>');
	t = $('#testAddEvent').get(0);
	expect(1);
	stop();

	function checkRemoveEvent() {
		ok(true, 'should be only one test');
	}
	addEvent(t,'click', checkRemoveEvent);
	triggerEvent(t, 'click');


	removeEvent(t, 'click', checkRemoveEvent);

	function stopTest() {
			start();
			fixture.empty();
	}
	triggerEvent(t, 'click');
	setTimeout(stopTest, 200);
});

test( 'stopEvent()', function() {
	var a, i, fixture = new Fixture('stopEvent');

	expect(1)
	stop();

	fixture.set('<iframe src="http://getvanilla.com/" name="testIframe" id="testIframe" width="200px" height="200px">'
		+ '</iframe><a href="http://www.google.co.uk/" target="testIframe" id="testStopEvent">test</a>');
	a = $('#testStopEvent').get(0);

	function stopIframeLoad(e){
		stopEvent(e);
	}

	addEvent(a, 'click', stopIframeLoad);
	triggerEvent(a, 'click');

	function checkLocation() {
		equals(
			$('#testIframe').attr('src'),
			'http://getvanilla.com/',
			'check the page location as not changed');
		start();
		fixture.empty();
	}
	setTimeout(checkLocation, 500);

});

test( 'getElement()', function() {
	var p, fixture = new Fixture('getElement');

	fixture.set('<p id="testGetElement"><span id="testSrc">test</span></p>');
	expect(1);
	stop();

	p = $('#testGetElement').get(0);
	addEvent(p, 'click', function(e){
		if (window.event){
			equals(getElement(e).id, $('#testSrc').attr('id'));
		} else {
			equals(getElement(e).id, p.id);
		}
		start();
		fixture.empty();
	});
	triggerEvent($('#testSrc').get(0), 'click');
});

test('getTargetElement()', function(){
	var p, fixture = new Fixture('getTargetElement');

	fixture.set('<p id="testGetTargetElement"><span id="testSrc">test</span></p>');
	expect(1);
	stop();

	p = $('#testGetTargetElement').get(0);
	addEvent(p, 'click', function(e){
		equals(getTargetElement(e).id, $('#testSrc').attr('id'));
		start();
		fixture.empty();
	});
	triggerEvent($('#testSrc').get(0), 'click');
});



test( 'stopSelect()', function() {

});

test( 'getCaretEnd()', function() {

});

test( 'getCaretStart()', function() {

});

test( 'setCaret()', function() {

});

test( 'setSelection()', function() {

});

test( 'curTop()', function() {

});

test( 'curLeft()', function() {

});

test( 'isNumber()', function() {
	ok(isNumber(3), 'test on a number');
	ok(!isNumber('5'), 'test on an array');
	ok(!isNumber([5]), 'test on number as a string');
	ok(!isNumber('one'), 'test on string');
});

test( 'replaceHTML()', function() {
	var el, fixture = new Fixture('replaceHTML');

	fixture.set('<p id="testReplaceHtml">this is a<span>test</span></p>');

	el = $('#testReplaceHtml').get(0);
	replaceHTML(el, '<em>tested</em>');

	// test the em tag is not created
	equals($('em', el).get().length, 0, 'Test there is no html');
	equals($(el).text(), '<em>tested</em>', 'test text content');

	fixture.empty();

});
