<div id="content">
	<div class="container">

<?php

if (DEBUG)
{
	include_once("./class/Database.class.php");

	$database = new Database();

	$temp = $database->req_get('SELECT SU_MAIL FROM T_SGL_USER ORDER BY SU_UID LIMIT 0, 100');

	while($data = $temp->fetch())
	{
		$hash = md5(strtolower(trim($data["SU_MAIL"])));
		echo '<a href="https://www.gravatar.com/'.$hash.'"><img src="https://www.gravatar.com/avatar/'.$hash.'.png?d=retro&amp;s=20" /></a>';
	}
}

?>

	</div>
</div>