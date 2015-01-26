test( 'DataManager()', function(){
	var Completed = false, dm;

	dm = new DataManager()
	dm.RequestCompleteEvent = function(Req){
		Completed = true;
		equals(Req.responseText, 'Marco,Mark,Max_B', 'test Request.ResponseText');
	};
	dm.RequestFailedEvent = function(Req){
		Completed = false;
		equals(this.Param, 'test', 'test Parm')
	};
	dm.Param = 'test';


	function checkError() {
		dm.LoadData('badRequest.html');
		setTimeout(function(){
			ok(!Completed, 'test RequestFailedEvent');
			start();
		}, 2000);
	}

	function checkSuccess() {
		dm.LoadData('getNames.html');
		setTimeout(function(Req){
			ok(Completed, 'test RequestCompleteEvent');
			checkError();
		}, 2000);
	}

	expect(4);
	stop();
	checkSuccess();
});

test( 'HandleFailure()', function(){
	var dm, backUpAlert;

	backUpAlert = window.alert;
	window.alert = function(msg){
		equals(msg, "Failed: (404) Not Found");
		start();
		window.alert = backUpAlert;
	};
	dm = new DataManager()
	dm.RequestCompleteEvent = function(Req){};
	dm.RequestFailedEvent = HandleFailure
	dm.Param = 'test';

	expect(1);
	stop();
	dm.LoadData('badRequest.html');
});