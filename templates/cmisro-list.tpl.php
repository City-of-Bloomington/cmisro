<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param mixed $variables['folder']
 */
?>
<div class="cmisro">
	<h2>CMISRO List</h2>
	<ul>
	<?php
		global $base_url;
		$download = "$base_url/cmisro/download";

		foreach ($variables['folder'] as $i=>$item) {
			$class = _cmisro_class_for_type($item['type']);
			$title = check_plain($item['title']);
			echo "<li><a href=\"$download/$item[id]\"><i class=\"$class\"></i>$title</a></li>";
		}
	?>
	</ul>
</div>
