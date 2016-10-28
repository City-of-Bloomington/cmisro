<?php
/**
 * @copyright 2015-2016 City of Bloomington, Indiana
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
        if (!empty($p->{'cmis:path'}               )) { $o['path'    ] = $p->{'cmis:path'};                }
        if (!empty($p->{'alfcmis:nodeRef'}         )) { $o['nodeRef' ] = $p->{'alfcmis:nodeRef'};          }

        $o['filename'] = $p->{'cmis:name'};
	}

	return $o;
}

/**
 * Cleans up results and returns simple objects from query results
 *
 * Iterates over results and calls _cmisro_object for each item.
 * Then, it returns the simplified list.
 *
 * The full, (even succinct) result responses are way more complicated
 * than we need for Drupal page rendering.  This function simplified
 * the results, making it easier for template authors.
 *
 * @param stdClass $result JSON results
 * @return array
 */
function _cmisro_result_objects($result)
{
    $list = [];
    foreach ($result->objects as $row) {
        $list[] = _cmisro_object($row->object);
    }
    return $list;
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
function _cmisro_getFolderItems($reference, $offset=0)
{
	$s = _cmisro_service();

	switch (_cmisro_referenceType($reference)) {
		case 'path' : $result = $s->getChildrenByPath($reference, $offset); break;
		case 'id'   : $result = $s->getChildren      ($reference, $offset); break;
		case 'query': $result = $s->query            ($reference, $offset); break;
	}
	$list = _cmisro_result_objects($result);

	return $list;
}

/**
 * @param string $folderId
 * @return array
 */
function _cmisro_getSubFolders($folderId)
{
    $s = _cmisro_service();
    return _cmisro_result_objects($s->getFolderTree($folderId));
}

/**
 * @param string $reference
 */
function _cmisro_getObject($reference)
{
    $s = _cmisro_service();

    $o = _cmisro_referenceType($reference) === 'path'
        ?  $s->getObjectByPath($reference)
        :  $s->getObject      ($reference);

    return _cmisro_object($o);
}
function _cmisro_getQuery($query)
{
    return _cmisro_service()->query($query);
}


/**
 * Returns the CSS class to use for a given cmis:type
 *
 * The CSS class name will be the basename of the mime_type
 * for the file.  Invalid characters are converted to a dash.
 *
 * @return string
 */
function _cmisro_class_for_type($type) {
    if ($type === 'cmis:folder') { return 'folder'; }

    if (strpos($type, '/') !== false) {
        list($t, $e) = explode('/', $type);
        return preg_replace('[^a-z0-9\-]', '-', $e);
    }
}

/**
 * Returns the Drupal uri for correctly handling the document
 *
 * @TODO implement link to interstitial page for document
 * @return string
 */
function _cmisro_document_uri($documentId)   { return "cmisro/download/$documentId"; }
function _cmisro_folder_uri($nid, $folderId) { return "cmisro/$nid/$folderId"; }

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

/**
 * Redirect the user to an object's location in Alfresco
 *
 * This should render an HTML page that redirects the user to
 * the object's URL in alfresco share.
 *
 * @param string $objectId
 */
function _cmisro_proxy($objectId)
{
    $o   = _cmisro_getObject($objectId);
    $url = _cmisro_getShareUrl($o['nodeRef']);
    header("Location: $url");
    exit();
}


/**
 * Returns the share url for a node
 *
 * nodeRef should be in the form of:
 * workspace://SpacesStore/bbb3c8b2-3e13-4a23-b00a-3441f34c6a05
 *
 * @param  string $nodeRef An alfresco nodeRef
 * @return string
 */
function _cmisro_getShareUrl($nodeRef)
{
    $cmis = parse_url(variable_get('cmisro_url'));
    $user = variable_get('cmisro_username');
    $pass = variable_get('cmisro_password');

    $url = "$cmis[scheme]://$user:$pass@$cmis[host]/alfresco/service/api/sites/shareUrl?".http_build_query(['nodeRef'=>$nodeRef]);
    $response = cob_http_get($url);
    if ($response) {
        $json = json_decode($response);
        if ($json) {
            return $json->url;
        }
    }
}

/**
 * Converts byte count into human readable filesize
 *
 * @param int     $size   Raw byte count
 * @param string  $format Format string for sprintf()
 * @return string
 */
function _cmisro_size_readable ($size, $format='%01.2f %s')
{
    // adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
    $sizes = ['B', 'KB', 'MB', 'GB'];
    $lastsizestring = end($sizes);
    foreach ($sizes as $sizestring) {
        if ($size < 1024) { break; }
        if ($sizestring != $lastsizestring) { $size /= 1024; }
    }
    if ($sizestring == $sizes[0]) { $format = '%01d %s'; } // Bytes aren't normally fractional
    return sprintf($format, $size, $sizestring);
}
