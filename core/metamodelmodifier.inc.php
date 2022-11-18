<?php

//Any extension to hook the initialization of the metamodel 

interface iOnClassInitialization
{
	public function OnAfterClassInitialization($sClass);
}

?>
