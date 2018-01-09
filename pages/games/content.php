<?php

global $csrf_check;

include_once("./class/Database.class.php");

$database = new Database();

// 1 - Overwatch
// 2 - League of Legends
// 3 - Counter Strike
// 4 - Hearthstone

// TODO array of objects OR read from database

$games = array(1, 2, 3, 4);

$games_team = array(6, 5, 5, 1);
$games_reps = array(2, 2, 2, 0);

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
	"Ryu ga waga teki wo kurau ! Nooooo...",
	"Captain Teemo on duty !",
	"Rush B my friend ! Don't stop, don't stop...",
	"You face Jaraxxus,<br />eredar lord of the burning legion !");

$games_content = array(
	'<p style="text-align:center;">
		<b><i class="fa fa-user-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> 6 joueurs par équipe
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-calendar" aria-hidden="true" style="padding-right: 5px;"></i></b> Les Jeudis (début le 9 Mars)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-trophy" aria-hidden="true" style="padding-right: 5px;"></i></b> 1200€ de cashprize
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-line-chart" aria-hidden="true" style="padding-right: 5px;"></i></b> Nouveau tournoi<br /><br />
		<span style="padding: 10px;">Finales le <b style="font-weight:bold;">27 et 28 Mai</b> à l\'<b style="font-weight:bold;">école 42</b></span><br /><br />
		<a href="./files/rules_sgl2017.pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Télécharger le règlement</a><br /><br />
		Phases finales diffusées sur<br /><a href="http://overwatch.gamersorigin.com/"><img src="./style/img/partners/sml_go.png" alt="Gamers Origin" /></a></p><br />',

	'<p style="text-align:center;">
		<b><i class="fa fa-user-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> 5 joueurs par équipe
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-calendar" aria-hidden="true" style="padding-right: 5px;"></i></b> Les Lundis (début le 6 Mars)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-trophy" aria-hidden="true" style="padding-right: 5px;"></i></b> Voyage à Berlin aux LCS (en attente de validation par Riot)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-line-chart" aria-hidden="true" style="padding-right: 5px;"></i></b> 200 joueurs l\'année dernière<br /><br />
		<span style="padding: 10px;">Finales le <b style="font-weight:bold;">27 et 28 Mai</b> à l\'<b style="font-weight:bold;">école 42</b></span><br /><br />
		<a href="./files/rules_sgl2017.pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Télécharger le règlement</a><br /><br />
		Equipe gagnante qualifiée aux University eSports Masters<br /><a href="http://www.uemasters.com/"><img src="./style/img/partners/uem.png" alt="Gamers Origin" /></a></p><br />',

	'<p style="text-align:center;">
		<b><i class="fa fa-user-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> 5 joueurs par équipe
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-calendar" aria-hidden="true" style="padding-right: 5px;"></i></b> Les Mercredis (début le 8 Mars)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-trophy" aria-hidden="true" style="padding-right: 5px;"></i></b> 1000€ de cashprize
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-line-chart" aria-hidden="true" style="padding-right: 5px;"></i></b> 120 joueurs l\'année dernière<br /><br />
		<span style="padding: 10px;">Finales le <b style="font-weight:bold;">27 et 28 Mai</b> à l\'<b style="font-weight:bold;">école 42</b></span><br /><br />
		<a href="./files/rules_sgl2017.pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Télécharger le règlement</a></p><br />',

	'<p style="text-align:center;">
		<b><i class="fa fa-user-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> Tournoi solo
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-calendar" aria-hidden="true" style="padding-right: 5px;"></i></b> Les Mardis (début le 7 Mars)
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-trophy" aria-hidden="true" style="padding-right: 5px;"></i></b> 500€ de cashprize
		<span style="padding: 0px 10px;">|</span>
		<b><i class="fa fa-line-chart" aria-hidden="true" style="padding-right: 5px;"></i></b> 114 joueurs l\'année dernière<br /><br />
		<span style="padding: 10px;">Finales le <b style="font-weight:bold;">27 et 28 Mai</b> à l\'<b style="font-weight:bold;">école 42</b></span><br /><br />
		<a href="./files/rules_sgl2017.pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Télécharger le règlement</a><br /><br />
		Phases finales diffusées sur<br /><a href="http://hearthstone.gamersorigin.com/"><img src="./style/img/partners/sml_go.png" alt="Gamers Origin" /></a></p><br />');

$check_teamtag = 0;
$error_teamtag = "";

if (isset($_GET["game"]) AND $csrf_check AND isset($_SESSION["sgl_id"]))
{
	$form_game = intval($_GET["game"]);
	if (in_array($form_game, $games))
	{
		if (isset($_GET["del"]))
		{
			$database->req('DELETE FROM sgl_teams WHERE user="'.$_SESSION["sgl_id"].'" AND game="'.$form_game.'"');
		}
		else if(isset($_GET["accept"]))
		{
			$database->req('UPDATE sgl_teams SET register = "'.time().'" WHERE user = "'.$_SESSION["sgl_id"].'" AND game = "'.$form_game.'" AND lead = "'.intval($_GET["accept"]).'"');
			$database->req('DELETE FROM sgl_teams WHERE user = "'.$_SESSION["sgl_id"].'" AND game="'.$form_game.'" AND register = "0"');
		}
		else if(isset($_GET["remove"]))
		{
			$database->req('DELETE FROM sgl_teams WHERE user = "'.intval($_GET["remove"]).'" AND game="'.$form_game.'" AND lead = "'.$_SESSION["sgl_id"].'"');
		}
		else
		{
			$temp = $database->req('SELECT COUNT(*) as exist FROM sgl_teams WHERE user="'.$_SESSION["sgl_id"].'" AND game="'.$form_game.'"');
			$data = $temp->fetch();

			if ($data["exist"] == 0)
			{
				$database->req('INSERT INTO sgl_teams (user, game, lead, type, register) VALUES("'.$_SESSION["sgl_id"].'", "'.$form_game.'", "'.$_SESSION["sgl_id"].'", 1, '.time().')');
			}
		}

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
			}

			$database->req('UPDATE sgl_teams SET tag="'.addslashes($form_teamtag).'", name="'.addslashes($form_teamname).'"
				WHERE user="'.$_SESSION["sgl_id"].'" AND lead="'.$_SESSION["sgl_id"].'" AND game="'.$form_game.'"');


			// TODO : check if teamtag alreay exist

			if ($check_teamtag < 0)
			{
				$error_teamtag = "<div class=\"error\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>".$error_teamtag."</div>";
			}
		}
	}
}

