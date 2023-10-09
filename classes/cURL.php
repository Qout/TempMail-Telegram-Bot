<?php

class Http
{
	public $IsLoad = false;
	public $UserAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36';
	//public $Proxy_Params = [];
	
	public function __construct ()
	{
		$this->IsLoad = curl_init () ? true : false;
	}
	
	public function SetUserAgent (string $UserAgent)
	{
		$this->UserAgent = $UserAgent;
		return true;
	}
	
	/*public function Proxy (string $type, string $ip, $port, string $username = null, string $password = null)
	{
		$Params = [
			'type'	=> strtolower ($type),
			
			'ip'	=> $ip,
			'port'	=> $port,
		];
		
		if ($username != NULL && $username != NULL)
		
		$this->Proxy_Params = $Params;
	}*/
	
	public function Post (string $url, array $params, $http_build_query = false, array $http_headers = [], string $cookies = null, int $timeout_ms = -1)
	{
        if (!($curl = curl_init ($url))) return false;
		
        curl_setopt ($curl, CURLOPT_POST, true);
        curl_setopt ($curl, CURLOPT_POSTFIELDS, $http_build_query ? http_build_query ($params) : $params);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($curl, CURLOPT_USERAGENT, $this->UserAgent);
		curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, true);
		
		/*if (count ($this->Proxy_Params) > 1)
		{
			$Types = (object)[
				'socks4' 	=> CURLPROXY_SOCKS4,
				'socks5' 	=> CURLPROXY_SOCKS5,
				'http' 		=> CURLPROXY_HTTP,
				'https' 	=> CURLPROXY_HTTP,
			];
			
			curl_setopt ($curl, CURLOPT_PROXYTYPE, $Types->{$this->Proxy_Params ['type']});
			curl_setopt ($curl, CURLOPT_PROXY, "{$this->Proxy_Params ['ip']}:{$this->Proxy_Params ['port']}");
			if (array_key_exists ('login', $this->Proxy_Params) && array_key_exists ('password', $this->Proxy_Params))
				curl_setopt ($curl, CURLOPT_PROXYUSERPWD, "{$this->Proxy_Params ['login']}:{$this->Proxy_Params ['password']}");
		}*/
		
		if ($timeout_ms != -1)
		{
			curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT_MS, $timeout_ms);
			curl_setopt ($curl, CURLOPT_TIMEOUT_MS		, $timeout_ms);
		}
		
		if (!empty ($cookies))
			curl_setopt ($curl, CURLOPT_COOKIE, $cookies);
		
		if (count ($http_headers) > 0)
			curl_setopt ($curl, CURLOPT_HTTPHEADER, $http_headers);
		
        $response = curl_exec ($curl);
        curl_close ($curl);
		
        return $response;
    }
	
	public function Get (string $url, array $http_headers = [], string $cookies = null, int $timeout_ms = -1)
	{
        if (!($curl = curl_init ($url))) return false;
		
		curl_setopt ($curl, CURLOPT_URL, $url);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, true);

		curl_setopt ($curl, CURLOPT_USERAGENT, $this->UserAgent);
		curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, true);
		
		/*if (count ($this->Proxy_Params) > 1)
		{
			$Types = (object)[
				'socks4' 	=> CURLPROXY_SOCKS4,
				'socks5' 	=> CURLPROXY_SOCKS5,
				'http' 		=> CURLPROXY_HTTP,
				'https' 	=> CURLPROXY_HTTP,
			];
			
			curl_setopt ($curl, CURLOPT_PROXYTYPE, $Types->{$this->Proxy_Params ['type']});
			curl_setopt ($curl, CURLOPT_PROXY, "{$this->Proxy_Params ['ip']}:{$this->Proxy_Params ['port']}");
			if (array_key_exists ('login', $this->Proxy_Params) && array_key_exists ('password', $this->Proxy_Params))
				curl_setopt ($curl, CURLOPT_PROXYUSERPWD, "{$this->Proxy_Params ['login']}:{$this->Proxy_Params ['password']}");
		}*/
		
		if ($timeout_ms != -1)
		{
			curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT_MS, $timeout_ms);
			curl_setopt ($curl, CURLOPT_TIMEOUT_MS		, $timeout_ms);
		}
		
		if (!empty ($cookies))
			curl_setopt ($curl, CURLOPT_COOKIE, $cookies);
		
		if (count ($http_headers) > 0)
			curl_setopt ($curl, CURLOPT_HTTPHEADER, $http_headers);
		
        $response = curl_exec ($curl);
        curl_close ($curl);
		
        return $response;
    }
}

?>