<div id="content">
	<div class="container">
		<h1><i class="fa fa-angle-right" aria-hidden="true"></i> Panneau d'administration</h1>
		<p style="text-align: center;">
<?php

$games_profile[1]['rank'] = array(
	"Non Classé",
	"Bronze (1-1499)",
	"Silver (1500-1999)",
	"Gold (2000-2499)",
	"Platinum (2500-2999)",
	"Diamond (3000-3499)",
	"Master (3500-3999)",
	"Grand Master (4000-5000)"
);

$games_profile[2]['rank'] = array(
	"Non Classé",
	"Bronze",
	"Argent",
	"Or",
	"Platine",
	"Diamant",
	"Maitre",
	"Challenger"
);

$games_profile[3]['rank'] = array(
	"Non Classé",
	"Silver 1",
	"Silver 2",
	"Silver 3",
	"Silver 4",
	"Silver Elite",
	"Silver Elite Master",
	"Gold Nova 1",
	"Gold Nova 2",
	"Gold Nova 3",
	"Gold Nova Master",
	"Master Guardian 1",
	"Master Guardian 2",
	"Master Guardian Elite",
	"Distinguished Master Guardian",
	"Legendary Eagle",
	"Legendary Eagle Master",
	"Supreme Master First Class",
	"The Global Elite"
);

