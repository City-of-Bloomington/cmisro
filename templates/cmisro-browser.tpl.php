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

                $title = check_plain($o['title']);
				$class = _cmisro_class_for_type($o['type']);
                // Only render links on folders
                //
                // This browser is written to navigate the directory structure.
                // It makes no sense to render a link on a document.
                //
                // However, if this browser is being used as a popup, the user
                // should be able to choose either a folder or a document.
                // We should render buttons on every item.
                if ($o['type'] == 'cmis:folder') {

					$params = '';
					if (!empty($_GET['popup']) && !empty($_GET['id'])) {
						$params = '&amp;popup=1&amp;id='.$_GET['id'];
					}

					$title = "<a href=\"$url?ref=$o[id]$params\">$title</a>";
                }
                
				$button = "
				<button type=\"button\" onclick=\"CMISRO_BROWSER.handleSelection('$_GET[id]', '$o[id]');\">
					Choose
				</button>";

                echo "
                <tr><td class=\"cmis_object $class\">$title</td>
                    <td>$button</td>
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
