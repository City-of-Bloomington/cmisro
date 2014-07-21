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

$fullpath = '';
foreach (explode('/', $variables['object']['path']) as $i=>$dir) {
    # Skip the root dir
    if ($i > 0) {
        $fullpath.= "/$dir";
        echo "<a href=\"$url?ref=$fullpath\">/$dir</a>";
    }
}
echo "</h2>";
