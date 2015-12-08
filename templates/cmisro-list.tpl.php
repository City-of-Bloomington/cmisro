<?php
/**
 * Lists the documents returned from a CMIS query
 *
 * This will only list the document objects.  Folders will be ignored.
 *
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param mixed $variables['list']
 * @param string $variables['title']
 */
global $base_url;
$download = "$base_url/cmisro/download";

$ignore = ['.DS_Store'];

$title = isset($variables['title']) ? check_plain($variables['title']) : 'Attachments';
?>
<div class="cmisro">
	<h2><?= $title ?></h2>
	<ul>
	<?php
        if (isset   ($variables['list']->objects)) {
            foreach ($variables['list']->objects as $item) {
                $o = _cmisro_object($item->object);

                if ($o['type'] == 'cmis:folder')    { continue; }
                if (in_array($o['title'], $ignore)) { continue; }

                echo '<li>'.theme('cmisro_item', ['object'=>$o]).'</li>';
            }
        }
	?>
	</ul>
</div>
