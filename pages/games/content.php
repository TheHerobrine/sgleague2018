<?php

global $csrf_check;

include_once("./class/Database.class.php");
include_once("./class/Form.class.php");

$database = new Database();

// 1 - Overwatch
// 2 - League of Legends
// 3 - Counter Strike
// 4 - Hearthstone

$games = array(1, 2, 3, 4);

$games_name = array(
	"Overwatch",
	"League of Legends",
	"Counter Strike",
	"Hearthstone");

$games_short = array(
	"ow",
	"lol",
	"csgo",
	"hs");

$games_quote = array(
	"Ryu ga waga teki wo kurau !",
	"Captain Teemo on duty !",
	"Rush B my friend ! Don't stop, don't stop...",
	"You face Jaraxxus,<br />eredar lord of the burning legion !");


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

function display_card($data, $player_type, $game_data, $get_game, $game_profile, $is_lead = false)
{
	$valid = true;
	$editable = (($data["SU_ID_PARENT_SU"] == $_SESSION["sgl_id"]) || ($data["SU_UID"] == $_SESSION["sgl_id"]));
	$deletable = (($is_lead) && ($data["SU_UID"] != $_SESSION["sgl_id"]));

	$edit_button = '';
	if ($editable)
	{
		$edit_button = "<span style=\"padding-left:10px;color:#ffc68f;\">[ <a href=\"index.php?page=account&amp;uid=".$data["SU_UID"]."\">Editer</a> ]</span>";
	}

	$delete_button = '';
	if ($deletable)
	{
		$delete_button = "<span style=\"padding-left:10px;color:#ffc68f;\">[ <a href=\"index.php?page=games&amp;gpage=".$get_game."&amp;action=del&amp;player=".$data["SU_UID"]."\">X</a> ]</span>";
	}

	$error_login = '';
	if ($data["SU_LOGIN"] == '')
	{
		$error_login = "<span class=\"error\">MissingNo</span>";
		$valid = false;
	}

	$error_pseudo = '';
	if ($data["PU_PSEUDO"] == '')
	{
		$error_pseudo = "<span class=\"error\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>Manquant</span>";
		$valid = false;
	}

	$error_card = '';
	if (!$data["SU_ID_CARD_F"])
	{
		$error_card = " <span class=\"error\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>Carte étudiante</span>";
		$valid = false;
	}

	$error_school = '';
	if ($data["S_NAME"] == '')
	{
		$error_school = "<span class=\"error\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>Ecole</span>";
		$valid = false;
	}

	$error_first_name = '';
	if ($data["SU_FIRST_NAME"] == '')
	{
		$error_first_name = "<span class=\"error\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>Prénom</span>";
		$valid = false;
	}

	$error_last_name = '';
	if ($data["SU_LAST_NAME"] == '')
	{
		$error_last_name = "<span class=\"error\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>Nom</span>";
		$valid = false;
	}

	$info_valid = " <i class=\"fa fa-check\" aria-hidden=\"true\"></i>";

	if (!$valid)
	{
		$info_valid = " <span class=\"error\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></span>";
	}

	$hash = md5(strtolower(trim($data["SU_MAIL"])));
	echo '<span class="playercard">
	<a style="float:left" href="https://www.gravatar.com/'.$hash.'"><img style="border: 1px solid #ffc68f;" src="https://www.gravatar.com/avatar/'.$hash.'.png?d=retro&amp;s=60" /></a>
	<span class="playername">'.htmlspecialchars($data["SU_LOGIN"]).$error_login.$info_valid.'</span>
	<span style="opacity:0.5;">[</span><span style="text-transform:uppercase;font-size:10px">'.htmlspecialchars($data["SU_FIRST_NAME"]).$error_first_name.'
	'.htmlspecialchars($data["SU_LAST_NAME"]).$error_last_name.'
	<span style="opacity:0.5;">-</span> '.htmlspecialchars($data["S_NAME"]).$error_school.'</span><span style="opacity:0.5;">]</span>'.$error_card.'
	<span class="playertype">'.$player_type.$edit_button.$delete_button.'</span><br />
	<span class="playerplatform"><span style="opacity:0.5;">'.$game_data["P_PSEUDO_NAME"].' :</span> '.htmlspecialchars($data["PU_PSEUDO"]).$error_pseudo.'
	<span style="opacity:0.5;margin:0px 5px;">|</span> '.$data["SU_MAIL"].'</span>
	<span class="playerrank">'.$game_profile[$data["GU_RANK"]].'</span>
	</span><br />';
}


