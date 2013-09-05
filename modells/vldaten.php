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
		$termine = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $termine;
	}

	function getAllVlsWeek($day = false, $montag) {
		if(!$day)  {
			$day = time();
		}
		/*if(isset($_REQUEST["week"]) AND is_numeric($_REQUEST["week"])) { //TODO: L�schen
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

    function getDetails($terminid) {
        $db = DBManager::get();
        $sql = "SELECT * ".
            "FROM `termine` ".
            "INNER JOIN seminar_inst ON seminar_inst.seminar_id = termine.range_id ".
            "INNER JOIN seminare ON seminare.Seminar_id = seminar_inst.seminar_id ".
            "WHERE seminar_inst.institut_id = '".$instid."' AND ".
            "date BETWEEN ".$day." AND ".$dayend. " ";
        "ORDER BY date ".
        "LIMIT 30";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);


    }


}