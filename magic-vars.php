<?php

define ('userid'   		, $Telegram->UserID   	());
define ('chatid'   		, $Telegram->ChatID   	());
define ('username' 		, $Telegram->Username 	());

define ('first_name' 	, $Telegram->FirstName 	());
define ('last_name' 	, $Telegram->LastName 	());

$UNames = '';
if (!empty(first_name))$UNames .= first_name;
if (!empty(last_name))$UNames .= ' ' . last_name;

define ('user_name' 	, trim($UNames));

$iMessage = $Telegram->Text ();
if (empty ($iMessage))$iMessage = $Telegram->Caption ();
define ('message'  		, trim (str_replace (["@{$Me->username}", '  '], ['', ' '], $iMessage)));
define ('messageid'		, $Telegram->MessageID 	());

define ('isChat'		, chatid != userid);

$getData 	= $Telegram->getData ();
if (!is_array ($getData))$getData = [];

$isButton 	= false;
foreach ($mainbuttons as $Data)
{
	foreach ($Data as $Data2)
	{
		if ($Data2 == message)
			$isButton = true;
	}
	
	if ($isButton)break;
}
	
define ('isButton'		, (array_key_exists ('callback_query', $getData) || $isButton));

?>