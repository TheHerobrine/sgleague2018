<?php
class Database
{
	private $database;

	public function __construct()
	{
		$hote = DTB_HOST;
		$db = DTB_NAME;
		$login = DTB_LOGIN;
		$pass = DTB_PASS; // Z'avez cru quoi ?

		try
		{
			$this->database = new PDO('mysql:host='.$hote.';dbname='.$db.';charset=UTF8', $login, $pass);
		}
		catch (Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		}
	}

	public function req_get($req)
	{
		$return = $this->database->query($req) or die(print_r($this->database->errorInfo())); 
		return $return;
	}

	public function req_post($req, array $params)
	{
		$resp = $this->database->prepare($req);
		$resp->execute($params);

		debug("Query Error", $resp->errorInfo());
		debug("Query Response", $resp);

		return $resp;
	}
}
?>