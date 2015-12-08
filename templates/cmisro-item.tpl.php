<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param mixed $variables['object']
 */
$item  = &$variables['object'];
$class = _cmisro_class_for_type($item['type']);
$title = check_plain($item['title']);
$url   = _cmisro_document_url($item['id']);
echo "<a href=\"$url\" class=\"cmis_object $class\">$item[title]</a>";
