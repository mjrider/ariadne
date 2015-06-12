<?php
	$ARCurrent->nolangcheck=true;
	if ($this->CheckLogin("read") && $this->CheckConfig()) {

		include($this->store->get_config("code")."widgets/wizard/code.php");

		$wgWizButtons = array(
			"cancel" => array(
				"value" => $ARnls["cancel"]
			),
			"save" => array(
				"value" => $ARnls["save"]
			),
		);

		$wgWizFlow = array();
		$wgWizFlow[] = array(
			"current" => $this->getdata("wgWizCurrent","none"),
			"cancel" => "window.close.js",
			"save" => "dialog.image.save.php",
		);
		$wgWizFlow[] = array(
			"title" => $ARnls['settings'],
			"image" => $AR->assets->images . 'wizard/data.png', // FIXME: give this a decent icon or no icon
			"template" => "dialog.image.form.php"
		);

		$this->call("typetree.ini");
		$name=$ARCurrent->arTypeNames[$this->type];

		$wgWizScripts = array(
			$AR->assets->js . "/muze.js",
			$AR->assets->js . "/muze/event.js",
			$AR->assets->js . "/muze/dialog.js"
		);
		// spawn wizard
		$wgWizHeaderIcon = $AR->assets->images . 'icons/large/pphoto.png';
		$wgWizTitle=$ARnls["ariadne:editor:imageedit"];
		$wgWizHeader=$wgWizTitle;
		include($this->store->get_config("code")."widgets/wizard/yui.wizard.html");
	}
?>
