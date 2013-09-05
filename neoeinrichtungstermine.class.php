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

require_once 'vendor/trails/trails.php';

class neoeinrichtungstermine  extends \StudipPlugin implements \SystemPlugin {

	function __construct()
	{
		unset($GLOBALS["plugin_pfad"]);
		$this->flash = Trails_Flash::instance();
		$this->flash->net->url = $this->getPluginURL();
		$this->flash->instid = $this->checkInstitute((!empty($_GET["cid"]) ? $_GET["cid"] : (empty($SessSemName[1]) ? $_GET["auswahl"] : $SessSemName[1]))); //ToDO: Das geht bestimmt besser
		$this->instid = $this->checkInstitute((!empty($_GET["cid"]) ? $_GET["cid"] : (empty($SessSemName[1]) ? $_GET["auswahl"] : $SessSemName[1])));
		$this->createnav();
		parent::__construct();

	}

	/**
	 * This method dispatches all actions.
	 *
	 * @param string   part of the dispatch path that was not consumed
	 */
	function perform($unconsumed_path)
	{
		$trails_root = $this->getPluginPath();
		$dispatcher = new Trails_Dispatcher($trails_root, NULL, NULL);
		$dispatcher->dispatch($unconsumed_path);
	}

	function createnav() {
		try{
			if($this->checkInstitute($this->instid)) {
				$navigation = new AutoNavigation(_("Einrichtungstermine"), PluginEngine::getURL($this, array(), "start"));
				Navigation::addItem('/course/insttermin', clone $navigation);

				$navday = new AutoNavigation(_("Tag"), PluginEngine::getURL($this, array(), "start/dayview"));
				Navigation::addItem('/course/insttermin/day', clone $navday);

				$navweek = new AutoNavigation(_("Woche"), PluginEngine::getURL($this, array(), "start/index"));
				Navigation::addItem('/course/insttermin/week', clone $navweek);

			}

		} catch(Exception $ex) {}
	}

	function checkInstitute ($id) {

		$inst = new Institute($id);
		$fkid = $inst->getValue('fakultaets_id');
		if(!empty($fkid)) {

			return $id;
		}
		else {
			return false;
		}
	}

	function getPluginname()
	{
		return $this->pluginname;
	}
}

