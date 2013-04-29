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
		parent::__construct();
		unset($GLOBALS["plugin_pfad"]);
		$this->flash = Trails_Flash::instance();
		$this->flash->vmurl = $this->getPluginURL();
		$this->flash->instid = $this->checkInstitute((!empty($_GET["cid"]) ? $_GET["cid"] : (empty($SessSemName[1]) ? $_GET["auswahl"] : $SessSemName[1]))); //ToDO: Das geht bestimmt besser
		$this->createnav();
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

		if(!$this->flash->instid AND Navigation::hasItem("/course")) {
			$course = Navigation::getItem('/course');
			$course->setEnabled(0);

			$navigation = new AutoNavigation(_("Einrichtungstermine"), PluginEngine::getURL($this, array(), "start"));
			Navigation::addItem('/course/insttermin', clone $navigation);
		}

	}

	function checkInstitute ($id) {
		$inst = new Institute($id);
		$fkid = $inst->getValue('fakultaets_id');
		if(!empty($fkid)) {

			return $id;
		}
		else {
			return true;
		}
	}

	function getPluginname()
	{
		return $this->pluginname; //HFWU Change
	}



}

