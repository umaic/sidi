<?php

include_once 'ofc_bar_base.php';

class bar extends bar_base
{
	function bar()
	{
		$this->type      = "bar";
		parent::bar_base();
	}
}

