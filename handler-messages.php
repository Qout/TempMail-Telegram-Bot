<?php

if (IsUser (userid))$DataBase = $Sql->Read ('users', 'users', 'userid = "'. userid .'"');

if ($MArray [0] == '/start' || message == $mainbuttons [0][0])
{
	if (!IsUser (userid))
	{
		CreateUser (userid);
		
		$Result = SendMessage ([
			'chat_id' 		=> userid,
			'parse_mode' 	=> 'html',
		
			'text' 			=> '🎉',
		]);
		
		if ($Result ['ok'] && ($msgid = @$Result ['result']['message_id']) > 0)
		{
			$MessageData = [
				("📨 <b>Приветствуем тебя, ". user_name ."!</b>\n"),
				
				"<b>Это твоя анонимная временная почта</b>, в которой ты сможешь получать: коды авторизации и ссылки для верификации сервисов.\n",
				
				"<b>Легко, быстро, без рекламы и полностью анонимно. Круто не правда-ли?.</b> 😎"
			];
			
			SendMessage ([
				'chat_id' 		=> userid,
				'parse_mode' 	=> 'html',
			
				'text' 			=> implode ("\n", $MessageData),
				'reply_markup' 	=> json_encode ([
					'inline_keyboard' => [
						[
							['text' => 'Продолжить', 'callback_data' => "/start new {$msgid}"],
						],
					]
				])
			]);
		}
	}
	else
	{
		$Flags = '';
		if (isButton)
		{
			if ($MArray [1] == 'new')
			{
				RemoveMessage ();
				RemoveMessage ($MArray [2]);
			}
			elseif ($MArray [1] == 'back')$Flags = $MArray [1];
		}
		
		$MessageData = [
			"📨 <b>Главное меню</b>\n",
			
			"<b>Ваша почта</b>: <code>{$DataBase ['login']}@{$DataBase ['domain']}</code>\n",
			
			("<b>Последние письмо в</b>: " . (empty ($DataBase ['lastsms']) ? 'Ни одно письмо еще не получено' : date ('d.m.Y в H:i:s', $DataBase ['lastsms']))),
			("<b>Обновлялась в</b>: " . date ('d.m.Y в H:i:s', $DataBase ['updates']) . "\n"),
			
			'⚠️ <u><b>Ящик автоматически обновляется каждую минуту.</b></u>',
		];
		
		if (empty ($Flags))
		{
			SendMessage ([
				'chat_id' 		=> userid,
				'parse_mode' 	=> 'html',
			
				'text' 			=> '📨',
				
				'reply_markup'	=> MainButtons ()
			]);
			
			SendMessage ([
				'chat_id' 		=> userid,
				'parse_mode' 	=> 'html',
			
				'text' 			=> implode ("\n", $MessageData),
				'reply_markup' 	=> ProfileButtons ()
			]);
		}
		elseif ($Flags == 'back')
		{
			$Telegram->editMessageText ([
				'chat_id'		=>	userid,
				'message_id'	=>	messageid,
				'parse_mode'	=>	'html',
				
				'text'			=> implode ("\n", $MessageData),
				'reply_markup' 	=> ProfileButtons ()
			]);
			
			Alert ('Вы вернулись назад');
		}
	}
	
	exit ();
}

