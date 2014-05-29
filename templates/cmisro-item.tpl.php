<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param mixed $variables['object']
 */
?>
<div class="cmisro">
	<h2>CMISRO Item</h2>
	<?php
		global $base_url;
		$download = "$base_url/cmisro/download";
		
		$item = &$variables['object'];
		$class = _cmisro_class_for_type($item['type']);
		$title = check_plain($item['title']);
		echo "<a href=\"$download/$item[id]\"><i class=\"$class\"></i>$item[title]</a>";
	?>
</div>
