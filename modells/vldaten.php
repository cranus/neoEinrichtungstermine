<?php
/**
 * Liefert die Vorlesungsdaten
 */

class vldaten {

	function __construct()
	{
		$this->flash = Trails_Flash::instance();

	}

	function getAllVlsDay($day = false, $instid = false) {
		if(!$instid) $instid = $this->flash->instid;
		if(!$day) $day = time();
		$day = mktime('6','00','00', date("n", $day), date("j", $day), date("Y", $day));
		$dayend = $day+86399;
		$db = DBManager::get();
		$sql = "SELECT * ".
					"FROM `termine` ".
					"INNER JOIN seminar_inst ON seminar_inst.seminar_id = termine.range_id ".
					"INNER JOIN seminare ON seminare.Seminar_id = seminar_inst.seminar_id ".
					"WHERE seminar_inst.institut_id = '".$instid."' AND ".
					"date BETWEEN ".$day." AND ".$dayend. " ";
					"ORDER BY date ".
					"LIMIT 30";
		$this->flash->debug .= $sql."<br>";
		$termine = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $termine;
	}

	function getAllVlsWeek($day = false, $montag) {
		if(!$day)  {
			$day = time();
		}
		/*if(isset($_REQUEST["week"]) AND is_numeric($_REQUEST["week"])) { //TODO: Löschen
			$day = $day + $_REQUEST["week"] * (86400*7);
		}*/
		$termine = array();
		for($i=0; $i <= "6"; $i++) {
			$temp_day = $montag+86400*$i;
			$temp = $this->getAllVlsDay($temp_day);
			$termine = array_merge($termine, $temp);
			$this->flash->end = $temp_day;

		}
		$this->flash->start = $montag;

		return $termine;
	}


}