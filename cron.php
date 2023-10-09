<?php

include 'preload.php';

$Sql->All ('users', 'users', '*', function ($params) use ($Sql, $TempMail)
{
	$time 	 	= time ();
	$Buffer 	= json_decode (base64_decode ($params ['buffer']), true);
	$Message 	= $TempMail->GetMessage ("{$params ['login']}@{$params ['domain']}");
	
	if ($Message ['status'] && $params ['lastsms2'] != $Message ['date'])
	{
		$params ['lastsms2'] 	= $Message ['date'];
		
		$Message ['date'] = $time-1;
		
		$params ['lastsms'] 	= $Message ['date'];
		$Buffer [] = $Message;
		
		$sMessage = [];
		$m = trim (strip_tags ($Message ['subject']));
		
		if (!empty ($m) && preg_match ('/[0-9]+/', $m))
		{
			preg_match_all ('/[0-9 ]+/', $m, $xBuffer);
			if (count ($xBuffer) > 0)
			{
				foreach ($xBuffer as $i => $data)
				{
					if (is_array ($data))
					{
						foreach ($data as $i2 => $info)
						{
							if (!empty (trim ($info)))
							{
								$info = trim ($info);
								$m = str_replace ($info, "<code>{$info}</code>", $m);
							}
						}
					}
				}
			}
		}
		
		$m2 = trim (strip_tags ($Message ['text']));
		
		if (!empty ($m2) && preg_match ('/[0-9]+/', $m2))
		{
			preg_match_all ('/[0-9 ]+/', $m2, $xBuffer2);
			if (count ($xBuffer2) > 0)
			{
				foreach ($xBuffer2 as $i => $data)
				{
					if (is_array ($data))
					{
						foreach ($data as $i2 => $info)
						{
							if (!empty (trim ($info)))
							{
								$info = trim ($info);
								$m2 = str_replace ($info, "<code>{$info}</code>", $m2);
							}
						}
					}
				}
			}
		}
		
		if (empty ($m) && empty ($m2))exit ();
		elseif (empty ($m2) && !empty ($m))
		{
			$m2 = $m;
			$m = '';
		}
		
		$sMessage [] = "💬 <b>У Вас новое сообщение</b>\n";
		
		$sMessage [] = '| <b>Отправитель</b>: ' . ucfirst (strtolower (explode ('@', trim (strip_tags ($Message ['from']))) [1]));
		$sMessage [] = '|';
		$sMessage [] = '| <b>Тема</b>: ' . (empty ($m) ? 'Без названия темы...' : $m);
		$sMessage [] = '| <b>Текст сообщения</b>: ' . (empty ($m2) ? 'Без текстового сообщения...' : $m2);
		
		SendMessage ([
			'chat_id' 		=> $params ['userid'],
			'parse_mode' 	=> 'html',
			//'disable_web_page_preview'	=> true,
			
			'text' 			=> trim (implode("\n", $sMessage)),
			'reply_markup'	=> json_encode ([
				'inline_keyboard' => [
					[
						['text' => '❌ Закрыть', 'callback_data' => '/close'],
					],
				]
			])
		]);
	}
	
	$Sql->Edit ('users', 'users', [
		'updates'	=> (string)$time,
		'lastsms'	=> $params ['lastsms'],
		'lastsms2'	=> $params ['lastsms2'],
		'buffer'	=> base64_encode (json_encode ($Buffer)),
	], "userid='{$params ['userid']}'");
});

?>