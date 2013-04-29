<?php
/**
 * Liefert die Vorlesungsdaten
 */

class vldaten {

	function getAllVlsDay($day = false) {
		if(!$day) $day = time();
		$day = mktime('6','00','00', date("n", $day), date("j", $day), date("Y", $day));
		$dayend = $day+86399;
		//print_r($day." ".$dayend);
		$db = DBManager::get();
		$sql = "SELECT * ".
					"FROM `termine` ".
					"INNER JOIN seminar_inst ON seminar_inst.seminar_id = termine.range_id ".
					"INNER JOIN seminare ON seminare.Seminar_id = seminar_inst.seminar_id ".
					"WHERE seminar_inst.institut_id = '9ce78aec995002a8d3aa96d48c4fec20' AND ".
					"date BETWEEN ".$day." AND ".$dayend. " ";
					"ORDER BY date ".
					"LIMIT 30";
		$termine = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $termine;
	}

	function getAllVlsWeek($day = false) {
		if(!$day) $day = time();
		//Montag errechnen
		$tag = date("N", $day);
		$montag = $day - 86400*($tag - 1);
		$termine = array();
		for($i=0; $i <= "7"; $i++) {
			$temp = $this->getAllVlsDay($montag+86400*$i);
			$termine = array_merge($termine, $temp);

		}
		return $termine;
	}


}