?>
<div id="content">

<?php
$single = false;
$get_game = 0;

if (isset($_GET["gpage"]))
{
	$get_game = intval($_GET["gpage"]);
	if (in_array($get_game, $games))
	{
		echo '<div class="top_ban" style="background-image: url(\'./style/img/ban/top_'.$games_short[$get_game-1].'.png\');"></div>';


?>
	<div class="container">
		<h1><i class="fa fa-angle-right" aria-hidden="true"></i> Tournoi <?=$single?$games_name[$get_game-1]:''?></h1>
		<div class="quote">
			<span class="qcontent">
				<i>&ldquo;</i>
<?php

	echo $games_quote[$get_game-1];

?>
				<i>&rdquo;</i>
			</span>
			<span class="qauthor">
				- Un joueur de la SGL 2017
			</span>
		</div>
		<br />
<?php

	echo '<p id="'.$games_short[$get_game-1].'"><table class="line_table"><tr><td><hr class="line" /></td><td><img src="./style/img/games/'.$games_short[$get_game-1].'.png" alt="'.$games_name[$get_game-1].'" /></td><td><hr class="line" /></td></tr></table></p>';


	switch($get_game)
	{
		case 1:
		?>
			<p style="text-align:center;">
		<b><i class="fa fa-user-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> 6 joueurs par équipe
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-calendar" aria-hidden="true" style="padding-right: 5px;"></i></b> Les Lundis (début le 26 Février)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-trophy" aria-hidden="true" style="padding-right: 5px;"></i></b> Récompenses à venir ;)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-line-chart" aria-hidden="true" style="padding-right: 5px;"></i></b> 34 équipes l'année dernière<br /><br />
		<span style="padding: 10px;">Finale le <b style="font-weight:bold;">12 Mai</b></span><!--<br /><br />
		<a href="./files/rules_sgl2017.pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Télécharger le règlement</a>--></p>
		<?php
			break;
		case 2:
		?>
	<p style="text-align:center;">
		<b><i class="fa fa-user-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> 5 joueurs par équipe
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-calendar" aria-hidden="true" style="padding-right: 5px;"></i></b> Les Mardis (début le 27 Février)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-trophy" aria-hidden="true" style="padding-right: 5px;"></i></b> Récompenses à venir ;)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-line-chart" aria-hidden="true" style="padding-right: 5px;"></i></b> 102 équipes l'année dernière<br /><br />
		<span style="padding: 10px;">Finale le <b style="font-weight:bold;">5 Mai</b></span><!--<br /><br />
		<a href="./files/rules_sgl2017.pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Télécharger le règlement</a>--></p>
		<?php
			break;
		case 3:
		?>
	<p style="text-align:center;">
		<b><i class="fa fa-user-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> 5 joueurs par équipe
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-calendar" aria-hidden="true" style="padding-right: 5px;"></i></b> Les Mercredis (début le 28 Février)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-trophy" aria-hidden="true" style="padding-right: 5px;"></i></b> Récompenses à venir ;)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-line-chart" aria-hidden="true" style="padding-right: 5px;"></i></b> 62 équipes l'année dernière<br /><br />
		<span style="padding: 10px;">Finale le <b style="font-weight:bold;">12 Mai</b></span><!--<br /><br />
		<a href="./files/rules_sgl2017.pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Télécharger le règlement</a>--></p>
		<?php
			break;
		case 4:
		?>
	<p style="text-align:center;">
		<b><i class="fa fa-user-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> Tournoi solo
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-calendar" aria-hidden="true" style="padding-right: 5px;"></i></b> Les Dimanches (début le 25 Février)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-trophy" aria-hidden="true" style="padding-right: 5px;"></i></b> Récompenses à venir ;)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-line-chart" aria-hidden="true" style="padding-right: 5px;"></i></b> 384 joueurs l'année dernière<br /><br />
		<span style="padding: 10px;">Finale le <b style="font-weight:bold;">5 Mai</b></span><!--<br /><br />
		<a href="./files/rules_sgl2017.pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Télécharger le règlement</a>--></p>
		<?php
			break;
	}
