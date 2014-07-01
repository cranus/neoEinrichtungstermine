<?php
/**
 * Datei ist Teil neoEinrichtungstermine
 * Erstellt von: johannes.stichler
 * Datum: 01.08.13
 * The MIT License (MIT)
 *
 * Copyright (c) 2013 Johannes Stichler
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
require_once 'app/controllers/authenticated_controller.php';
require_once dirname(__FILE__).'/../modells/vldaten.php';
require_once 'lib/calendar/CalendarColumn.class.php';
require_once 'lib/calendar/CalendarView.class.php';
require_once 'ajax.php';
class startController extends \StudipController {
	function before_filter(&$action, &$args)
	{
		$this->flash = Trails_Flash::instance();
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
            //Titel zusammensetzen:
            $title = $t["Name"];
            $title .= " (".$typ["name"].")";
            $raum = $vldaten->getRoomToDate($t["termin_id"]);
            if(!empty($raum)) $title .= " ".$raum;
            $title .= " - ".$vldaten->getListDozenten($t["Seminar_id"], $t["termin_id"]);
            $grps = $vldaten->getRelatedGroups($t["termin_id"]);
            if(!empty($grps)) $title .= " - ".$grps;
            //$name = htmlReady($t["Name"]." - ".$typ["name"]." - ".$vldaten->getRoomToDate($t["termin_id"])." - ".$vldaten->getListDozenten($t["Seminar_id"], $t["termin_id"])." - ".$vldaten->getRelatedGroups($t["termin_id"]));
            $name = htmlReady($title);
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
		$this->plan = $this->renderPlan($entry, $montag);
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
            //Titel zusammensetzen:
            $title = $t["Name"];
            $title .= " (".$typ["name"].")";
            $raum = $vldaten->getRoomToDate($t["termin_id"]);
            if(!empty($raum)) $title .= " ".$raum;
            $title .= " - ".$vldaten->getListDozenten($t["Seminar_id"], $t["termin_id"]);
            echo $vldaten->getRelatedGroups($t["termin_id"]);
			//$name = htmlReady($t["Name"]." - ".$typ["name"]." - ".$vldaten->getRoomToDate($t["termin_id"])." - ".$vldaten->getListDozenten($t["Seminar_id"], $t["termin_id"])." - ".$vldaten->getRelatedGroups($t["termin_id"]));
			$name = htmlReady($title);
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
		$this->plan = $this->renderPlan($entry,$day, "day");

	}



	function renderPlan($termine, $tag, $plantyp = "week") {
		$plan = new CalendarView();
		$plan->setRange("6","21");
        $tag = intval($tag);


		if(sizeof($termine[1]) > "0" OR $plantyp == "week") {
			if($plantyp == "week") $plan->addColumn(_('Montag ('.strftime("%d.%m",$tag).')'));
            if($plantyp == "day") $plan->addColumn(_('Montag ('.$tag.')'));
			foreach($termine[1] as $date) $plan->addEntry($date);
		}


		if(sizeof($termine[2]) > "0" OR $plantyp == "week") {
            if($plantyp == "week") {
                $t =  $tag + 86400;
                $plan->addColumn(_('Dienstag ('.strftime("%d.%m",$t).')'));
            } elseif($plantyp == "day") $plan->addColumn(_('Dienstag ('.$tag.')'));
			foreach($termine[2] as $date) $plan->addEntry($date);
		}

		if(sizeof($termine[3]) > "0" OR $plantyp == "week") {
            if($plantyp == "week") {
                $t =  $tag+(86400*2);
                $plan->addColumn(_('Mittwoch ('.strftime("%d.%m",$t).')'));
            }
            if($plantyp == "day") $plan->addColumn(_('Mittwoch ('.$tag.')'));
			foreach($termine[3] as $date) $plan->addEntry($date);
		}


		if(sizeof($termine[4]) > "0" OR $plantyp == "week") {
            if($plantyp == "week") {
                $t =  $tag+(86400*3);
                $plan->addColumn(_('Donnerstag ('.strftime("%d.%m",$t).')'));
            }
            if($plantyp == "day") $plan->addColumn(_('Donnerstag ('.$tag.')'));
			foreach($termine[4] as $date) 	$plan->addEntry($date);
		}


		if(sizeof($termine[5]) > "0" OR $plantyp == "week") {
            if($plantyp == "week") {
                $t =  $tag+(86400*4);
                $plan->addColumn(_('Freitag ('.strftime("%d.%m",$t).')'));
            }
            if($plantyp == "day") $plan->addColumn(_('Freitag ('.$tag.')'));
			foreach($termine[5] as $date)  $plan->addEntry($date);

		}

		if(sizeof($termine[6]) > "0") {
            if($plantyp == "week") {
                $t =  $tag+(86400*5);
                $plan->addColumn(_('Samstag ('.strftime("%A - %d.%m.%G",$t)).')');
            }
            if($plantyp == "day") $plan->addColumn(_('Samstag ('.$tag.')'));
			foreach($termine[6] as $date) $plan->addEntry($date);
		}
		$plaene =  $plan->render();

		return $plaene;
	}

	public function DateTypToHuman($typ) { //TODO: Ausfall falsche Farbe!!!
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
				$day = $this->DatumToDate($_REQUEST["datum"])+($_REQUEST["day"]*90000);
			}
			else $day = time()+($_REQUEST["day"]*90000);

		}
		elseif($_REQUEST["datum"]) $day = $this->DatumToDate($_REQUEST["datum"]);
		else $day = time();
		return $day;
	}
}