<?php
class OpenID_Client
{
	public $trustRoot;
	public $returnTo;
	public $errors;
	
	private $identity;
	private $fields;
	private $realIdentity;
	private $server;
	
	public function __construct()
	{
		if(!function_exists('curl_init'))
		{
			trigger_error('OpenID_Client::__construct(): The cURL extension is required.');
			return(false);
		}
		
		$this->fields = array(
			'required' => array(),
			'optional' => array()
		);
	}
	
	# @param string $names
	# [@param boolean $optional]	
	public function addFields($names,$optional = false)
	{
		if(!is_array($names)) $names = array($names);
		
		foreach($names as $name)
		{
			if(!$optional) $this->fields['required'][] = $name;
			else $this->fields['optional'][] = $name;
		}
		return(true);
	}
	
	public function setIdentity($identity)
	{
		if(!preg_match('#^http://(.*?)$#',$identity)) $identity = 'http://' . $identity;
		$this->identity = $identity;
		return(true);
	}
	
	public function make()
	{
		if(!$this->getOpenIDServer($this->identity)) return(false);
		
		if(count($delegates) > 0 && !empty($delegates[0])) $this->realIdentity = $delegates[0];
		else $this->realIdentity = $this->identity;
		
		$this->redirect();
		return(true);
	}
	
	public function getOpenIDServer($identity)
	{
		$response = $this->cURL($identity);
		list($servers,$delegates) = $this->parseLinks($response);
		
		if(count($servers) == 0 || $servers[0] == '')
		{
			$this->errors[] = 'NOSERVER';
			trigger_error('OpenID_Client::make(): No server found.');
			return(false);
		}
		$this->server = $servers[0];
		
		return(true);
	}
	
	private function parseLinks($response)
	{
		preg_match_all('/<link[^>]*rel="openid.server"[^>]*href="([^"]+)"[^>]*\/?>/i',$response,$matches1);
		preg_match_all('/<link[^>]*href="([^"]+)"[^>]*rel="openid.server"[^>]*\/?>/i',$response,$matches2);
		$servers = array_merge($matches1[1],$matches2[1]);
		
		preg_match_all('/<link[^>]*rel="openid.delegate"[^>]*href="([^"]+)"[^>]*\/?>/i',$response,$matches1);
		preg_match_all('/<link[^>]*href="([^"]+)"[^>]*rel="openid.delegate"[^>]*\/?>/i',$response,$matches2);
		$delegates = array_merge($matches1[1],$matches2[1]);
		
		return(array($servers,$delegates));
	}
	
	private function parseResponse($response)
	{
		preg_match_all('/^([\w-]+):[\s]?([\w-]+)[\s]?$/m',$response,$arr);
		
		$response = array();
		foreach($arr[1] as $i => $key)
		{
			$response[$key] = $arr[2][$i];
		}
		return($response);
	}
	
	private function redirect()
	{
		if(!empty($this->returnTo) && !empty($this->realIdentity) && !empty($this->trustRoot) && !empty($this->server))
		{
			$params =
				'openid.return_to=' . urlencode($this->returnTo) .
				'&openid.mode=checkid_setup' .
				'&openid.identity=' . urlencode($this->realIdentity) .
				'&openid.trust_root=' . urlencode($this->trustRoot);
			
			if(count($this->fields['required']) > 0) $params .= '&openid.sreg.required=' . implode(',',$this->fields['required']);
			if(count($this->fields['optional']) > 0) $params .= '&openid.sreg.optional=' . implode(',',$this->fields['optional']);
			
			$uri = $this->server . '?' . $params;
			
			//exit(http_build_query($params));
//			print_r($params);die($uri);
			if(!headers_sent()) header('Location: ' . $uri);
			else print('<script type="text/javascript">window.location=\'' . $uri . '\';</script>'); 
		}
	}
	
