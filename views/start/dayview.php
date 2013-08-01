<h1>
	Einrichtungstermine:
</h1>
<h2>
	Am: <?= strftime("%A - %d.%m.%G", $this->flash->start) ?>
</h2>
<div id="neoeinrichtungstermine_menue">
	<div style="float:left; width: 20%">
		<a href="?cid=<?= $this->flash->instid ?>&day=<?= $_REQUEST["day"]-1 ?><? if(isset( $_REQUEST["datum"])) : ?>&datum=<?= $_REQUEST["datum"] ?><? ENDIF ?>"> <img src="/assets/images/icons/16/yellow/arr_2left.png"></a>
	</div>
	<div style="float:left; width: 40%">
		<form><input id="neoeinrichtungstermine_date" name="datum" style="margin-left: 25%" value="Datum"><input id="neoeinrichtungstermine_cid" name="cid" style="display: none" value="<?= $_REQUEST["cid"] ?>"><button class="button">Ok</button></form>
	</div>
	<div style="float:right; width: 20%">
		<a href="?cid=<?= $this->flash->instid ?>&day=<?= $_REQUEST["day"]+1 ?><? if(isset( $_REQUEST["datum"])) : ?>&datum=<?= $_REQUEST["datum"] ?><? ENDIF ?>"><img src="/assets/images/icons/16/yellow/arr_2right.png" style="float: right"></a>
	</div>
</div>
<div id="neoeinrichtungstermine_plan" style="width: 100%; float: left">
	<?= $plan ?>
</div>
<div id="neoeinrichtungstermine_debug" style="width: 100%;float: left">
	<?= $debug ?>
</div>
