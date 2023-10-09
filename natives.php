<?php

function MainButtons () { return json_encode (['keyboard' => @$GLOBALS ['mainbuttons'], 'resize_keyboard' => true]); }

function ProfileButtons () {
	return json_encode ([
		'inline_keyboard' => [
			[
				['text' => '📬 Ящик', 'callback_data' => '/mail open'],
			],
		
			[
				['text' => '📨 Изменить почту', 'callback_data' => '/mail edit'],
			],
			
			[
				['text' => '♻️ Обновить письма', 'callback_data' => '/mail update'],
			],
		]
	]);
}

function array_to_object (array $array)
{
	$obj = new stdClass;
	foreach ($array as $k => $v)
	{
		if (strlen ($k))
		{
			if (is_array ($v))	$obj->{$k} = array_to_object ($v);
			else				$obj->{$k} = $v;
		}
	}
	
	return $obj;
}

function GetMessageHtml ()
{
	global $Telegram, $EntityDecoder;
	return $EntityDecoder->decode ($Telegram->getData () ['callback_query']['message']);
}

function SendMessage (array $pointer)
{
	return $GLOBALS ['Telegram']->sendMessage ($pointer);
}

function Alert ($message, bool $popup = true)
{
	if (is_array ($message))$message = implode ("\n", $message);
	elseif (!is_string ($message))return false;
	
	return $GLOBALS ['Telegram']->answerCallbackQuery([
		'callback_query_id' => $GLOBALS ['Telegram']->getData () ['callback_query']['id'],
		'text' 				=> $message,
		'show_alert' 		=> !$popup,
		'cache_time' 		=> 0
	]);
}

function ClickButton ()
{
	if (!isButton)return;
	
	return $GLOBALS ['Telegram']->answerCallbackQuery (
		[
			'callback_query_id' => $GLOBALS ['Telegram']->getData () ['callback_query']['id']
		]
	);
}

function RemoveMessage ($messageid = 0, $chatid = 0)
{
	if ($chatid <= 0)	$chatid = chatid;
	if ($messageid <= 0)$messageid = messageid;
	
	return $GLOBALS ['Telegram']->deleteMessage  (['chat_id' => $chatid, 'message_id' => $messageid]);
}

function IsUser ($userid = 0)
{
	if ($userid <= 0)$userid = userid;
	
	global $Sql;
	return $Sql->Exists ('users', 'users', "userid='{$userid}'") > 0;
}

function CreateUser ($userid = 0)
{
	if ($userid <= 0)$userid = userid;
	
	global $TempMail, $Sql;
	$time 			= time ()+1;
	$LoginDomain	= $TempMail->ld ($TempMail->GetEmail ());
	
	return $Sql->Write ('users', 'users', [
		'userid' 	=> (string)$userid,
		'login'		=> $LoginDomain ['login'],
		'domain'	=> $LoginDomain ['domain'],
		'updates'	=> (string)$time,
		'lastsms'	=> '',
		'lastsms2'	=> '',
		'buffer'	=> base64_encode ('[]'),
	]);
}

?>