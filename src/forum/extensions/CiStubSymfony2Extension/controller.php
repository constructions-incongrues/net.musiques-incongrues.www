<?php
use \Symfony\Component\Routing\Route;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

$routes->add('stub', new Route('/stub', array('_controller' =>
	function (Request $request) {
		$Page->AddRenderControl(new StubPage(), $Configuration["CONTROL_POSITION_BODY_ITEM"]);
	}
)));

class StubPage
{
	public function render() {
		echo "RAOUL";
	}	
}
