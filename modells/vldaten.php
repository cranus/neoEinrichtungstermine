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
		$sql = "SELECT termine.termin_id, termine.content, termine.description, termine.date, termine.end_time, termine.date_typ, seminare.Name,  seminare.Seminar_id ".
					"FROM `termine` ".
					"INNER JOIN seminar_inst ON seminar_inst.seminar_id = termine.range_id ".
					"INNER JOIN seminare ON seminare.Seminar_id = seminar_inst.seminar_id ".
					"WHERE seminar_inst.institut_id = '".$instid."' AND ".
					"date BETWEEN ".$day." AND ".$dayend. " ".
					"".
                    "UNION ".
		            "SELECT ex_termine.termin_id, ex_termine.content, ex_termine.description, ex_termine.date, ex_termine.end_time, '6' AS date_typ, seminare.Name,  seminare.Seminar_id ".
                    "FROM `ex_termine` ".
                    "INNER JOIN seminar_inst ON seminar_inst.seminar_id = ex_termine.range_id ".
                    "INNER JOIN seminare ON seminare.Seminar_id = seminar_inst.seminar_id ".
                    "WHERE seminar_inst.institut_id = '".$instid."' AND date BETWEEN ".$day." AND ".$dayend. " ".
                    "AND ex_termine.content <> ''";
		$termine = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $termine;
	}

	function getAllVlsWeek($day = false, $montag) {
		if(!$day)  {
			$day = time();
		}
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

    public function getTerminInfos($terminid) {
        $sql = "SELECT termine.raum AS raum_frei, seminare.name, seminare.VeranstaltungsNummer, seminare.Seminar_id, termine.date, termine.end_time FROM `termine`
                INNER JOIN seminare on seminare.Seminar_id = termine.range_id
                WHERE termine.termin_id = ?".
                " UNION ".
                "SELECT ex_termine.raum AS raum_frei, seminare.name, seminare.VeranstaltungsNummer, seminare.Seminar_id, ex_termine.date, ex_termine.end_time FROM `ex_termine`
                INNER JOIN seminare on seminare.Seminar_id = ex_termine.range_id
                WHERE ex_termine.termin_id = ?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($terminid,$terminid));
        return $result = $db->fetchAll();
    }


    public function getInstituteBySemid($semid) {
        $sql = "SELECT Institute.Name "
            ."FROM `seminar_inst` "
            ."INNER JOIN Institute on Institute.Institut_id = seminar_inst.Institut_id "
            ."WHERE Seminar_id = ?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($semid));
        return $db->fetchAll();
    }

    public function getRoomToDate($terminid) {
        $sql =  "SELECT ro.name AS raum "
            ." FROM resources_objects as ro"
            ." INNER JOIN resources_assign as ra ON ra.resource_id = ro.resource_id"
            ." WHERE ra.assign_user_id = ?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($terminid));
        $result = $db->fetchAll();
        return $result[0][0];
    }

    function getListDozenten($semid) {
        $sql = "SELECT auth_user_md5.Vorname, auth_user_md5.Nachname FROM `seminar_user`
                INNER JOIN auth_user_md5 on seminar_user.user_id = auth_user_md5.user_id
                WHERE Seminar_id = ?
                AND status='dozent'";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($semid));
        $result = $db->fetchAll();
        //Dozenten in die VL eintragen
        $dozenten = "";
        foreach($result as $res) {
            if(!empty($dozenten)) $dozenten .=", ";
            $dozenten .= $res["Vorname"]." ".$res["Nachname"];

        }
        return $dozenten;
    }
}