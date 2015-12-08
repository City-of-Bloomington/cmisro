<!DOCTYPE html>
<html>
<head>
<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param mixed $variables['listing']
 * @param array $variables['current_directory']
 */
$p = $_SERVER['SERVER_PORT']==443 ? 'https' : 'http';
drupal_add_js(drupal_get_path('module', 'cmisro').'/js/cmisro_browser.js');

echo drupal_get_css();
echo drupal_get_js();
?>
</head>
<body>
<?php
    echo theme('cmisro_browser', $variables);
?>
</body>
</html>
