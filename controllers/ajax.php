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

class ajaxController extends \StudipController {
    function before_filter(&$action, &$args)
    {
        $this->flash = Trails_Flash::instance();
        $this->instid = (!empty($_GET["cid"]) ? $_GET["cid"] : (empty($SessSemName[1]) ? $_GET["auswahl"] : $SessSemName[1]));
    }

    public function details_action() {
        $this->getTermin();
    }

    function getTermin() {
        //Allgemeine Infos
        $result = vldaten::getTerminInfos($_REQUEST['id']);
        $this->sem_name = $result[0]['name'];
        $this->sem_id = $result[0]['Seminar_id'];
        $this->start = date("d.m.Y, H:i",$result[0]['date']);
        $this->ende = date("d.m.Y, H:i",$result[0]['end_time']);

        // Raum auslesen
        $this->raum = vldaten::getRoomToDate($this->flash->id);
        // Dozenten auslesen
        $this->dozenten = vldaten::getListDozenten($this->sem_id);
        // Einrichtungen

        $result = vldaten::getInstituteBySemid($this->semid);
        //Dozenten in die VL eintragen
        foreach($result as $res) {
            $this->einrichtungen .= $res["Name"]."<br/>";
        }
    }
}