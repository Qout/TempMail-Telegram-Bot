<?php

class SQL3
{
	/*
		Example
		
		$sql->Write ('users', 'Users', [
			'userid' 	=> 1337,
			'mail'		=> 'your3@mail.com',
			'mail2'		=> 'your4@mail.com',
		]);
		
		$a = $sql->Read ('users', 'Users', 'userid = "1338"');
		$c = $sql->Counts ('users', 'Users', 'userid = "1337"');
		$c = $sql->Exists  ('users', 'Users', 'userid = "1337"');
		$sql->Edit ('users', 'Users', [
			'mail' => 'edit@mail.com',
			'mail2' => 'edit2@mail.com',
		], 'userid = "1337"');
		
		==========================
	*/
	
	private $Database = []; 
	
	public function __construct (string $filename)
	{
		$dbname = basename ($filename, '.db');
		$this->Database [md5 ($dbname)] = new SQLite3 ($filename);
	}
	
	public function Open (string $filename)
	{
		$dbname = basename ($filename, '.db');
		$this->Database [md5 ($dbname)] = new SQLite3 ($filename);
		return true;
	}
	
	public function Close (string $dbname)
	{
		$this->Database [md5 ($dbname)]->close ();
		unset ($this->Database [md5 ($dbname)]);
		
		return true;
	}
	
	public function CreateTable (string $dbname, string $Name, string $Params)
	{
		$dbname = md5 ($dbname);
		if (array_key_exists ($dbname, $this->Database))
			return $this->Database [$dbname]->exec ("CREATE TABLE IF NOT EXISTS `{$Name}` ({$Params});");
		
		return false;
	}
	
	private function KeyValue (array $KeyValue, $type = 1)
	{
		if (count ($KeyValue) > 0)
		{
			if ($type == 1)
			{
				$Keys 	= '';
				$Values = '';
				foreach ($KeyValue as $Key => $Value)
				{
					$Keys 	.= "{$Key}, ";
					
					if (is_numeric ($Value) || is_int ($Value))	$Values	.= "{$Value}, ";
					elseif (is_string ($Value))					$Values	.= "'{$Value}', ";
				}
				
				$Keys 	= substr ($Keys, 0, strlen ($Keys)-2);
				$Values = substr ($Values, 0, strlen ($Values)-2);
				
				return ['Keys' => $Keys, 'Values' => $Values];
			}
			elseif ($type == 2)
			{
				$KeyValue_line = '';
				foreach ($KeyValue as $Key => $Value)
				{
					$Value_line = '';
					if (is_numeric ($Value) || is_int ($Value))	$Value_line	= "{$Value}, ";
					elseif (is_string ($Value))					$Value_line	= "'{$Value}', ";
					
					$KeyValue_line 	.= "`{$Key}`={$Value_line}";
				}
				
				return substr ($KeyValue_line, 0, strlen ($KeyValue_line)-2);
			}
		}
		
		return false;
	}
	
	public function Write (string $dbname, string $Table, array $KeyValue)
	{
		$dbname = md5 ($dbname);
		if (array_key_exists ($dbname, $this->Database))
		{
			$KeyValue = $this->KeyValue ($KeyValue, 1);
			return $this->Database [$dbname]->exec ("INSERT INTO `{$Table}` ({$KeyValue ['Keys']}) VALUES ({$KeyValue ['Values']});");
		}
		
		return false;
	}
	
	public function Read (string $dbname, string $Table, string $Key, $Values = '*', $type = SQLITE3_ASSOC)
	{
		$dbname = md5 ($dbname);
		if (array_key_exists ($dbname, $this->Database))
		{
			$Key = str_replace ('"', "'", $Key);
			return $this->Database [$dbname]->query ("SELECT {$Values} FROM `{$Table}` WHERE {$Key}")->fetchArray ($type);
		}
		
		return false;
	}
	
	public function Edit (string $dbname, string $Table, array $KeyValue, string $Key)
	{
		$dbname = md5 ($dbname);
		if (array_key_exists ($dbname, $this->Database))
		{
			$KeyValue = $this->KeyValue ($KeyValue, 2);	
			
			$Key = str_replace ('"', "'", $Key);
			return $this->Database [$dbname]->exec ("UPDATE `{$Table}` SET {$KeyValue} WHERE {$Key};");
		}
		
		return false;
	}
	
	// $callback = function ($params) { ... }
	public function All (string $dbname, string $Table, string $params, $callback)
	{
		$dbname = md5 ($dbname);
		if (array_key_exists ($dbname, $this->Database))
		{
			$Result = $this->Database [$dbname]->query ("SELECT {$params} FROM {$Table}");
			while ($Assoc = $Result->fetchArray (SQLITE3_ASSOC))
				$callback ($Assoc);
			
			return true;
		}
		
		return false;
	}
	
	public function Counts (string $dbname, string $Table, string $Key)
	{
		$dbname = md5 ($dbname);
		if (array_key_exists ($dbname, $this->Database))
		{
			$Key = str_replace ('"', "'", $Key);
			return $this->Database [$dbname]->querySingle ("SELECT COUNT(*) FROM `{$Table}` WHERE {$Key};");
		}
		
		return false;
	}
	
	public function Exists (string $dbname, string $Table, string $Key)
	{
		return $this->Counts ($dbname, $Table, $Key);
	}
	
	public function Sql (string $dbname)
	{
		$dbname = md5 ($dbname);
		if (array_key_exists ($dbname, $this->Database))
			return $this->Database [$dbname];
		
		return false;
	}
}

?>