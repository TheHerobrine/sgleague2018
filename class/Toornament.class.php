<?php

class Toornament
{
	protected $access_token;

	public function __construct()
	{
		$curl = curl_init();

		curl_setopt_array(
		    $curl, array(
		    CURLOPT_URL             => 'https://api.toornament.com/oauth/v2/token?grant_type=client_credentials&client_id='.TOORNAMENT_ID.'&client_secret='.TOORNAMENT_SECRET,
		    CURLOPT_RETURNTRANSFER  => true,
		    CURLOPT_VERBOSE         => true,
		    CURLOPT_HEADER          => true,
		    CURLOPT_SSL_VERIFYPEER  => false,
		    CURLOPT_HTTPHEADER      => array(
		        'X-Api-Key: '.TOORNAMENT_API,
		        'Content-Type: application/json'
		    )
		));
		$output         = curl_exec($curl);
		$header_size    = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$header         = substr($output, 0, $header_size);
		$body           = json_decode(substr($output, $header_size));
		$this->access_token   = $body->access_token;

		debug("Toornament Body", $body);
	}

	public function execute($url, $type, $data)
	{
		$curl = curl_init();

		debug("Toornament data", $data);

		curl_setopt_array(
		    $curl, array(
		    CURLOPT_URL             => $url,
		    CURLOPT_RETURNTRANSFER  => true,
		    CURLOPT_VERBOSE         => true,
		    CURLOPT_HEADER          => true,
		    CURLOPT_SSL_VERIFYPEER  => false,
		    CURLOPT_HTTPHEADER      => array(
		        'X-Api-Key: '.TOORNAMENT_API,
		        'Authorization: Bearer '.$this->access_token,
		        'Content-Type: application/json'
		    ),
		    CURLOPT_CUSTOMREQUEST   => $type,
		    CURLOPT_POSTFIELDS      => $data
		));

		$output         = curl_exec($curl);
		$header_size    = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$header         = substr($output, 0, $header_size);
		$body           = json_decode(substr($output, $header_size));

		debug("Toornament Output", $output);
		debug("Toornament Body", $body);
	}
}

?>