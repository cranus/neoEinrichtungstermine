<h2>Details zum Termin</h2> <br/>
<strong>Name der Veranstaltung</strong>: <?= utf8_encode($sem_name) ?> <br/>
<strong>Dozent</strong>: <br/> <?= utf8_encode($dozenten) ?><br/>
<strong>Raum</strong>:<?=  $raum ?><br/>
<strong>Start</strong>:<?=  $start ?><br/>
<strong>Ende</strong>:<?=  $ende ?><br/>
<strong>Link zu Veranstaltung</strong>: <a href='/details.php?cid=<?= $sem_id?>' target='_blank' style="text-decoration-line: underline; color: #24437C" class="mainmenu"><?= utf8_encode($sem_name) ?></a><br/>
<strong>Liste der Beteiligten Einrichtungen</strong>: <br/><?= $einrichtungen ?>