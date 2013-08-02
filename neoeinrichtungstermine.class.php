<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Johannes stichler
 * Date: 18.04.13
 * Time: 15:21
 * To change this template use File | Settings | File Templates.
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
				Navigation::addItem('/insttermin', clone $navigation);
				$navday = new AutoNavigation(_("Tag"), PluginEngine::getURL($this, array(), "start/dayview"));
				Navigation::addItem('/course/insttermin/day', clone $navday);

				$navweek = new AutoNavigation(_("Woche"), PluginEngine::getURL($this, array(), "start/index"));
				Navigation::addItem('/course/insttermin/week', clone $navweek);
	
				//$navmonth = new AutoNavigation(_("Monat"), PluginEngine::getURL($this, array(), "start/monthview"));
				//Navigation::addItem('/course/insttermin/month', clone $navmonth);
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

