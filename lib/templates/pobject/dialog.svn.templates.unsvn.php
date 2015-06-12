<?php
	$ARCurrent->nolangcheck=true;
	if ($this->CheckLogin("layout") && $this->CheckConfig()) {
		include($this->store->get_config("code")."widgets/wizard/code.php");

		$wgWizFlow = array(
			array(
				"current" => $this->getdata("wgWizCurrent","none"),
				"template" => "dialog.svn.templates.unsvn.form.php",
				"cancel" => "window.close.js",
				"save" => "dialog.svn.templates.unsvn.save.php"
			)
		);

		$wgWizAction = $this->getdata("wgWizAction");

		if( $wgWizAction == "save" ) {

			$wgWizButtons = array(
				"cancel" => array(
					"value" => $ARnls["ok"]
				)
			);
		} else {
			$wgWizButtons = array(
				"cancel" => array(
					"value" => $ARnls["cancel"]
				),
				"save" => array(
					"value" => $ARnls["ariadne:svn:unsvn"]
				),
			);
		}

		$wgWizTitle=$ARnls['ariadne:svn:unsvn'];
		$wgWizHeader = $wgWizTitle;
		$wgWizHeaderIcon = $AR->assets->images.'icons/large/unsvn.png';

		include($this->store->get_config("code")."widgets/wizard/yui.wizard.html");
	}
?>
