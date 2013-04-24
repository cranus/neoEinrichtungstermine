<?php
/**
 * Created by JetBrains PhpStorm.
 * User: johannesstichler
 * Date: 24.04.13
 * Time: 10:20
 * To change this template use File | Settings | File Templates.
 */
require_once 'app/controllers/authenticated_controller.php';
class start extends \StudipController {
	function before_filter(&$action, &$args)
	{
		$this->flash = Trails_Flash::instance();
		// set default layout
		$layout = $GLOBALS['template_factory']->open('layouts/base');
		//$layout =  "ajax/layout";
		$this->set_layout($layout);
		PageLayout::addScript( $this->flash->vmurl  . '/assets/js/start.js');

	}

	public function index_action() {

	}


}