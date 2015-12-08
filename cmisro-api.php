<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
/**
 * @return CMISService
 */
function _cmisro_service()
{
	static $service;
	if (!$service) {
        libraries_load('chemistry');

		$service = new CMISService(
			variable_get('cmisro_url'),
			variable_get('cmisro_username'),
			variable_get('cmisro_password'),
			variable_get('cmisro_repositoryId')
		);
		$service->succinct = true;
	}
	return $service;
}

/**
 * Determines what kind of reference the user is wanting to make
 *
 * Users can reference a folder, an object, or provide a raw CMIS query.
 * This function determines what kind of reference the user is making
 * by looking at the string they provided
 *
 * folder: /Sites/bloomington-arts-commission/documentLibrary/Agendas
 * query:  select id from cmis:document where ...
 * object: 753026c2-1d1e-40df-bf5b-847f5d23c1df
 *
 * @param string $reference
 * @return string
 */
function _cmisro_referenceType($reference)
{
	if (substr($reference, 0, 1) == '/') {
		return 'path';
	}
	elseif (substr($reference, 0, 6) == 'select') {
		return 'query';
	}
	else {
		return 'id';
	}
}

/**
 * Parses CMIS object response and returns a data object
 *
 * This function provides a simpler data representation than what
 * comes from the CMIS server.  It also handles dealing with variations
 * in what fields contain data we're interested in.
 *
 * @param stdClass $json A single object from the CMIS response
 * @return array
 */
function _cmisro_object($json)
{
	$o = [];

	if (!empty($json->succinctProperties)) {
        $p =  &$json->succinctProperties;
		// Alfresco likes to put versionNumbers into the objectId, instead of
		// only declaring a cmis:versionSeriesId
		$o['id']    = !empty($p->{'cmis:versionSeriesId'})       ? $p->{'cmis:versionSeriesId'}       : $p->{'cmis:objectId'};
		$o['type']  =  isset($p->{'cmis:contentStreamMimeType'}) ? $p->{'cmis:contentStreamMimeType'} : $p->{'cmis:baseTypeId'};

		if (!empty($p->{'cm:title'})) {
			$o['title'] = is_array($p->{'cm:title'}) ? $p->{'cm:title'}[0] : $p->{'cm:title'};
		}
		if (empty($o['title']) && !empty($p->{'cmis:name'})) {
			$o['title'] = $p->{'cmis:name'};
		}

		if (!empty($p->{'cmis:contentStreamLength'})) { $o['filesize'] = $p->{'cmis:contentStreamLength'}; }
        if (!empty($p->{'cmis:path'}))                { $o['path']     = $p->{'cmis:path'}; }

        $o['filename'] = $p->{'cmis:name'};
	}

	return $o;
}

/**
 * Gets a listing of objects from the CMIS server
 *
 * You can specify a path, objectId, or query to generate the listing
 *
 * @param string $reference (path|objectId|query)
 * @param int $offset Results to skip because of paging
 * @return array
 */
function _cmisro_getFolder($reference, $offset=0)
{
	$list = [];
	$s = _cmisro_service();

	switch (_cmisro_referenceType($reference)) {
		case 'path' : $list = $s->getChildrenByPath($reference, $offset); break;
		case 'id'   : $list = $s->getChildren      ($reference, $offset); break;
		case 'query': $list = $s->query            ($reference, $offset); break;
	}
	return $list;
}

function _cmisro_getObject($reference)
{
    $s = _cmisro_service();

    $o = _cmisro_referenceType($reference) === 'path'
        ?  $s->getObjectByPath($reference)
        :  $s->getObject      ($reference);

    return _cmisro_object($o);
}
function _cmisro_getQuery ($query) { return _cmisro_service()->query($query); }


/**
 * Returns the FontAwesome class to use for a given cmis:type
 *
 * @return string
 */
function _cmisro_class_for_type($type) {
	static $lookup = [
		'cmis:folder'=>'folder',
		'application/octet-stream' => 'fa fa-file',
		'image/png' => 'image',
		'image/jpg' => 'image'
	];
	return array_key_exists($type, $lookup) ? $lookup[$type] : $type;
}

/**
 * Returns the Drupal url for correctly handling the document
 *
 * Right now, the only option is to download the document
 *.
 * @TODO implement link to interstitial page for document
 * @return string
 */
function _cmisro_document_url($documentId)
{
    global $base_url;
    return "$base_url/cmisro/download/$documentId";
}

/**
 * Streams the document file to the browser
 *
 * @param string $objectId The CMIS object ID
 */
function _cmisro_download($objectId=null)
{
	if ($objectId) {
		$o = _cmisro_getObject($objectId);

		drupal_add_http_header('Cache-Control', 'no-cache, must-revalidate');
		drupal_add_http_header('Content-Type',   $o['type']);
		drupal_add_http_header('Content-Length', $o['filesize']);
		drupal_add_http_header('Content-Disposition', "attachment; filename=\"$o[filename]\"");
		echo _cmisro_service()->getContentStream($objectId);
		exit();
	}
}
