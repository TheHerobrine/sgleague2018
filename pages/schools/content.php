<div id="content">
	<div class="container">
		<h1><i class="fa fa-angle-right" aria-hidden="true"></i> Statistiques écoles</h1>
<?php

	include_once("./class/Database.class.php");
	$database = new Database();

	$temp = $database->req('SELECT COUNT(school) AS number, school FROM sgl_users GROUP BY school ORDER BY number DESC, school ASC');

	while ($data = $temp->fetch())
	{
		if ($data["number"] > 0)
		{
			echo $data["number"].';'.htmlspecialchars($data["school"]).'<br />';
		}
	}

?>

	</div>
</div>