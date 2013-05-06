<h1>
	Einrichtungstermine:
</h1>
<h2>
	Von: <?= date("D - d.m.Y", $this->flash->start) ?> -
	Bis: <?= date("D - d.m.Y", $this->flash->end) ?>
</h2>
<div id="neoeinrichtungstermine_menue">
	<a href="?cid=<?= $this->flash->instid ?>&week=<?= $_REQUEST["week"]-1 ?>"> <img src="/assets/images/icons/16/yellow/arr_2left.png" style="float:left"></a>
	<form><input type="date" id="neoeinrichtungstermine_date" name="datum" style="margin-left: 25%" value="Datum"><button class="button">Ok</button></form>
	<a href="?cid=<?= $this->flash->instid ?>&week=<?= $_REQUEST["week"]+1 ?>"><img src="/assets/images/icons/16/yellow/arr_2right.png" style="float:right"></a>
</div>
<div id="neoeinrichtungstermine_plan">
	<?= $plan ?>
</div>
<div id="neoeinrichtungstermine_debug">
	<?= $debug ?>
</div>