	private function cURL($uri,$method = 'GET',$params = '')
	{
		if(is_array($params)) $params = http_build_query($params);
		
		$cURL = curl_init($uri . (($method == 'GET' && !empty($params)) ? '?' . $params : ''));
		@curl_setopt($cURL,CURLOPT_FOLLOWLOCATION,true);
		curl_setopt($cURL,CURLOPT_HEADER,false);
		curl_setopt($cURL,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($cURL,CURLOPT_HTTPGET,($method == 'GET' ? true : false));
		curl_setopt($cURL,CURLOPT_POST,($method == 'POST' ? true : false));
		if($method == 'POST') curl_setopt($cURL,CURLOPT_POSTFIELDS,$params);
		curl_setopt($cURL,CURLOPT_RETURNTRANSFER,true);
		
		$response = curl_exec($cURL);
		if(curl_errno($cURL) != 0) $this->errors[] = 'CURLERROR: ' . curl_error($cURL);
		
		return($response);
	}
	
	public function validate($format = 0)
	{
		if(!isset($_GET['openid_mode'],$_GET['openid_assoc_handle'],$_GET['openid_signed'],$_GET['openid_sig'])) { trigger_error('OpenID_Client::validate(): Invalid response'); return(false); }
		//,$_GET['openid_identity'],$_GET['openid_signed']) || $_GET['openid_mode'] != 'id_res')
		else
		{
			switch($_GET['openid_mode']):
				case('id_res'): break;
				case('cancel'): trigger_error('OpenID_Client::validate(): User rejected the request!'); return(false);
				default: trigger_error('OpenID_Client::validate(): Invalid response'); return(false);
			endswitch;
			
			if(!isset($_GET['openid_identity'])) { trigger_error('OpenID_Client::validate(): Invalid response'); return(false); }
			else
			{
				$params = 'openid.assoc_handle=' . urlencode($_GET['openid_assoc_handle']) .
					'&openid.signed=' . urlencode($_GET['openid_signed']) .
					'&openid.sig=' . urlencode($_GET['openid_sig']) .
					'&openid.return_to=' . urlencode($this->returnTo) . 
					'&openid.identity=' . urlencode($_GET['openid_identity']);
				
				$fields = array();
				foreach($_GET as $key => $value)
					if(preg_match('#^openid_sreg_([a-z]+)$#s',$key,$arr)) $fields[$arr[1]] = $value;
				
				if($format)
				{
					$output = array(
						'required' => array(),
						'optional' => array()
					);
					
					foreach($this->fields['optional'] as $name)
						if(isset($fields[$name])) $output['optional'][$name] = $fields[$name];
				}
				
				foreach($this->fields['required'] as $name)
				{
					if(!isset($fields[$name]))
					{
						trigger_error('OpenID_Client::validate(): Invalid response. The required field <strong>' . $name . '</strong> has not been returned.');
						return(false);
					}
					elseif($format) $output['required'][$name] = $fields[$name];
					
					$params .= '&openid.sreg.' . $name . '=' . urlencode($fields[$name]); 
				}
				
				// Validate with server
				$params .= '&openid.mode=check_authentication';
				
				if(!$this->getOpenIDServer($_GET['openid_identity'])) return(false);
				
				$response = $this->cURL($this->server,'POST',$params);
				$response = $this->parseResponse($response);
				
				if(!isset($response['is_valid']) || $response['is_valid'] != 'true') return(false);
				
				// Output				
				if($format) return($output);
				return($fields);
			}
		}
	}
	
	function ValidateWithServer(){
		$params = array(
			'openid.assoc_handle' => urlencode($_GET['openid_assoc_handle']),
			'openid.signed' => urlencode($_GET['openid_signed']),
			'openid.sig' => urlencode($_GET['openid_sig'])
		);
		
		// Send only required parameters to confirm validity
		$arr_signed = explode(",",str_replace('sreg.','sreg_',$_GET['openid_signed']));
		for ($i=0; $i<count($arr_signed); $i++){
			$s = str_replace('sreg_','sreg.', $arr_signed[$i]);
			$c = $_GET['openid_' . $arr_signed[$i]];
			// if ($c != ""){
				$params['openid.' . $s] = urlencode($c);
			// }
		}
		$params['openid.mode'] = "check_authentication";

		$openid_server = $this->GetOpenIDServer();
		if ($openid_server == false){
			return false;
		}
		$response = $this->CURL_Request($openid_server,'POST',$params);
		$data = $this->splitResponse($response);

		if ($data['is_valid'] == "true") {
			return true;
		}else{
			return false;
		}
	}
}
/*
Array (
[openid.return_to] => http://oid2.blackpig.cz/validate.php?c=blog&section=comment&mode=add&openid
[openid.mode] => checkid_setup
[openid.identity] => http://looksmog.myid.net
[openid.trust_root] => http://oid2.blackpig.cz/
[openid.sreg.required] => nickname,email
)

https://server.myid.net/server?openid.return_to=http%3A%2F%2Foid2.blackpig.cz%2Fvalidate.php%3Fc%3Dblog%26section%3Dcomment%26mode%3Dadd%26openid&openid.mode=checkid_setup&openid.identity=http%3A%2F%2Flooksmog.myid.net&openid.trust_root=http%3A%2F%2Foid2.blackpig.cz%2F&openid.sreg.required=nickname%2Cemail

*/
?>