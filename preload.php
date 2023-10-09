<?php

include 'config.php';

include 'classes/cURL.php';
include 'classes/TempMail.php';
include 'classes/Sqlite3.php';
include 'classes/Telegram.php';
include 'classes/EntityDecoder.php';

global $Http, $TempMail, $Sql, $Telegram, $EntityDecoder;

$Http 			= new Http ();
$TempMail 		= new TempMail ();
$Sql 			= new SQL3 ('users/users.db');
$Telegram 		= new Telegram (TOKEN);
$EntityDecoder 	= new EntityDecoder ('HTML');

$Sql->CreateTable ('users', 'users', 'userid TEXT NOT NULL, login TEXT NOT NULL, domain TEXT NOT NULL, updates TEXT NOT NULL, lastsms TEXT NOT NULL, lastsms2 TEXT NOT NULL, buffer TEXT NOT NULL');

include 'magic-vars.php';
include 'natives.php';

?>