$games_profile[4]['rank'] = array(
	"Non Classé",
	"Division 5 (25-21)",
	"Division 4 (20-16)",
	"Division 3 (15-11)",
	"Division 2 (10-6)",
	"Division 1 (5-1)"
);
if ($_SESSION["sgl_type"] > 5)
{
	if (isset($_GET["gpage"]))
	{
		$get_game = intval($_GET["gpage"]);
		$url_game = "page=admin&amp;gpage=".$get_game;

		include_once("./class/Database.class.php");
		$database = new Database();



		if (isset($_GET["validc"]))
		{
			$temp = $database->req_post('UPDATE T_SGL_USER SET SU_VALID_CARD = 1 WHERE SU_UID=:uid',
			array(
					"uid" => $_GET["validc"]
				));
		}

		if (isset($_GET["unvalidc"]))
		{
			$temp = $database->req_post('UPDATE T_SGL_USER SET SU_VALID_CARD = 0 WHERE SU_UID=:uid',
			array(
					"uid" => $_GET["unvalidc"]
				));
		}

		$temp = $database->req_post('SELECT ST_UID, ST_NAME, ST_TAG, F_PATH FROM T_SGL_TEAM LEFT JOIN T_FILE ON F_UID=ST_ID_PICTURE_F WHERE ST_ID_G=:game',
			array(
					"game" => $get_game
				));

		if ($get_game == 4)
		{
			echo '<div class="adm_team">';
		}

		while($data = $temp->fetch())
		{
			if ($get_game != 4)
			{
				echo '<div class="adm_team">';

				$data["ST_TAG"] = $data["ST_TAG"]??"___";
				$data["ST_NAME"] = $data["ST_NAME"]??"Unnamed";

				echo '<h2>[ <b>'.$data["ST_TAG"].'</b> ] '.$data["ST_NAME"].($data["F_PATH"]==""?'':' <a href="'.RELATIVE_FILES_DIRECTORY.$data["F_PATH"].'"><i class="fa fa-picture-o" style="padding:0px 20px"></i></a>').'<a href="index.php?'.$url_game.'&export='.$data["ST_UID"].'"><i class="fa fa-share-square-o" style="padding:0px 20px"></i></a></h2><br />';
			}
			echo '<table>';

			$temp_p = $database->req_post('SELECT SU_UID, SU_VALID_CARD, SU_LOGIN, SU_MAIL, SU_FIRST_NAME, SU_LAST_NAME, SU_GENDER, S_NAME, GU_TYPE, GU_RANK, T_PLATFORM_USER.PU_PSEUDO, F_PATH, T_DISCORD.PU_PSEUDO AS PU_DISCORD FROM T_SGL_TEAM LEFT JOIN T_GAME_USER ON GU_ID_ST=ST_UID LEFT JOIN T_SGL_USER ON SU_UID=GU_ID_SU LEFT JOIN T_SCHOOL ON SU_ID_S=S_UID JOIN T_GAME ON G_UID=GU_ID_G LEFT JOIN T_PLATFORM_USER ON PU_ID_SU=SU_UID AND PU_ID_P=G_ID_P LEFT JOIN T_FILE ON F_UID=SU_ID_CARD_F LEFT JOIN T_PLATFORM_USER AS T_DISCORD ON T_DISCORD.PU_ID_SU=SU_UID AND T_DISCORD.PU_ID_P=3 WHERE ST_UID=:id_team ORDER BY GU_TYPE',
				array(
					"id_team" => $data["ST_UID"]
				));

			$export = false;
			$exTeam = array();

			if (isset($_GET["export"]) && $_GET["export"] == $data["ST_UID"])
			{

				$export = true;
				$exTeam["name"] = "[".$data["ST_TAG"]."] ".$data["ST_NAME"];
				$exTeam["sgl_id"] = $data["ST_UID"];
			}

			while($data_p = $temp_p->fetch())
			{
				$verif = false;
				if ((strlen($data_p["SU_LOGIN"]) > 1) && (strlen($data_p["SU_MAIL"]) > 1) && (strlen($data_p["S_NAME"]) > 1) && (strlen($data_p["SU_FIRST_NAME"]) > 1) && (strlen($data_p["PU_DISCORD"]) > 1) && (strlen($data_p["SU_LAST_NAME"]) > 1) && (strlen($data_p["PU_PSEUDO"]) > 1) && $data_p["SU_VALID_CARD"])
				{
					$verif = true;
				}

				echo '<tr>';
				echo '<td style="width:20px;text-align:center;">'.($verif?'<i class="fa fa-check" aria-hidden="true"></i>':'<i class="fa fa-times" style="color:#d00000" aria-hidden="true"></i>').'</td>';

				switch ($data_p["GU_TYPE"])
				{
					case 1:
					if ($export)
					{
						$exTeam["mail"] = $data_p["SU_MAIL"];
					}
					case 2:
					echo '<td style="width:20px;text-align:center;"><i class="fa fa-user" aria-hidden="true"></i></td>';
					break;
					case 3:
					echo '<td style="width:20px;text-align:center;"><i class="fa fa-user-plus" aria-hidden="true"></i></td>';
					break;
					case 4:
					echo '<td style="width:20px;text-align:center;"><i class="fa fa-graduation-cap" aria-hidden="true"></i></i></td>';
					break;
				}

				switch ($data_p["SU_GENDER"])
				{
					case 1:
					echo '<td style="width:20px;text-align:center;"><i class="fa fa-mars" aria-hidden="true"></i></td>';
					break;
					case 2:
					echo '<td style="width:20px;text-align:center;"><i class="fa fa-venus" aria-hidden="true"></i></td>';
					break;
					default:
					echo '<td style="width:20px;text-align:center;"><i class="fa fa-genderless" aria-hidden="true"></i></td>';
					break;
				}

				if ($export)
				{
					$exTeam["lineup"][] = array("name" => $data_p["SU_LOGIN"], "email" => $data_p["SU_MAIL"], "sgl_id" => $data_p["SU_UID"], "summoner_player_id" => $data_p["PU_PSEUDO"]);
				}
				
				$hash = md5(strtolower(trim($data_p["SU_MAIL"])));

				echo '<td style="width:20px;text-align:center;"><img style="border: 1px solid #ffc68f;margin-top:3px;margin-right: 4px;margin-left: 4px;" src="https://www.gravatar.com/avatar/'.$hash.'.png?d=retro&amp;s=16" /></td>';

				$playername = '';
				if ($data_p["SU_FIRST_NAME"] != "")
				{
					$playername = ' <span style="opacity:0.5">('.$data_p["SU_FIRST_NAME"].' '.$data_p["SU_LAST_NAME"].')</span>';
				}

				echo '<td style="width:15%">'.htmlspecialchars($data_p["SU_LOGIN"]).$playername.'</td>
				<td style="width:160px;">'.htmlspecialchars($data_p["S_NAME"]).'</td>';

				if ($data_p["F_PATH"] != "")
				{
					echo '<td style="width:20px;text-align:center;"><a target="_blank" href="'.RELATIVE_FILES_DIRECTORY.$data_p["F_PATH"].'"><i class="fa fa-address-card" aria-hidden="true"></a></td>';
				}
				else
				{
					echo '<td style="width:20px;text-align:center;"></td>';
				}

				if ($data_p["SU_VALID_CARD"])
				{
					echo '<td style="width:20px;text-align:center;"><a href="index.php?'.$url_game.'&amp;unvalidc='.$data_p["SU_UID"].'"><i class="fa fa-check-square-o" aria-hidden="true"></i></a></td>';
				}
				else
				{
					echo '<td style="width:20px;text-align:center;"><a href="index.php?'.$url_game.'&amp;validc='.$data_p["SU_UID"].'"><i class="fa fa-square-o" aria-hidden="true"></i></a></td>';
				}

				echo '<td>'.htmlspecialchars($data_p["PU_DISCORD"]).'</td>
				<td style="width:160px;">'.htmlspecialchars($games_profile[$get_game]['rank'][$data_p["GU_RANK"]]).'</td>
				<td style="width:160px;">'.htmlspecialchars($data_p["PU_PSEUDO"]).'</td>
				<td style="width:15px;text-align:center;"><a href="mailto:'.htmlspecialchars($data_p["SU_MAIL"]).'"><i class="fa fa-envelope" aria-hidden="true"></i></a></td>
				<td style="width:15px;text-align:center;"><a href="javascript:void(0);" onclick="copyTextToClipboard(\''.htmlspecialchars($data_p["SU_MAIL"]).'\')"><i class="fa fa-clipboard" aria-hidden="true"></i></a></td>';


				/*
				if ($get_game == 4)
				{
					if ($data["valid"] == 0)
					{
						echo '<td style="width:15px;text-align:center;"><a href="index.php?page=admin&amp;game='.$get_game.'&amp;team='.$data["user"].'&amp;valid=1"><i class="fa fa-square-o" aria-hidden="true"></i></a></td>';
					}
					else
					{
						echo '<td style="width:15px;text-align:center;"><a href="index.php?page=admin&amp;game='.$get_game.'&amp;team='.$data["user"].'&amp;valid=0"><i class="fa fa-check-square-o" aria-hidden="true"></i></a></td>';
					}
				}
				*/

				echo '</tr>';
			}

			echo '</table>';

			if ($export)
			{
				debug("Export", $exTeam);

				if ($get_game == 2)
				{
					include_once("./class/Toornament.class.php");
					$toornament = new Toornament();

					$toornament->execute('https://api.toornament.com/v1/tournaments/1108754586129408000/participants' ,'POST', json_encode($exTeam));
				}
			}

			if ($get_game != 4)
			{
				echo '</div>';
			}
			else
			{
				echo '<br />';
			}
		}
		if ($get_game == 4)
		{
			echo '</div>';
		}
	}
	else
	{
	?>
	<br /><br />
	<p style="text-align: center;">
		<a href="index.php?page=admin&amp;gpage=1" class="button">Overwatch</a>
		<a href="index.php?page=admin&amp;gpage=2" class="button">League of Legends</a>
		<a href="index.php?page=admin&amp;gpage=3" class="button">Counter Strike</a>
		<a href="index.php?page=admin&amp;gpage=4" class="button">Hearthstone</a>
	</p>

	<?php
	}
}

?>
		</p>
		<br />
	</div>
</div>