<?php

date_default_timezone_set('Europe/Moscow');

// Включаем вывод всех ошибок
error_reporting(E_ERROR | E_PARSE);

// Задаем переменные php.ini
ini_set ('error_log', 'errors_' . time (). '.txt');
ini_set ('log_errors', true);
ini_set ('display_errors', false);

include 'preload.php';

$MArray = [];
if (!empty (message))
{
	$MArray = @explode (' ', message);
	if (count ($MArray) > 0 && @$MArray [0][0] == '/')
		$MArray [0] = mb_strtolower ($MArray [0]);
}

if (message == '!' || message == 'null')
{
	if (isButton)ClickButton ();
	exit ();
}
elseif (isButton)
{
	$_ = true;
	if ($MArray [0] == '/close')
	{
		if (count ($MArray) >= 2
			&& $MArray [1] != userid)
		{
			Alert ('⚠️ Вы не можете нажать');
			exit ();
		}
		
		ClickButton ();
		RemoveMessage ();
	}
	elseif ($MArray [0] == '/cancel')
	{
		if (count ($MArray) >= 2
			&& $MArray [1] != userid)
		{
			Alert ('⚠️ Вы не можете нажать');
			exit ();
		}
		
		//Step (userid, '');
		ClickButton ();
		RemoveMessage ();
	}
	else $_ = false;
	
	if ($_)exit ();
}

include 'handler-messages.php';

?>