// TODO automated generation
$games_in = array (1 => false, 2 => false, 3 => false, 4 => false);

if (isset($_SESSION["sgl_id"]))
{
	$temp = $database->req('SELECT game FROM sgl_teams WHERE user="'.$_SESSION["sgl_id"].'" AND register > 0');

	while($data = $temp->fetch())
	{
		$games_in[$data["game"]] = true;
	}
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
		$single = true;

		echo '<div class="top_ban" style="background-image: url(\'./style/img/ban/top_'.$games_short[$get_game-1].'.png\');"></div>';
	}
}

?>
	<div class="container">
		<h1><i class="fa fa-angle-right" aria-hidden="true"></i> Tournoi <?=$single?$games_name[$get_game-1]:''?></h1>
		<div class="quote">
			<span class="qcontent">
				<i>&ldquo;</i>
<?php
if ($single)
{
	echo $games_quote[$get_game-1];
}
else
{
	echo 'Keep calm and blame it on the lag';
}
?>
				<i>&rdquo;</i>
			</span>
			<span class="qauthor">
				- Un joueur de la SGL 2016
			</span>
		</div>
		<br />
<?php


switch($get_game)
{
	case 1:
		echo '<iframe style="width:100%; height: 500px" src="https://widget.toornament.com/tournaments/58f4eb2c150ba0a5318b4572/stages/1?_locale=fr_FR&theme=discipline" frameborder="0" allowfullscreen></iframe><br /><br />';
		break;
	case 2:
		echo '<iframe style="width:100%; height: 500px" src="https://widget.toornament.com/tournaments/5883b197140ba012738b4576/stages/2?_locale=fr_FR&theme=discipline" frameborder="0" allowfullscreen></iframe><br /><br />';
		break;
	case 3:
		echo '<iframe style="width:100%; height: 500px" src="https://widget.toornament.com/tournaments/58ed0a0c140ba04e228b45bb/stages/1?_locale=fr_FR&theme=discipline" frameborder="0" allowfullscreen></iframe><br /><br />';
		break;
	case 4:
		echo '<iframe style="width:100%; height: 500px" src="https://widget.toornament.com/tournaments/58ed064b140ba04e228b457c/stages/1?_locale=fr_FR&theme=discipline" frameborder="0" allowfullscreen></iframe><br /><br />';
		break;
}