if (isset($_SESSION["sgl_id"]))
{
	if ($_GET["action"]=="create")
	{
		$database->req_post('CALL INSERT_SGL_TEAM(:id_user, :game)',
		array(
			"id_user" => $_SESSION['sgl_id'],
			"game" => $get_game
		));
	}

	if ($_GET["action"]=="leave")
	{
		$database->req_post('CALL UPDATE_SGL_USER_LEAVES_TEAM(:id_user, :game)',
		array(
			"id_user" => $_SESSION['sgl_id'],
			"game" => $get_game
		));
	}

	$error_teamtag ='';

	if (isset($_POST["sent"]))
	{
		$form_teamtag = isset($_POST['teamtag']) ? $_POST['teamtag'] : '';
		$form_teamname = isset($_POST['teamname']) ? $_POST['teamname'] : '';

		if (strlen($form_teamtag) != 0)
		{
			if (!preg_match("/^[A-Za-z0-9]{3,4}$/", $form_teamtag))
			{
				$check_teamtag = -1;
				$error_teamtag = "Hep ! Seulement 3 ou 4 caractères, et seulement des lettres et des chiffres !";
				$form_teamtag = '';
			}
			else
			{
				$temp = $database->req_post('SELECT ST_TAG FROM T_SGL_TEAM WHERE ST_ID_G=:game AND ST_ID_LEAD_SU!=:id_user',
					array(
						"id_user" => $_SESSION['sgl_id'],
						"game" => $get_game
					));
				$data = $temp->fetch();

				if ($data["ST_TAG"])
				{
					$check_teamtag = -2;
					$error_teamtag = "Désolé, mais ce TAG est déjà utilisé... Soyez plus original !";
					$form_teamtag = '';
				}
			}
		}
		$database->req_post('UPDATE T_SGL_TEAM SET ST_NAME=:team_name, ST_TAG=:team_tag WHERE ST_ID_LEAD_SU=:id_user AND ST_ID_G=:game',
		array(
			"id_user" => $_SESSION['sgl_id'],
			"game" => $get_game,
			"team_tag" => $form_teamtag,
			"team_name" => $form_teamname
		));

		if ($check_teamtag < 0)
		{
			$error_teamtag = "<div class=\"error\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>".$error_teamtag."</div>";
		}

		//  ----- [ Card Update ] --------------------------------------------------

		debug("FILES", $_FILES);
		$error_file = '';
		if (isset($_FILES["f_logo"]))
		{
			if ($_FILES["f_logo"]["error"] == 0)
			{
				$types = array(
					image_type_to_mime_type(IMAGETYPE_JPEG),
					image_type_to_mime_type(IMAGETYPE_JPEG2000),
					image_type_to_mime_type(IMAGETYPE_PNG)
				);
				$fields = array(
					'id_user' => array('type' => 'value', 'value' => $_SESSION["sgl_id"]),
					'id_game' => array('type' => 'value', 'value' => $get_game),
					'f_logo' => array('type' => 'file', 'types' =>  $types, 'max_size' => 8000000, 'destination' => '/', 'max_width' => 1600, 'max_height' => 1600)
				);

				$query = "CALL UPDATE_SGL_TEAM_LOGO(:id_user, :id_game, :f_logo)";

				$form = new Form($database, $query, $fields);

				if($form->is_valid())
				{
					$return = $form->send();
					if($data = $return->fetch())
					{
						$return->closeCursor();
						if($data['TO_DELETE'])
						{
							$file = new File($database);
							if($file->init_for_get($data['FILE']))
							{
								$file->delete();
							}
						}
					}
					else
					{
						$return->closeCursor();
					}
				}
			}
			else if ($_FILES["f_logo"]["error"] == 1)
			{
				$error_file = "<div class=\"error\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>Ce fichier est trop gros, on a pas un espace de stockage infini, hein !</div>";
			}
		}

	}

	$temp = $database->req_post('SELECT G_NAME, G_RANK_DESCRIPTION, G_NUM_PLAYERS, G_NUM_SUBSTITUTES, G_ID_P, P_PSEUDO_NAME FROM T_GAME, T_PLATFORM WHERE P_UID=G_ID_P AND G_UID = :game',
		array(
			"game" => $get_game
		));
	$data = $temp->fetch();

	$game_data = $data;

	$temp = $database->req_post('SELECT GU_ID_ST FROM T_GAME_USER WHERE GU_ID_SU=:id_user AND GU_ID_G = :game',
		array(
			"id_user" => $_SESSION['sgl_id'],
			"game" => $get_game
		));
	$data = $temp->fetch();

	$is_team = $data["GU_ID_ST"] > 0;
	$team_id = $data["GU_ID_ST"];
	$url_game = "&amp;gpage=".$get_game;

	echo '<p class="smallquote" style="text-align:center;">
	Vous devez rejoindre notre <a href="https://discord.gg/sgnw">serveur discord</a> pour participer au tournoi !</p>';

	if ($is_team)
	{
		echo '<p style="text-align:center; font-weight: bold">Yay ! <b>Vous êtes inscrit</b> à ce tournoi ! Un premier pas vers la victoire...</p>';
		echo '<p style="text-align: center;" class="smallquote">Plus qu\'à hard train jusqu\'à fin Février. [ <a href="index.php?page=games'.$url_game.'&amp;action=leave">Se désinscrire du tournoi</a> ]</p><br />';
		//echo '<p style="text-align: center;" class="smallquote">Le tournoi a commencé... Bon courage !</p><br />';

		$temp = $database->req_post('SELECT ST_TAG, ST_NAME, ST_STATUS, ST_RANK, ST_ID_LEAD_SU, ST_ID_PICTURE_F FROM T_SGL_TEAM WHERE ST_UID=:team_id',
			array(
				"team_id" => $team_id
			));
		$data = $temp->fetch();
		$is_lead = ($data["ST_ID_LEAD_SU"] == $_SESSION["sgl_id"]);

		if ($game_data["G_NUM_PLAYERS"] > 1)
		{
			echo '<p><table class="line_table"><tr><td><hr class="line" /></td><td>Equipe</td><td><hr class="line" /></td></tr></table></p>';

			$logo_file = new File($database);

			if($is_lead)
			{
				echo '<div class="form"><form action="index.php?page=games'.$url_game.'" method="post" enctype="multipart/form-data">
					<table class="form_table">
						<tr><td><h3>Nom d\'équipe :</h3></td><td colspan="2"><input value="'.htmlspecialchars($data["ST_NAME"]).'" name="teamname" type="text"><br />
						<div class="smallquote">Le nom de votre équipe, genre "Télécom Bretagne Gaming"</div></td></tr>
						<tr><td><h3>TAG d\'équipe :</h3></td><td colspan="2"><input value="'.htmlspecialchars($data["ST_TAG"]).'" name="teamtag" type="text"><br />
						'.$error_teamtag.'
						<div class="smallquote">Votre tag en 3 ou 4 caractères, genre "TBG" ou "TBG2" (que des lettres et des chiffres par contre !)</div></td></tr>
											<tr><td><h3>Logo :</h3></td><td><input style="padding: 5px 10px;width: 464px;" type="file" name="f_logo" accept="image/png, image/jpeg" size="10000000"/>
						<br/>
						'.$error_file.'
						<div class="smallquote">Il sera affiché sur les infographies de récap de vos matchs !</div>
					</td>
					<td>
						'.($logo_file->init_for_get($data["ST_ID_PICTURE_F"])?
						'<a href="'.$logo_file->get_url().'" target="_blank"><img style="width: 33px;height: 33px;border: 1px solid #ffc68f;" src="'.$logo_file->get_url().'" alt="logo"/></a>':'').'
					</td>
					</tr>
					</table><br /><br />
					<input type="hidden" name="sent" value="sent">
					<button type="submit" value="Submit">Mettre à jour</button>
					</form></div><br />';
			}
			else
			{
				echo '<div class="form">
					<table class="form_table">
						<tr><td><h3>Nom d\'équipe :</h3></td><td><input value="'.htmlspecialchars($data["ST_NAME"]).'" disabled="disabled" name="teamname" type="text"><br />
						<div class="smallquote">Le nom de votre équipe, genre "Télécom Bretagne Gaming"</div></td></tr>
						<tr><td><h3>TAG d\'équipe :</h3></td><td><input value="'.htmlspecialchars($data["ST_TAG"]).'" disabled="disabled" name="teamtag" type="text"><br />
						<div class="smallquote">Votre tag en 3 ou 4 caractères, genre "TBG" ou "TBG2" (que des lettres et des chiffres par contre !)</div></td></tr>
					</table></div>';
			}
		}

		echo '<p><table class="line_table"><tr><td><hr class="line" /></td><td>Joueurs</td><td><hr class="line" /></td></tr></table></p>';

		echo '<p style="text-align: center;" class="smallquote">Si vous ajoutez un joueur non inscrit, vous pourrez compléter ses informations.<br />
		Si vous invitez un joueur déjà inscrit, il devra remplir ses informations lui-même.</p>';

		if (($_GET["action"]=="add") && ($is_lead))
		{
			if(filter_var($_GET["mail"], FILTER_VALIDATE_EMAIL))
			{
				$temp = $database->req_post('SELECT COUNT(*) AS total FROM T_GAME_USER WHERE GU_ID_ST=:id_team AND GU_TYPE=:type',
					array(
						"id_team" => $team_id,
						"type" => $_GET["type"],
					));

				$data = $temp->fetch();

				if(($data["total"] < 1) && ($_GET["type"] == 4)
					|| ($data["total"] < $game_data["G_NUM_SUBSTITUTES"]) && ($_GET["type"] == 3)
					|| ($data["total"] < $game_data["G_NUM_PLAYERS"]) && ($_GET["type"] == 2))
				{
					$temp = $database->req_post('CALL INSERT_TEAM_MAIL(:id_team, :id_lead, :mail, :type, :id_game)',
						array(
							"id_team" => $team_id,
							"id_lead" => $_SESSION["sgl_id"],
							"mail" => $_GET["mail"],
							"type" => $_GET["type"],
							"id_game" => $get_game
						));

					$data = $temp->fetch();

					if(!$data["RESULT"])
					{
						echo "<div style=\"text-align:center\" class=\"error\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>
						Le joueur ".htmlspecialchars($_GET["mail"])." est déjà dans une équipe.</div>";
					}
				}
				else
				{
					echo "<div style=\"text-align:center\" class=\"error\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>
						Vous ne pouvez pas ajouter ce joueur.</div>";
				}
			}
		}

	if (($_GET["action"]=="del") && ($is_lead))
	{
		if($_GET["player"] != $_SESSION["sgl_id"])
		{
			$temp = $database->req_post('UPDATE T_GAME_USER SET GU_ID_ST=NULL WHERE GU_ID_SU=:id_user AND GU_ID_ST=:id_team',
				array(
					"id_user" => $_GET["player"],
					"id_team" => $team_id
				));
		}
	}

		$temp = $database->req_post('SELECT SU_ID_PARENT_SU, SU_ID_CARD_F, SU_LOGIN, SU_UID, SU_MAIL, PU_PSEUDO, GU_RANK, S_NAME, SU_FIRST_NAME, SU_LAST_NAME, GU_TYPE FROM T_SGL_USER
			JOIN T_GAME_USER ON SU_UID=GU_ID_SU JOIN T_GAME ON GU_ID_G=G_UID LEFT JOIN T_PLATFORM_USER ON PU_ID_SU=SU_UID AND PU_ID_P=G_ID_P LEFT JOIN T_SCHOOL ON SU_ID_S=S_UID WHERE GU_ID_ST=:team_id
			ORDER BY GU_TYPE, SU_UID',
			array(
				"team_id" => $team_id
			));

		$player_type = array("Aucun", "Capitaine", "Joueur", "Remplaçant", "Coach");

		$nplayer = 0;
		$nreps = 0;

		echo '<div style="text-align: center">';

		$data = $temp->fetchAll();

		$current_player = 0;
		display_card($data[$current_player], $player_type[1], $game_data, $get_game, $games_profile[$get_game]['rank'], $is_lead);
		$current_player++;

		$randhash= time();

		for ($iplayer=1;$iplayer<$game_data["G_NUM_PLAYERS"];$iplayer++)
		{
			if (isset($data[$current_player]))
			{
				if($data[$current_player]["GU_TYPE"] == 2)
				{
					display_card($data[$current_player], $player_type[2], $game_data, $get_game, $games_profile[$get_game]['rank'], $is_lead);

					$current_player++;

					continue;
				}
			}
			
			$hash = md5("player".$iplayer.$randhash);
			echo '<span class="playercard">
			<span style="float:left"><img style="border: 1px solid #ffc68f;" src="https://www.gravatar.com/avatar/'.$hash.'.png?d=retro&amp;s=60" /></span>
			'.($is_lead?'<span class="playerbutton" onclick="addPlayer(this, 2)">Ajouter</span>':'').'
			<span class="playertype">'.$player_type[2].'</span>
			</span><br />';
		}

		for ($iplayer=0;$iplayer<$game_data["G_NUM_SUBSTITUTES"];$iplayer++)
		{
			if (isset($data[$current_player]))
			{
				if($data[$current_player]["GU_TYPE"] == 3)
				{
					display_card($data[$current_player], $player_type[3], $game_data, $get_game, $games_profile[$get_game]['rank'], $is_lead);

					$current_player++;

					continue;
				}
			}
			
			$hash = md5("substitute".$iplayer.$randhash);
			echo '<span class="playercard">
			<span style="float:left"><img style="border: 1px solid #ffc68f;" src="https://www.gravatar.com/avatar/'.$hash.'.png?d=retro&amp;s=60" /></span>
			'.($is_lead?'<span class="playerbutton" onclick="addPlayer(this, 3)">Ajouter</span>':'').'
			<span class="playertype">'.$player_type[3].'</span>
			</span><br />';
		}

		for ($iplayer=0;$iplayer<1;$iplayer++)
		{
			if (isset($data[$current_player]))
			{
				if($data[$current_player]["GU_TYPE"] == 4)
				{
					display_card($data[$current_player], $player_type[4], $game_data, $get_game, $games_profile[$get_game]['rank'], $is_lead);

					$current_player++;

					continue;
				}
			}
			
			$hash = md5("coach".$iplayer.$randhash);
			echo '<span class="playercard">
			<span style="float:left"><img style="border: 1px solid #ffc68f;" src="https://www.gravatar.com/avatar/'.$hash.'.png?d=retro&amp;s=60" /></span>
			'.($is_lead?'<span class="playerbutton" onclick="addPlayer(this, 4)">Ajouter</span>':'').'
			<span class="playertype">'.$player_type[4].'</span>
			</span><br />';
		}

		echo '</div>';

	// TODO : confirm delete participation
	}
	else
	{
		if ($game_data["G_NUM_PLAYERS"] > 1)
		{
			echo '<p><table class="line_table"><tr><td><hr class="line" /></td><td>Rejoindre une équipe</td><td><hr class="line" /></td></tr></table></p>';
			echo '<br /><p style="text-align: center;" class="smallquote">Si vous souhaitez <span style="font-weight:bold;">rejoindre une équipe</span>,<br /><u>votre chef d\'équipe doit d\'abord vous inviter</u>.</p><br />';

			/*

			$temp = $database->req('SELECT sgl_users.login, sgl_users.id FROM sgl_teams, sgl_users WHERE sgl_teams.user="'.$_SESSION["sgl_id"].'" AND sgl_teams.lead = sgl_users.id AND game="'.$games[$i].'" AND sgl_teams.register = 0');

			while($data = $temp->fetch())
			{
				//echo '<p style="text-align: center;"><a href="index.php?page=games'.$url_game.'&amp;game='.$games[$i].'&accept='.$data["id"].'" class="button">Accepter l\'invitation de '.htmlspecialchars($data["login"]).'</a></p><br />';
			}
			*/
		}

		echo '<p><table class="line_table"><tr><td><hr class="line" /></td><td>Inscription</td><td><hr class="line" /></td></tr></table></p>';

		if ($game_data["G_NUM_PLAYERS"] > 1)
		{
			echo '<br /><p style="text-align: center;"><a href="index.php?page=games'.$url_game.'&amp;action=create" class="button">Créer son équipe</a></p><br />';
		}
		else
		{
			echo '<br /><p style="text-align: center;"><a href="index.php?page=games'.$url_game.'&amp;action=create" class="button">S\'inscrire à la compétition</a></p>';
		}

		
	}
	if ($game_data["G_NUM_PLAYERS"] > 1)
	{
		echo '<p><table class="line_table"><tr><td><hr class="line" /></td><td>Chercher des joueurs</td><td><hr class="line" /></td></tr></table></p>';

		echo '<p style="text-align: center;" class="smallquote">Seuls les joueurs sans équipe ayant rempli leur rang <a href="index.php?page=account">dans leur profil</a> sont affichés.<br />Utilisez <span style="font-weight:bold;">Discord</span> pour les contacter.</p>';

		$temp = $database->req_post('SELECT SU_LOGIN, PU_PSEUDO, GU_RANK, S_NAME FROM T_SGL_USER
			JOIN T_GAME_USER ON SU_UID=GU_ID_SU AND GU_ID_G=:game AND GU_ID_ST IS NULL AND GU_RANK>0 JOIN T_PLATFORM_USER ON PU_ID_P=3 AND PU_ID_SU=SU_UID JOIN T_SCHOOL ON SU_ID_S=S_UID
			WHERE SU_LOGIN IS NOT NULL ORDER BY GU_RANK, SU_UID',
			array(
				"game" => $get_game
			));

		echo '<table class="searchtable"><tr><th>Pseudo</th><th>Ecole</th><th>Discord</th><th>Rang</th></table><div class="playersearch"><table class="searchtable">';



		$count = 0;

		while($data = $temp->fetch())
		{
			echo "<tr><td>".$data["SU_LOGIN"]."</td><td>".$data["S_NAME"]."</td><td>".$data["PU_PSEUDO"]."</td><td>".$games_profile[$get_game]['rank'][$data["GU_RANK"]]."</td></tr>";
			$count++;
		}

		if($count == 0)
		{
			echo "<tr><td colspan=\"4\" style=\"text-align:center;\">Aucun joueur trouvé :(... Revenez un peu plus tard !</td></tr>";
		}

		echo '</table></div>';
	}
}
else
{
	echo '<p style="text-align: center;">Vous devez d\'abord vous inscrire sur le site pour pouvoir participer...<br /><br /><a href="index.php?page=register" class="button">S\'inscrire à la SGL</a></p>';
}

echo '<br /><br />';

echo '<p style="text-align:center;"><b><i class="fa fa-question-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> Des questions ? Besoin d\'aide ? A la recherche de joueurs ?<br />Venez nous rejoindre sur <a target="_blank" href="https://discord.gg/sgnw">Discord</a> :D !</p>';

	}
}
?>
		<br />
	</div>
</div>
