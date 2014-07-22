<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param mixed $variables['listing']
 * @param array $variables['current_directory']
 */
?>
<div class="cmisro">
	<h2>CMISRO Browser</h2>
	<?php
        echo theme('cmisro_breadcrumbs', ['object'=>$variables['current_directory']]);
	?>
	<table>
	<?php
		global $base_url;
		$url = "$base_url/cmisro/browser";

        $ignore = ['.DS_Store'];

        if (isset   ($variables['listing']->objects)) {
            foreach ($variables['listing']->objects as $item) {
                $o = _cmisro_object($item->object);

                if (in_array($o['title'], $ignore)) { continue; }

                $class = _cmisro_class_for_type($o['type']);
                $title = check_plain($o['title']);
                echo "
                <tr><td><a href=\"$url?ref=$o[id]\"><i class=\"$class\"></i>$title</a></td>
                    <td></td>
                </tr>
                ";
            }
        }
	?>
	</table>
    <?php
        if ($variables['listing']->hasMoreItems) {
            pager_default_initialize($variables['listing']->numItems, _cmisro_service()->maxItems);
            echo theme('pager');
        }
    ?>
</div>
