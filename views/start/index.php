<h1>
	Einrichtungstermine:
</h1>
<h2>
	Von: <?= date("D - d.m.Y", $this->flash->start) ?> -
	Bis: <?= date("D - d.m.Y", $this->flash->end) ?>
</h2>
<div id="neoeinrichtungstermine_menue">
	<div style="float:left; width: 20%">
		<a href="?cid=<?= $this->flash->instid ?>&day=<?= $_REQUEST["day"]-1 ?>"> <img src="/assets/images/icons/16/yellow/arr_2left.png"></a>
	</div>
	<div style="float:left; width: 40%">
		<form><input type="date" id="neoeinrichtungstermine_date" name="datum" style="margin-left: 25%" value="Datum"><button class="button">Ok</button></form>
	</div>
	<div style="float:right; width: 20%">
		<a href="?cid=<?= $this->flash->instid ?>&day=<?= $_REQUEST["day"]+1 ?>"><img src="/assets/images/icons/16/yellow/arr_2right.png" style="float: right"></a>
	</div>
</div>
<div id="neoeinrichtungstermine_plan" style="width: 100%; float: left">
	<?= $plan ?>
</div>
<div id="neoeinrichtungstermine_debug" style="width: 100%">
	<?= $debug ?>
</div>

