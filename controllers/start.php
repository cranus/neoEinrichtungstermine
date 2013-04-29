<?php
/**
 * Created by JetBrains PhpStorm.
 * User: johannesstichler
 * Date: 24.04.13
 * Time: 10:20
 * To change this template use File | Settings | File Templates.
 */
require_once 'app/controllers/authenticated_controller.php';
require_once dirname(__FILE__).'/../modells/vldaten.php';
require_once 'lib/calendar/CalendarColumn.class.php';
require_once 'lib/calendar/CalendarView.class.php';
class startController extends \StudipController {
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
		$this->instid = $this->flash->instid;
		$vldaten = new vldaten();
		$termine = $vldaten->getAllVlsWeek();
		foreach($termine as $t) {
			$name = $t["Name"];
			$start = date("Hi", $t['date']);
			$ende = date("Hi", $t['end_time']);
			$weekday = date("N", $t['date']);
			$entry[$weekday][] = array(
				'id' => md5(uniqid()),
				'color' => "#000000",
				'start' => $start,
				'end' => $ende,
				'title' => $name,
				'onClick' => "function() { showdetails('".$t["id"]."'); }"
			);
		}
		$this->plan = $this->renderPlan($entry);
	}


	public function dayview_action() {
		if(isset($_REQUEST["day"])) $day = time()+($_REQUEST["day"]*86400);
		else $day = time();
		$this->instid = $this->flash->instid;
		$vldaten = new vldaten();
		$termine = $vldaten->getAllVlsDay($day);
		foreach($termine as $t) {
			$name = $t["Name"];
			$start = date("Hi", $t['date']);
			$ende = date("Hi", $t['end_time']);
			$weekday = date("N", $t['date']);
			$entry[$weekday][] = array(
				'id' => md5(uniqid()),
				'color' => "#000000",
				'start' => $start,
				'end' => $ende,
				'title' => $name,
				'onClick' => "function() { showdetails('".$t["id"]."'); }"
			);
		}
		$this->plan = $this->renderPlan($entry);
	}

	function renderPlan($termine) {
		$plan = new CalendarView();
		$plan->setRange("6","21");


		if(sizeof($termine[1]) > "0") {
			$plan->addColumn(_('Montag'));
			foreach($termine[1] as $date) $plan->addEntry($date);
		}


		if(sizeof($termine[2]) > "0") {
			$plan->addColumn(_('Dienstag'));
			foreach($termine[2] as $date) $plan->addEntry($date);
		}


		if(sizeof($termine[3]) > "0") {
			$plan->addColumn(_('Mittwoch'));
			foreach($termine[3] as $date) $plan->addEntry($date);
		}


		if(sizeof($termine[4]) > "0") {
			$plan->addColumn(_('Donnerstag'));
			foreach($termine[4] as $date)
				$plan->addEntry($date);
		}


		if(sizeof($termine[5]) > "0") {
			$plan->addColumn(_('Freitag'));
			foreach($termine[5] as $date) {
				$plan->addEntry($date);
			}
		}

		if(sizeof($termine[6]) > "0") {
			$plan->addColumn(_('Samstag'));
			foreach($termine[6] as $date) $plan->addEntry($date);
		}
		$plaene =  $plan->render();

		return $plaene;
	}




}