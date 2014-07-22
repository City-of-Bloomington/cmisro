<!DOCTYPE html>
<html>
<head>
<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param mixed $variables['listing']
 * @param array $variables['current_directory']
 */
$p = $_SERVER['SERVER_PORT']==443 ? 'https' : 'http';
drupal_add_css("$p://$_SERVER[SERVER_NAME]/Font-Awesome/css/font-awesome.css", ['type'=>'external']);
echo drupal_get_css();
?>
</head>
<body>
<?php
    echo theme('cmisro_browser', $variables);
?>
</body>
</html>
