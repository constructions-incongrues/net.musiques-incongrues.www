/**
 * An easy way to create elements for testing so that tests don't have to share elements.
 * @param {string} id
 * @param {string} html
 */
Fixture = function(id, html) {
	this.id = id || this.id;
	if (html) this.set(html);

	return this;
};
/**
 * id of the container
 * @param {string}
 */
Fixture.prototype.id = 'fixtures';


/**
 * Last set of html set. allow to easely reset the content while a test.
 * @private
 * @param {string}
 */
Fixture.prototype.lastSet = '';

/**
 * set to true if the endTest doesn't need to retart the test
 */
Fixture.prototype.restarted = false;

/**
 * Create the fixture
 * @param {string} html code to create.
 */
Fixture.prototype.set = function(html) {
	this.lastSet = html;
	$('<div id="' + this.id +'"><div>').appendTo('body').html(html);
};

/**
 * Reset the fixture to the last set
 */
Fixture.prototype.reset	= function() {
	$('#' + this.id).html(this.lastSet);
};

/**
 * empty the fixture container
 */
Fixture.prototype.empty = function() {
	$('#' + this.id).remove();
};

/**
 * Remove fixture elements and restart test if needed.
 */
Fixture.prototype.endTest = function(timeOut) {
	var that = this;

	function cleanUp() {
		if (timeOut && !that.restarted) {
			start();
		}
		that.empty();
	}

	if (timeOut) {
		setTimeout(cleanUp, timeOut);
	} else {
		cleanUp()
	}
}

test('Fixture()', function(){
	var backUpStart, fixture = new Fixture('Fixture', '<div id="testFixture">test</div>');

	expect(4);
	stop();

	equals($('#Fixture').get().length, 1, 'test constructor');

	$('#testFixture').text('testing...');
	fixture.reset();
	equals($('#testFixture').text(), 'test', 'testing reset');

	// to test how many time start is called
	backUpStart = window.start;
	window.start = function(){ ok(true, 'start called'); };
	function cleanUp() {
		window.start = backUpStart;
		window.start();
	}

	fixture.endTest(50);
	setTimeout(testEndTest, 100);

	function testEndTest(){
		//start should have been called

		// empty as well
		equals($('#Fixture').get().length, 0, 'test empty()');

		//test endTest() when restarted is set to true
		fixture.restarted = true
		fixture.endTest(50);
		setTimeout(cleanUp, 100);
	}
});

/**
 * Trigger an event
 * @param {element} obj
 * @param {string} eventName
 * @param {string} eventType	MouseEvents|KeyboardEvent
 * @param (string} key 			key	pressed for KeyboardEvent
 */
function triggerEvent(obj, eventName, evenType, key){
	var t, e;

	if (obj.fireEvent) {
		e = document.createEventObject();
		// ie way
		if (key) {
			e.keyCode = key ;
		}
		obj.fireEvent('on' + eventName, e);
	} else if (obj.dispatchEvent) {
		// w3c way
		t = evenType || 'MouseEvents';
		e = document.createEvent( t );
		if (t === 'MouseEvents' ) {
			e.initMouseEvent(
			  eventName,    // the type of mouse event
			  true,       // do you want the event to
						  // bubble up through the tree?  (sure)
			  true,       // can the default action for this
						  // event, on this element, be cancelled? (yep)
			  window,     // the 'AbstractView' for this event,
						  // which I took to mean the thing sourcing
						  // the mouse input.  Either way, this is
						  // the only value I passed that would work
			  1,          // details -- for 'click' type events, this
						  // contains the number of clicks. (single click here)
			  1,          // screenXArg - I just stuck 1 in cos I
						  // really didn't care
			  1,          // screenYArg - ditto
			  1,          // clientXArg - ditto
			  1,          // clientYArg - ditto
			  false,      // is ctrl key depressed?
			  false,      // is alt key depressed?
			  false,      // is shift key depressed?
			  false,      // is meta key depressed?
			  0,          // which button is involved?
						  // I believe that 0 = left, 1 = right,
						  // 2 = middle
			  obj	      // the originator of the event
						  // if you wanted to simulate a child
						  // element firing the event you'd put
						  // its handle here, and call this method
						  // on the parent catcher.  In this case,
						  // they are one and the same.
			);
		} else if (t === 'KeyboardEvent') {
			key = key || 0;
			e.initKeyEvent (
				eventName,
				true,
				true,
				window,
				false,      // is ctrl key depressed?
				false,      // is alt key depressed?
				false,      // is shift key depressed?
				false,      // is meta key depressed?
				key,
				0
			);
		}
		obj.dispatchEvent(e);
	} else if (jQuery) {
		// jQuery way
		jQuery(obj).triggerHandler(eventName);
	}
}

/**
 * Replace DataManager object to check parameter given to it are ok.
 * @param {string} expUrl expected url to test for
 * @param {string} expParam expected Param to test for
 * @param {Function} LoadFonction called by the default LoadData
 */
function setFauxDataManager(expUrl, expParm, LoadFunction) {
	var backUp = window.DataManager;

	window.DataManager = function(){
			this.Param = '';
			this.RequestFailedEvent = '';
			this.RequestCompleteEvent = '';
			this.LoadData = function(url) {
				if (expUrl) {
					equals( url, expUrl, 'check url' );
				}

				if (expParm) {
					equals(this.Param, expParm, 'check Preference Name' );
				}

				if (LoadFunction) {
					LoadFunction.call(this);
				}

				window.DataManager = backUp;
			};

			return this;
	}
};