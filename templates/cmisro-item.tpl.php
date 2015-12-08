<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param mixed $variables['object']
 */
global $base_url;
$download = "$base_url/cmisro/download";

$item = &$variables['object'];
$class = _cmisro_class_for_type($item['type']);
$title = check_plain($item['title']);
echo "<a href=\"$download/$item[id]\" class=\"cmis_object $class\">$item[title]</a>";
