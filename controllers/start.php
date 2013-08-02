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
		$this->instid = (!empty($_GET["cid"]) ? $_GET["cid"] : (empty($SessSemName[1]) ? $_GET["auswahl"] : $SessSemName[1]));
		PageLayout::addScript($this->flash->net->url . 'neo/neoeinrichtungstermine/assets/neoET.js');
	}

	public function index_action() {

		$day = $this->getDate();
		$this->instid = $this->flash->instid;
        $inst = new Institute($this->instid);
        PageLayout::setTitle($inst->Name.' - Einrichtungstermine / Wochenansicht');

		//Montag errechnen
		$tag = date("N", $day);
		$montag = $day - 86400*($tag - 1);
		$this->start= $montag;
		$this->end = $montag+(86400*6);

		$vldaten = new vldaten();
		$termine = $vldaten->getAllVlsWeek($day, $montag);
		$entry = array(1 => array() ,2 => array(),3 => array(),4 => array(),5 => array(),6 => array(),7 => array());
		foreach($termine as $t) {
			$typ = $this->DateTypToHuman($t["date_typ"]);
			$name = $t["Name"]." - ".$typ["name"];
			$start = date("Hi", $t['date']);
			$ende = date("Hi", $t['end_time']);
			$weekday = date("N", $t['date']);
			$entry[$weekday][] = array(
				'id' => md5(uniqid()),
				'color' => $typ["color"],
				'start' => $start,
				'end' => $ende,
				'title' => $name,
				'onClick' => "function() {  showdetails('".$t["termin_id"]."');}"
			);
		}
		$this->plan = $this->renderPlan($entry);


	}


	public function dayview_action() {
		$day = $this->getDate();
		$this->flash->debug = "Inst-Id: " . $this->flash->instid . " -> " . $this->instid;
		$this->flash->start = $day;
		$this->instid = $this->flash->instid;
        $inst = new Institute($this->instid);
        PageLayout::setTitle($inst->Name.' - Einrichtungstermine / Tagesansicht');
		$vldaten = new vldaten();
		$termine = $vldaten->getAllVlsDay($day, $this->instid);
		foreach($termine as $t) {
			$typ = $this->DateTypToHuman($t["date_typ"]);
			$name = $t["Name"]." - ".$typ["name"];
			$start = date("Hi", $t['date']);
			$ende = date("Hi", $t['end_time']);
			$weekday = date("N", $t['date']);
			$entry[$weekday][] = array(
				'id' => md5(uniqid()),
				'color' => $typ["color"],
				'start' => $start,
				'end' => $ende,
				'title' => $name,
				'onClick' => "function() { showdetails('".$t["termin_id"]."');}"
			);
		}
		//$this->debug = $this->flash->debug;
		$this->plan = $this->renderPlan($entry, "day");

	}



	function renderPlan($termine, $plantyp = "week") {
		$plan = new CalendarView();
		$plan->setRange("6","21");


		if(sizeof($termine[1]) > "0" OR $plantyp == "week") {
			$plan->addColumn(_('Montag'));
			foreach($termine[1] as $date) $plan->addEntry($date);
		}


		if(sizeof($termine[2]) > "0" OR $plantyp == "week") {
			$plan->addColumn(_('Dienstag'));
			foreach($termine[2] as $date) $plan->addEntry($date);
		}

		if(sizeof($termine[3]) > "0" OR $plantyp == "week") {
			$plan->addColumn(_('Mittwoch'));
			foreach($termine[3] as $date) $plan->addEntry($date);
		}


		if(sizeof($termine[4]) > "0" OR $plantyp == "week") {
			$plan->addColumn(_('Donnerstag'));
			foreach($termine[4] as $date) 	$plan->addEntry($date);
		}


		if(sizeof($termine[5]) > "0" OR $plantyp == "week") {
			$plan->addColumn(_('Freitag'));
			foreach($termine[5] as $date)  $plan->addEntry($date);

		}

		if(sizeof($termine[6]) > "0") {
			$plan->addColumn(_('Samstag'));
			foreach($termine[6] as $date) $plan->addEntry($date);
		}
		$plaene =  $plan->render();

		return $plaene;
	}

	public function DateTypToHuman($typ) {
		switch($typ) {
			case 1: $return = array("name"=>_("Vorlesung"), "sitzung"=>1, "color"=>"#2D2C64"); break;
			case 2: $return = array("name"=>_("Vorbesprechung"), "sitzung"=>0, "color"=>"#5C2D64"); break;
			case 3: $return = array("name"=>_("Klausur"), "sitzung"=>0, "color"=>"#526416"); break;
			case 4: $return = array("name"=>_("Exkursion"), "sitzung"=>0, "color"=>"#505064"); break;
			case 5: $return = array("name"=>_("Neuer Termin / Ersatztermin"), "sitzung"=>1, "color"=>"#41643F"); break;
			case 6: $return = array("name"=>_("Ausfall / cancelled"), "sitzung"=>0, "color"=>"#E60005"); break;
			case 7: $return = array("name"=>_("Sitzung"), "sitzung"=>1, "color"=>"#627C95"); break;
			case 8: $return = array("name"=>_("Sondertermin"), "sitzung"=>1, "color"=>"#2D2C64"); break;
			case 9: $return = array("name"=>_("Freiwillig"), "sitzung"=>1,  "color"=>"#6c6c6c"); break;
			default: $return = array("name"=>_("Vorlesung"), "sitzung"=>1, "color"=>"#2D2C64");
		}
		return $return;

	}

	private function DatumToDate($datum) {
		return mktime("00","00","01",$datum["3"].$datum["4"],$datum["0"].$datum["1"],$datum["6"].$datum["7"].$datum["8"].$datum["9"]);

	}

	/*
	 * Erstellt aus den m?glichen ?bergebenen Werten das Datum
	 *
	 * return array Anfangs und End Datum
	 */

	private function getDate() {
		if(isset($_REQUEST["day"])) {
			if($_REQUEST["datum"]) {
				$day = $this->DatumToDate($_REQUEST["datum"])+($_REQUEST["day"]*86400);
			}
			else $day = time()+($_REQUEST["day"]*86400);

		}
		elseif($_REQUEST["datum"]) $day = $this->DatumToDate($_REQUEST["datum"]);
		else $day = time();
		return $day;
	}



}