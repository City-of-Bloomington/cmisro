<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param mixed $variables['listing']
 * @param array $variables['current_directory']
 */
?>
<div class="cmisro">
	<?php
        echo theme('cmisro_breadcrumbs', ['object'=>$variables['current_directory']]);
	?>
	<table>
	<?php
		global $base_url;
		$url = "$base_url/cmisro/browser";

        $ignore = ['.DS_Store'];

        if (isset   ($variables['listing'])) {
            foreach ($variables['listing'] as $o) {

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
				$params = '';
				$button = '';
				if (!empty($_GET['popup']) && !empty($_GET['id'])) {
					$params = '&amp;popup=1&amp;id='.$_GET['id'];
					$button = "
					<button type=\"button\" onclick=\"CMISRO_BROWSER.handleSelection('$_GET[id]', '$o[id]');\">
						Choose
					</button>
					";
				}

                if ($o['type'] == 'cmis:folder') {
					$title = "<a href=\"$url?ref=$o[id]$params\">$title</a>";
                }

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
        if (!empty($variables['listing']->hasMoreItems)) {
            pager_default_initialize($variables['listing']->numItems, _cmisro_service()->maxItems);
            echo theme('pager');
        }
    ?>
</div>