if ($_SESSION["sgl_id"])
{

	if ($get_game <= 4)
	{

	
		$file = "save_".$games_short[$get_game-1].".txt";

		if (filemtime ($file) < time() - 60*10)
		{
			switch($get_game)
			{
				case 1:
					$content = file_get_contents("https://api.toornament.com/v1/tournaments/588a2de4150ba0022c8b45e2/matches?api_key=cfmrLZK1wmlZf5MM0rP3Wydiu6vchycYkZT44riGkPk");
					break;
				case 2:
					$content = file_get_contents("https://api.toornament.com/v1/tournaments/5883b197140ba012738b4576/matches?api_key=cfmrLZK1wmlZf5MM0rP3Wydiu6vchycYkZT44riGkPk");
					break;
				case 3:
					$content = file_get_contents("https://api.toornament.com/v1/tournaments/588a2614140ba0e9058b45b6/matches?api_key=cfmrLZK1wmlZf5MM0rP3Wydiu6vchycYkZT44riGkPk");
					break;
				case 4:
					$content = file_get_contents("https://api.toornament.com/v1/tournaments/5889ec83140ba05c4c8b468d/matches?api_key=cfmrLZK1wmlZf5MM0rP3Wydiu6vchycYkZT44riGkPk");
					break;
			}
			file_put_contents($file, $content);
		}

		$json_raw = file_get_contents($file);
		$matches = json_decode($json_raw, true);

		if ($get_game == 4)
		{
			$temp = $database->req('SELECT battletag FROM sgl_users WHERE id="'.$_SESSION["sgl_id"].'"');
			$data = $temp->fetch();

			echo "<div style=\"text-align:center\">";

			for ($i=count($matches)-1; $i>=0; $i--)
			{
				if ($matches[$i]["status"] == "completed")
				{
					$op1_name = $matches[$i]["opponents"][0]["participant"]["name"];
					$op2_name = $matches[$i]["opponents"][1]["participant"]["name"];

					$op1_score = intval($matches[$i]["opponents"][0]["score"]);
					$op2_score = intval($matches[$i]["opponents"][1]["score"]);

					if (($op1_score == 0) AND ($op2_score == 0))
					{
						if ($matches[$i]["opponents"][0]["result"] == 1)
						{
							$op1_score = "V";
							$op2_score = "D";

							$win = 1;
						}
						else
						{
							$op1_score = "D";
							$op2_score = "V";

							$win = 2;
						}
					}

					if (($op1_name == $data["battletag"]) || ($op2_name == $data["battletag"]))
					{

						if ($win == 1)
						{
							echo "<div class=\"gen_block\"><span class=\"gen_team\">".$op1_name."</span> <span class=\"gen_score\"><strong><b>".$op1_score."</b> / ".$op2_score."</strong></span> <span class=\"gen_team\">".$op2_name."</span></div> <a href=\"generation/gen_".$games_short[$get_game-1].".php?match=".$matches[$i]["id"]."\" class=\"gen_share\"><i class=\"fa fa-share-alt\" aria-hidden=\"true\"></i></a><br /><br />";
						}
						else
						{
							echo "<div class=\"gen_block\"><span class=\"gen_team\">".$op1_name."</span> <span class=\"gen_score\"><strong>".$op1_score." / <b>".$op2_score."</b></strong></span> <span class=\"gen_team\">".$op2_name."</span></div> <a href=\"generation/gen_".$games_short[$get_game-1].".php?match=".$matches[$i]["id"]."\" class=\"gen_share\"><i class=\"fa fa-share-alt\" aria-hidden=\"true\"></i></a><br /><br />";
						}

					}
				}
			}

			echo "</div>";
		}
		else
		{

			$temp = $database->req('SELECT sgl_teams.tag
				FROM sgl_users, sgl_teams LEFT JOIN sgl_teams AS my_team ON sgl_teams.lead = my_team.lead AND sgl_teams.game = my_team.game
				WHERE my_team.user="'.$_SESSION["sgl_id"].'" AND my_team.game="'.$get_game.'" AND sgl_teams.user = sgl_users.id AND sgl_teams.type = 1');

			$data = $temp->fetch();

			echo "<div style=\"text-align:center\">";

			for ($i=count($matches)-1; $i>=0; $i--)
			{
				if ($matches[$i]["status"] == "completed")
				{
					//echo $matches[$i]["id"]."<br />";

					$op1_exp = explode(" ", $matches[$i]["opponents"][0]["participant"]["name"]);
					$op1_tag = $op1_exp[1];
					$op1_name = substr($matches[$i]["opponents"][0]["participant"]["name"], strlen($op1_tag)+5);

					$op2_exp = explode(" ", $matches[$i]["opponents"][1]["participant"]["name"]);
					$op2_tag = $op2_exp[1];
					$op2_name = substr($matches[$i]["opponents"][1]["participant"]["name"], strlen($op2_tag)+5);

					$op1_score = intval($matches[$i]["opponents"][0]["score"]);
					$op2_score = intval($matches[$i]["opponents"][1]["score"]);

					$win = ($op1_score>$op2_score)?1:2;

					if (($op1_tag == $data["tag"]) || ($op2_tag == $data["tag"]))
					{
						if (($op1_score == 0) AND ($op2_score == 0))
						{
							if ($matches[$i]["opponents"][0]["result"] == 1)
							{
								$op1_score = "V";
								$op2_score = "D";

								$win = 1;
							}
							else
							{
								$op1_score = "D";
								$op2_score = "V";

								$win = 2;
							}
						}

						if ($win == 1)
						{
							echo "<div class=\"gen_block\"><span class=\"gen_team\">[".$op1_tag."] ".$op1_name."</span> <span class=\"gen_score\"><strong><b>".$op1_score."</b> / ".$op2_score."</strong></span> <span class=\"gen_team\">[".$op2_tag."] ".$op2_name."</span></div> <a href=\"generation/gen_".$games_short[$get_game-1].".php?match=".$matches[$i]["id"]."\" class=\"gen_share\"><i class=\"fa fa-share-alt\" aria-hidden=\"true\"></i></a><br /><br />";
						}
						else
						{
							echo "<div class=\"gen_block\"><span class=\"gen_team\">[".$op1_tag."] ".$op1_name."</span> <span class=\"gen_score\"><strong>".$op1_score." / <b>".$op2_score."</b></strong></span> <span class=\"gen_team\">[".$op2_tag."] ".$op2_name."</span></div> <a href=\"generation/gen_".$games_short[$get_game-1].".php?match=".$matches[$i]["id"]."\" class=\"gen_share\"><i class=\"fa fa-share-alt\" aria-hidden=\"true\"></i></a><br /><br />";
						}
					}
				}
			}

			echo "</div>";
		}
	}

}


$break_flag = false;
$url_game = "";

for ($i=0; $i<count($games); $i++)
{

	if ($single)
	{
		$i = $get_game-1;
		$break_flag = true;
		$url_game = "&amp;gpage=".$get_game;
	}

	echo '<p id="'.$games_short[$i].'"><table class="line_table"><tr><td><hr class="line" /></td><td><img src="./style/img/games/'.$games_short[$i].'.png" alt="'.$games_name[$i].'" /></td><td><hr class="line" /></td></tr></table></p>';

	echo $games_content[$i];

	if ($games_in[$games[$i]])
	{
		echo '<p style="text-align:center; font-weight: bold">Yay ! <b>Vous êtes inscrit</b> à ce tournoi ! Un premier pas vers la victoire...</p>';
		//echo '<p style="text-align: center;" class="smallquote">Plus qu\'à hard train jusqu\'à début Mars. [ <a href="index.php?page=games'.$url_game.'&amp;game='.$games[$i].'&amp;del=1">Se désinscrire du tournoi</a> ]</p><br />';
		echo '<p style="text-align: center;" class="smallquote">Le tournoi a commencé... Bon courage !</p><br />';

		$gameidType = "battletag";
		if ($get_game == 2)
		{
			$gameidType = "summoner";
		}
		else if ($get_game == 3)
		{
			$gameidType = "steamid";
		}

		$temp = $database->req('SELECT sgl_users.login, sgl_users.mail, sgl_users.'.$gameidType.', sgl_teams.type, sgl_teams.register, sgl_teams.user, sgl_teams.name, sgl_teams.tag
			FROM sgl_users, sgl_teams LEFT JOIN sgl_teams AS my_team ON sgl_teams.lead = my_team.lead AND sgl_teams.game = my_team.game
			WHERE my_team.user="'.$_SESSION["sgl_id"].'" AND my_team.game="'.$games[$i].'" AND sgl_teams.user = sgl_users.id ORDER BY type ASC');

		$type = array("Aucun", "Capitaine", "Joueur", "Remplaçant");

		$nplayer = 0;
		$nreps = 0;

		echo '<div style="text-align: center">';

		$lasttype = 1;
		$lead = false;

		while($data = $temp->fetch())
		{
			if ($data["type"] == 1)
			{
				if ($_SESSION["sgl_id"] == $data["user"])
				{
					$lead = true;

					if ($games_team[$i] > 1)
					{
						echo '<div class="form"><form action="index.php?page=games'.$url_game.'&amp;game='.$games[$i].'" method="post">
						<table class="form_table">
							<tr><td><h3>Nom d\'équipe :</h3></td><td><input disabled="disabled" value="'.htmlspecialchars($data["name"]).'" name="teamname" type="text"><br />
							<div class="smallquote">Le nom de votre équipe, genre "Télécom Bretagne Gaming"</div></td></tr>
							<tr><td><h3>TAG d\'équipe :</h3></td><td><input disabled="disabled" value="'.htmlspecialchars($data["tag"]).'" name="teamtag" type="text"><br />
							'.$error_teamtag.'
							<div class="smallquote">Votre tag en 3 ou 4 caractères, genre "TBG" ou "TBG2" (que des lettres et des chiffres par contre !)</div></td></tr>
						</table><br /><br />
						<input type="hidden" name="sent" value="sent">
						'./*<button type="submit" value="Submit">Mettre à jour</button>*/'
						</form></div><br /><br /><br />';

						echo '<p id="'.$games_short[$i].'"><table class="line_table"><tr><td><hr class="line" /></td><td>Votre équipe</td><td><hr class="line" /></td></tr></table></p><br />';
					}
				}
				else
				{
					echo "<h1>".htmlspecialchars($data["name"])." [".htmlspecialchars($data["tag"])."]</h1><br />";
				}
			}

			if ($data["type"] != $lasttype)
			{
				if ($lasttype == 2)
				{
					for ($j=0; $j<($games_team[$i]-$nplayer); $j++)
					{
						if ($lead)
						{
							echo '<span class="buttoncard" onclick="morphIntoTextField(this, '.$games[$i].')()" data-type="2" data-game="'.$games[$i].'">Ajouter un joueur</span><br />';
						}
						else
						{
							echo '<span class="playercard"></span><br />';
						}
					}
					echo "<br /><br/>";
				}
			}

			if ($data["type"] < 3)
			{
				$nplayer++;
			}
			else if ($data["type"] == 3)
			{
				$nreps++;
			}

			if ($lead && ($_SESSION["sgl_id"] != $data["user"]))
			{
				$dlstr = '';
				//$dlstr = '<span class="cardoption"><a href="index.php?page=games'.$url_game.'&amp;game='.$games[$i].'&remove='.$data["user"].'"><i class="fa fa-times" aria-hidden="true"></i></a></span>';
			}
			else
			{
				$dlstr = '';
			}

			if ($data["register"] == 0)
			{
				echo '<span class="playercard" style="opacity:0.5;"><span class="playername">'.htmlspecialchars(($data["login"] == "")?$data["mail"]:$data["login"]).' <span class="mintext">(Invitation envoyée)</span></span><span class="playertype">('.$type[$data["type"]].')</span>'.$dlstr.'</span><br />';
			}
			else
			{
				if($data[$gameidType] != "")
				{
					$gameTag = ' <span class="mintext">('.$data[$gameidType].')</span>';
				}
				else
				{
					$gameTag = ' <span class="mintext" style="color:#ff0000;text-decoration:underline">(Gametag manquant)</span>';
				}

				echo '<span class="playercard"><span class="playername">'.htmlspecialchars($data["login"]).$gameTag.'</span><span class="playertype">('.$type[$data["type"]].')</span>'.$dlstr.'</span><br />';
			}

			$lasttype = $data["type"];
		}

		if ($lasttype <= 2)
		{
			for ($j=0; $j<($games_team[$i]-$nplayer); $j++)
			{
				if ($lead)
				{
					//echo '<span class="buttoncard" onclick="morphIntoTextField(this, '.$games[$i].')()" data-type="2" data-game="'.$games[$i].'">Ajouter un joueur</span><br />';
					echo '<span class="playercard"></span><br />';
				}
				else
				{
					echo '<span class="playercard"></span><br />';
				}
			}
			echo "<br /><br />";
			$lasttype = 2;
		}

		for ($j=0; $j<($games_reps[$i]-$nreps); $j++)
		{
			if ($lead)
			{
				//echo '<span class="buttoncard" onclick="morphIntoTextField(this, '.$games[$i].')()" data-type="3" data-game="'.$games[$i].'">Ajouter un remplaçant</span><br />';
				echo '<span class="playercard"></span><br />';
			}
			else
			{
				echo '<span class="playercard"></span><br />';
			}
		}

		echo '</div>';

// TODO : confirm delete participation
	}
	else
	{
		if (isset($_SESSION["sgl_id"]))
		{
			if ($games_team[$i] > 1)
			{
				//echo '<p style="text-align: center;" class="smallquote">Si vous souhaitez <span style="font-weight:bold;">rejoindre une équipe</span>,<br /><u>votre chef d\'équipe doit d\'abord vous inviter</u>.</p><br /<br />';

				$temp = $database->req('SELECT sgl_users.login, sgl_users.id FROM sgl_teams, sgl_users WHERE sgl_teams.user="'.$_SESSION["sgl_id"].'" AND sgl_teams.lead = sgl_users.id AND game="'.$games[$i].'" AND sgl_teams.register = 0');

				while($data = $temp->fetch())
				{
					//echo '<p style="text-align: center;"><a href="index.php?page=games'.$url_game.'&amp;game='.$games[$i].'&accept='.$data["id"].'" class="button">Accepter l\'invitation de '.htmlspecialchars($data["login"]).'</a></p><br />';
				}
			}
			if ($games[$i] == 4)
			{
				//echo '<p style="text-align: center;"><a href="index.php?page=games'.$url_game.'&amp;game='.$games[$i].'" class="button">S\'inscrire à la compétition</a></p>';
			}
			else
			{
				//echo '<p style="text-align: center;"><a href="index.php?page=games'.$url_game.'&amp;game='.$games[$i].'" class="button">Inscrire son équipe</a></p>';
			}
		}
		else
		{
			/*echo '<p style="text-align: center;">Vous devez d\'abord vous inscrire sur le site pour pouvoir participer...<br /><br />
			<a href="index.php?page=register" class="button">S\'inscrire à la SGL</a></p>';*/
		}

		echo '<p style="text-align: center;"><a href="#" class="button">Le tournoi a déjà commencé...</a></p>';
	}
	
	echo '<br /><br />';

	echo '<p style="text-align:center;"><b><i class="fa fa-question-circle" aria-hidden="true" style="padding-right: 5px;"></i></b> Des questions ? Besoin d\'aide ? A le recherche de joueurs ?<br />Venez nous rejoindre sur <a target="_blank" href="https://discord.gg/SGL17">Discord</a> :D !</p>';

	if ($break_flag)
	{
		break;
	}
}
?>
		<br />
	</div>
</div>
