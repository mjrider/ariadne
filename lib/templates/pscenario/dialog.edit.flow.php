<?php

$ARCurrent->nolangcheck=true;
if (($this->CheckLogin("edit") || $this->CheckLogin("add", ARANYTYPE)) && $this->CheckConfig()) {

	$wgWizFlow[] = array(
		"title" => $ARnls["display"],
		"image" => $AR->assets->images."wizard/data.png",
		"template" => "dialog.edit.displays.php",
		"nolang" => true,
	);

	$arResult = $wgWizFlow;
}

?>
