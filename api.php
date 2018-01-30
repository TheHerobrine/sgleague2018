<?php

session_start();

include_once("./config.php");
function debug($a, $b){/*print_r($b);*/}

$get_type = isset($_GET['type']) ? $_GET['type'] : '';

if (isset($_SESSION["sgl_id"]) || $get_type=="search_school" || $get_type=="bot")
{

	switch($get_type)
	{
		case "search_school":

			header('Content-Type: application/json');
			include_once("./class/Form.class.php");

			$fields = array(
				'school' => array('type' => 'value', 'value' => '%'.$_GET["school"].'%')
			);

			$query = "CALL SEARCH_SCHOOL(:school)";

			$form = new Form(new Database(), $query, $fields);
			if($form->is_valid())
			{
				$return = $form->send();
			
				while ($data = $return->fetch(PDO::FETCH_ASSOC))
				{
					$result[] = $data;
				}
			}

			if (isset($result))
			{
				echo json_encode($result);
			}
			else
			{
				echo "[]";
			}
			break;

		case "search_mail":

			header('Content-Type: application/json');
			include_once("./class/Form.class.php");

			$fields = array(
				'mail' => array('type' => 'string', 'length' => '128'),
				'game' => array('type' => 'integer')
			);

			$query = "CALL SEARCH_MAIL(:mail,:game)";

			$form = new Form(new Database(), $query, $fields, METHOD_GET);
			if($form->is_valid())
			{
				$return = $form->send();
			
				while ($data = $return->fetch(PDO::FETCH_ASSOC))
				{
					$result[] = $data;
				}
			}

			if (isset($result))
			{
				echo json_encode($result);
			}
			else
			{
				echo "[]";
			}
			break;

		case "bot":

			header('Content-Type: application/json');
			include_once("./class/Form.class.php");

			if ($_GET["key"] == API_KEY)
			{
				$database = new Database();
				if ($_GET["player"])
				{
					$temp = $database->req_post('SELECT SU_LOGIN, SU_MAIL, SU_FIRST_NAME, SU_LAST_NAME, S_NAME, GU_TYPE, GU_RANK, ST_UID, ST_NAME, ST_TAG FROM T_SGL_USER LEFT JOIN T_SCHOOL ON SU_ID_S=S_UID LEFT JOIN T_GAME_USER ON GU_ID_G=:game AND GU_ID_SU = SU_UID LEFT JOIN T_SGL_TEAM ON GU_ID_ST=ST_UID WHERE SU_UID=:id_user',
						array(
							"id_user" => $_GET["player"],
							"game" => $_GET["game"]
						));
					$result = $temp->fetch(PDO::FETCH_ASSOC);

					if ($result)
					{
						echo json_encode($result);
					}
					else
					{
						echo '{"error": -2, "description": "No player found"}';
					}
				}
				else if ($_GET["team"])
				{


					$temp = $database->req_post('SELECT ST_UID, ST_NAME, ST_TAG FROM T_SGL_TEAM WHERE ST_UID=:id_team',
						array(
							"id_team" => $_GET["team"]
						));

					$result = $temp->fetch(PDO::FETCH_ASSOC);

					$temp = $database->req_post('SELECT SU_UID, SU_LOGIN, SU_MAIL, SU_FIRST_NAME, SU_LAST_NAME, S_NAME, GU_TYPE, GU_RANK FROM T_SGL_TEAM LEFT JOIN T_GAME_USER ON GU_ID_ST=ST_UID LEFT JOIN T_SGL_USER ON SU_UID=GU_ID_SU LEFT JOIN T_SCHOOL ON SU_ID_S=S_UID WHERE ST_UID=:id_team',
						array(
							"id_team" => $_GET["team"]
						));

					while ($data = $temp->fetch(PDO::FETCH_ASSOC))
					{
						$result["players"][] = $data;
					}

					if ($result)
					{
						echo json_encode($result);
					}
					else
					{
						echo '{"error": -2, "description": "No team found"}';
					}
				}
				else
				{
					echo '{"error": -3, "description": "Missing keyword"}';
				}
			}
			else
			{
				echo '{"error": -1, "description": "Wrong key"}';
			}

			break;
		default:
			header('Content-Type: application/json');
			echo '{"error": -1000, "description": "Wrong use of API"}';
	}
}
else
{
	header('Content-Type: application/json');
	echo '{"error": -1000, "description": "Wrong use of API"}';
}

?>