<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $variables['object']
 */
echo "<h2>";

global $base_url;
$url = "$base_url/cmisro/browser";
$params = (!empty($_GET['popup']) && !empty($_GET['id']))
	? '&amp;popup=1&amp;id='.$_GET['id']
	: '';

$fullpath = '';
$path  = explode('/', $variables['object']['path']);
$count = count($path);
foreach ($path as $i=>$dir) {
    # Skip the root dir
    if ($i > 0) {
        $fullpath.= "/$dir";
        echo "<a href=\"$url?ref=$fullpath$params\">/$dir</a>";
    }
    if ($i == ($count - 2)) {
		$back = $fullpath ? $fullpath : '/';
    }
}
echo "</h2>";

if ($variables['object']['path'] != '/') {
	echo "
	<div class=\"cmis_object folder\">
		<a href=\"$url?ref=$back$params\">Parent Folder</a>
	</div>
	";
}
