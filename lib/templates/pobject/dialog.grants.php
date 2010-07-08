<?php
	$ARCurrent->nolangcheck=true;
	if ($this->CheckLogin("config") && $this->CheckConfig()) {
		include($this->store->get_config("code")."widgets/wizard/code.php");

		$wgWizFlow = array(
			array(
				"current" => $this->getdata("wgWizCurrent","none"),
				"template" => "dialog.grants.form.php",
				"cancel" => "window.close.js",
				"review" => "dialog.grants.review.php",
				"save" => "dialog.grants.save.php"
			)
		);

		$wgWizAction = $this->getdata("wgWizAction");

		if( $wgWizAction == "save" ) {
			$wgWizButtons = array(
				"cancel" => array(
					"value" => $ARnls["ok"]
				)
			);
		} elseif ($wgWizAction == "review") {
			$wgWizButtons = array(
				"cancel" => array(
					"value" => $ARnls["cancel"]
				),
				"save" => array(
					"value" => $ARnls["apply"]
				)
			);
		} else {
			$wgWizButtons = array(
				"cancel" => array(
					"value" => $ARnls["cancel"]
				),
				"review" => array(
					"value" => $ARnls["apply"]
				),
			);
		}

		$wgWizScripts = array(
			$AR->dir->www . "js/muze.js",
		);

		$wgWizTitle=$ARnls["grants"];
		$wgWizStyleSheets = array( $AR->dir->styles."grants.css" );
		$wgWizHeader=$wgWizTitle;
		$wgWizHeaderIcon = $AR->dir->images.'icons/large/grants.png';
		include($this->store->get_config("code")."widgets/wizard/yui.wizard.html");
	}
?>