if (isButton && $MArray [0] == '/mail')
{
	switch ($MArray [1])
	{
		case 'update': {
			$time 	 = time ();
			$Buffer  = json_decode (base64_decode ($DataBase ['buffer']), true);
			$Message = $TempMail->GetMessage ("{$DataBase ['login']}@{$DataBase ['domain']}");
	
			if ($Message ['status'] && $DataBase ['lastsms2'] != $Message ['date'])
			{
				$DataBase ['lastsms2'] 	= $Message ['date'];
				$Message ['date'] = $time-1;
				$DataBase ['lastsms'] 	= $Message ['date'];
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
					'chat_id' 		=> userid,
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
			
			$Message 		= explode ("\n", GetMessageHtml ());
			$Message [4] 	= "<b>Последние письмо в</b>: " . (empty ($DataBase ['lastsms']) ? 'Ни одно письмо еще не получено' : date ('d.m.Y в H:i:s', $DataBase ['lastsms']));
			$Message [5] 	= "<b>Обновлялась в</b>: " . date ('d.m.Y в H:i:s', $time);
			
			$Sql->Edit ('users', 'users', [
				'updates' 	=> $time,
				'lastsms' 	=> $DataBase ['lastsms'],
				'lastsms2' 	=> $DataBase ['lastsms2'],
				'buffer'  	=> base64_encode (json_encode ($Buffer)),
			], "userid='". userid ."'");
			
			$Telegram->editMessageText ([
				'chat_id'		=>	userid,
				'message_id'	=>	messageid,
				'parse_mode'	=>	'html',
				
				'text'			=> implode ("\n", $Message),
				'reply_markup' 	=> ProfileButtons ()
			]);
			
			Alert ('♻️ Письма обновлены');
		}
		break;
		
		case 'edit': {
			$LoginDomain	= $TempMail->ld ($TempMail->GetEmail ());
			
			$time 			= time ();
			$Message 		= explode ("\n", GetMessageHtml ());
			$Message [2] 	= "<b>Ваша почта</b>: <code>{$LoginDomain ['login']}@{$LoginDomain ['domain']}</code>";
			$Message [4] 	= "<b>Последние письмо в</b>: Ни одно письмо еще не получено";
			$Message [5] 	= "<b>Обновлялась в</b>: " . date ('d.m.Y в H:i:s', $time);
			
			if ($Sql->Edit ('users', 'users', [
				'login'	  => $LoginDomain ['login'],
				'domain'  => $LoginDomain ['domain'],
			
				'updates' => $time,
				'lastsms' => '',
				'buffer'  => base64_encode ('[]'),
			], "userid='". userid ."'"))
			{
				$Telegram->editMessageText ([
					'chat_id'		=>	userid,
					'message_id'	=>	messageid,
					'parse_mode'	=>	'html',
					
					'text'			=> implode ("\n", $Message),
					'reply_markup' 	=> ProfileButtons ()
				]);
				
				Alert ('📨 Адрес почты был изменен');
			}
			else Alert ('⚠️ Произошла неизвестная ошибка, пожалуйста, попробуйте позже.', false);
		}
		break;
		
		case 'open': {
			$Buffer = json_decode (base64_decode ($DataBase ['buffer']), true);
			
			if (count ($MArray) >= 4 && $MArray [2] == 'r')
			{
				$page = $MArray [3];
				unset ($Buffer [$page]);
				
				$Buffer = array_values ($Buffer);
				if ($Sql->Edit ('users', 'users', [
					'buffer'  => base64_encode (json_encode ($Buffer)),
				], "userid='". userid ."'"))Alert ('✅ Письмо было удалено');
				else Alert ('⚠️ Произошла неизвестная ошибка, пожалуйста, попробуйте позже.', false);
			}
			
			if (count ($MArray) >= 4 && $MArray [3] == 'back')
				Alert ('Вы вернулись назад');
			
			$Buttons = [];
			$page 	 = count ($MArray) >= 3 ? ($MArray [2] != 'r' ? $MArray [2] : 0) : 0;
			$MessageData = [
				"📬 <b>Ваш почтовый ящик</b>\n",
				
				'Выберите письмо ниже чтобы его открыть'
			];
			
			$Pages  = array_chunk ($Buffer, 4, true);
			
			if (count ($MArray) >= 3 && $MArray [2] != 'r' && @$MArray [3] != 'back')
			{
				if ($page < 0 || $page >= count ($Pages))
				{
					ClickButton ();
					exit ();
				}
			}
			else $page = 0;
			
			foreach ($Pages [$page] as $num => $Pack)
			{
				$m = ucfirst (strtolower (explode ('@', trim (strip_tags ($Pack ['from']))) [1]));
				$d = date ('d.m.Y в H:i:s', $Pack ['date']);
				$Buttons [][] = ['text' => ("[{$d}] " . (empty ($m) ? 'Без названия темы...' : $m)), 'callback_data' => "/mail show {$num} {$page}"];
			}
			
			if (count ($Buffer) < 1)$Buttons = [[['text' => 'Письма не найдены', 'callback_data' => '!']]];
			else
			{
				$Buttons [] = [
					['text' => '«', 'callback_data' => "/mail open " . ($page-1)],
					['text' => (($page+1) . '/' . count ($Pages)), 'callback_data' => '!'],
					['text' => '»', 'callback_data' => "/mail open " . ($page+1)]
				];
			}
			
			$Buttons [] = [['text' => 'Назад', 'callback_data' => '/start back']];
			
			$Telegram->editMessageText ([
				'chat_id'		=>	userid,
				'message_id'	=>	messageid,
				'parse_mode'	=>	'html',
				
				'text'			=> implode ("\n", $MessageData),
				'reply_markup'	=> json_encode ([
					'inline_keyboard' => $Buttons
				])
			]);
		}
		break;
		
		case 'show': {
			$page = $MArray [2];
			$Buffer = json_decode (base64_decode ($DataBase ['buffer']), true);
			if ($page > count ($Buffer) || count ($Buffer) < $page)
			{
				Alert ('⚠️ Письмо устарело.', false);
				exit ();
			}
			else $Buffer = $Buffer [$page];
			
			$m = trim (strip_tags ($Buffer ['subject']));
				
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
			
			$m2 = trim (strip_tags ($Buffer ['text']));
			
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
			
			$sMessage [] = "💬 <b>Сообщение</b>\n";
				
			$sMessage [] = '| <b>Отправитель</b>: ' . ucfirst (strtolower (explode ('@', trim (strip_tags ($Buffer ['from']))) [1]));
			$sMessage [] = '| <b>Получено в</b>: ' . date ('d.m.Y в H:i:s', $Buffer ['date']);
			$sMessage [] = '|';
			$sMessage [] = '| <b>Тема</b>: ' . (empty ($m) ? 'Без названия темы...' : $m);
			$sMessage [] = '| <b>Текст сообщения</b>: ' . (empty ($m2) ? 'Без текстового сообщения...' : $m2);
			
			$Buttons = [
				[
					['text' => 'Удалить', 'callback_data' => "/mail open r {$page}"]
				],
				
				[
					['text' => 'Назад', 'callback_data' => "/mail open {$MArray [3]} back"]
				]
			];
			
			$Telegram->editMessageText ([
				'chat_id'		=>	userid,
				'message_id'	=>	messageid,
				'parse_mode'	=>	'html',
				
				'text'			=> implode ("\n", $sMessage),
				'reply_markup'	=> json_encode ([
					'inline_keyboard' => $Buttons
				])
			]);
		}
		break;
	}
	
	exit ();
}

?>