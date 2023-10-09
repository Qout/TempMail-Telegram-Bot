<?php

/*
 * TempMail by Root;
 */
class TempMail
{
	public $Http;
	
	public $login;
	public $domain;
	public $email;
	
	private function __Updaters ()
	{
		$tmails = json_decode ($this->Http->Get ('https://www.1secmail.com/api/v1/?action=genRandomMailbox&count=1'), true);
		$this->email = $tmails [0];
		
		
		$arr = explode ('@', $this->email);
		
		$this->login 	= $arr [0];
		$this->domain 	= $arr [1];
	}
	
	public function ld ($v)/*ld: l - login, d - domain*/
	{
		if (!empty ($v))
		{
			$v = explode ('@', $v);
			if (count ($v) == 2)
				return ['login' => $v [0], 'domain' => $v [1]];
		}
		
		return ['login' => $this->login, 'domain' => $this->domain];
	}
	
	public function __construct ()
	{
		$this->Http = new Http ();
		self::__Updaters ();
	}
	
	public function GetEmail ($update = false)
	{
		if (!$update)return $this->email;
		else
		{
			self::__Updaters ();
			return $this->email;
		}
	}
	
	public function GetMessages ($email = '')
	{
		$v = self::ld ($email);
		$login 	= $v ['login'];
		$domain = $v ['domain'];
		
		return @json_decode ($this->Http->Get ("https://www.1secmail.com/api/v1/?action=getMessages&login={$login}&domain={$domain}"), true);
	}
	
	public function GetMessage ($email = '', $id = 1)
	{
		$Messages = self::GetMessages ($email);
		
		if (($i = count ($Messages)) == 0)return ['status' => false];
		else
		{
			if ($i >= $id)
			{
				$v = self::ld ($email);
				$login 	= $v ['login'];
				$domain = $v ['domain'];
				$id 	= $Messages [($id-1)]['id'];
				
				$read 	= @json_decode ($this->Http->Get ("https://www.1secmail.com/api/v1/?action=readMessage&login={$login}&domain={$domain}&id={$id}"), true);
				
				return [
					'status' 		=> true,
				
					'date' 			=> $read ['date'],
					
					'from' 			=> $read ['from'],
					
					'attachments' 	=> $read ['attachments'],
					
					'subject' 		=> $read ['subject'],
					'text' 			=> $read ['textBody'],
					'html' 			=> $read ['htmlBody'],
				];
			}
			else return ['status' => false];
		}
	}
}

?>