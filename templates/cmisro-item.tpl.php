<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param mixed $object
 */
$type = !empty($object['type']) ? $object['type'] : $object['mimeType'];
$attr = ['attributes' => ['class' => ['cmis_object', _cmisro_class_for_type($type)]]];

echo l ($object['title'], _cmisro_document_uri($object['id']), $attr);
