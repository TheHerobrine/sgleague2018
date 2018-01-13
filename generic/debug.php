<?php

function debug($title, $content)
{
	if (DEBUG)
	{
		if (is_array($content))
		{
			echo '<div class="debug"><b>'.$title.'</b><br /><pre>';
			print_r($content);
			echo '</pre></div>';
		}
		elseif (is_string($content))
		{
			echo '<div class="debug"><b>'.$title.'</b><br />'.$content.'</div>';
		}
		else
		{
			echo '<div class="debug"><b>'.$title.'</b><br /><pre>';
			var_dump($content);
			echo '</pre></div>';
		}
	}
}