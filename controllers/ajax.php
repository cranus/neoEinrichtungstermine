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

class ajaxController extends \StudipController {
    function before_filter(&$action, &$args)
    {
        $this->flash = Trails_Flash::instance();
        // set default layout
        //$layout = $GLOBALS['template_factory']->open('layouts/base');
        //$layout =  "ajax/layout";
        //$this->set_layout($layout);
        $this->instid = (!empty($_GET["cid"]) ? $_GET["cid"] : (empty($SessSemName[1]) ? $_GET["auswahl"] : $SessSemName[1]));
        //PageLayout::addScript($this->flash->net->url . 'neo/neoeinrichtungstermine/assets/neoET.js');
    }

    public function details_action() {
        $this->getTermin();
    }

    function getTermin() {
        //Allgemeine Infos setzen
        $this->flash->id = $_REQUEST['id'];
        $sql = "SELECT termine.raum AS raum_frei, seminare.name, seminare.VeranstaltungsNummer, seminare.Seminar_id, termine.date, termine.end_time FROM `termine`
        INNER JOIN seminare on seminare.Seminar_id = termine.`range_id`
        WHERE termine.termin_id = ?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->flash->id));
        $result = $db->fetchAll();

        $this->sem_name = $result[0]['name'];
        $this->sem_id = $result[0]['Seminar_id'];
        $this->start = date("d.m.Y, H:i",$result[0]['date']);
        $this->ende = date("d.m.Y, H:i",$result[0]['end_time']);

        // Raum auslesen
        $this->getRoomToDate();
        // Dozenten auslesen
        $this->getListDozenten();
        // Einrichtungen

        $sql = "SELECT Institute.Name "
            ."FROM `seminar_inst` "
            ."INNER JOIN Institute on Institute.Institut_id = seminar_inst.Institut_id "
            ."WHERE Seminar_id = ?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->sem_id));
        $result = $db->fetchAll();
        //Dozenten in die VL eintragen
        foreach($result as $res) {
            $this->einrichtungen .= $res["Name"]."<br/>";
        }
    }

    function getRoomToDate() {
        $sql =  "SELECT ro.name AS raum "
            ." FROM resources_objects as ro"
            ." INNER JOIN resources_assign as ra ON ra.resource_id = ro.resource_id"
            ." WHERE ra.assign_user_id = ?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->flash->id));
        $result = $db->fetchAll();
        $this->raum = $result[0][0];
    }

    function getListDozenten() {
        $sql = "SELECT auth_user_md5.Vorname, auth_user_md5.Nachname FROM `seminar_user`
                INNER JOIN auth_user_md5 on seminar_user.user_id = auth_user_md5.user_id
                WHERE Seminar_id = ?
                AND status='dozent'";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->sem_id));
        $result = $db->fetchAll();
        //Dozenten in die VL eintragen
        foreach($result as $res) {
            $this->dozenten .= $res["Vorname"]." ".$res["Nachname"]."<br/>";
        }
    }

}