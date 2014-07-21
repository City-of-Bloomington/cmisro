<?php
/**
 * Lists the documents returned from a CMIS query
 *
 * This will only list the document objects.  Folders will be ignored.
 *
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

        $ignore = ['.DS_Store'];

        if (isset   ($variables['folder']->objects)) {
            echo "Found ".count($variables['folder']->objects)." objects\n";
            foreach ($variables['folder']->objects as $item) {
                $o = _cmisro_object($item->object);

                if ($o['type'] == 'cmis:folder')    { continue; }
                if (in_array($o['title'], $ignore)) { continue; }

                $class = _cmisro_class_for_type($o['type']);
                $title = check_plain($o['title']);
                echo "<li><a href=\"$download/$o[id]\"><i class=\"$class\"></i>$title</a></li>";
            }
        }
	?>
	</ul>
</div>
