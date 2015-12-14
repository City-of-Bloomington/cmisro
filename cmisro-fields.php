<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
/**
 * @implements hook_field_info()
 * @see https://api.drupal.org/api/drupal/modules!field!field.api.php/function/hook_field_info/7
 */
function cmisro_field_info()
{
	return [
		'cmisro_reference' => [
			'label'             => 'CMIS Reference',
			'description'       => 'A reference to one or more documents in the CMIS server',
			'default_widget'    => 'cmisro_chooser',
			'default_formatter' => 'cmisro_reference_formatter'
		],
		'cmisro_directorylisting' => [
            'label'             => 'CMIS Directory Listing',
            'description'       => 'Creates a seperate tab view for a CMIS directory',
            'default_widget'    => 'cmisro_chooser',
            'default_formatter' => 'cmisro_directorylisting_formatter'
		]
	];
}

/**
 * @implements hook_field_widget_info()
 * @see https://api.drupal.org/api/drupal/modules!field!field.api.php/function/hook_field_widget_info/7
 */
function cmisro_field_widget_info()
{
	return [
		'cmisro_chooser' => [
			'label'       => 'CMIS Chooser',
			'field types' => ['cmisro_reference', 'cmisro_directorylisting']
		]
	];
}

/**
 * @implements hook_field_widget_form()
 * @see https://api.drupal.org/api/drupal/modules!field!field.api.php/function/hook_field_widget_form/7
 */
function cmisro_field_widget_form(&$form, &$form_state, &$field, &$instance, &$langcode, &$items, &$delta, &$element)
{
	if ($instance['widget']['type'] == 'cmisro_chooser') {
		drupal_add_js(drupal_get_path('module', 'cmisro').'/js/cmisro_field.js');

		$element['reference'] = [
			'#type'          => 'textfield',
            '#title'         => isset($instance['label'])        ? $instance['label']       : 'CMIS Reference',
			'#default_value' => isset($items[$delta])            ? $items[$delta]           : null,
            '#description'   => !empty($instance['description']) ? $instance['description'] : null
		];
	}
	return $element;
}

/**
 * Validate user's CMIS reference
 *
 * Users can enter a CMIS reference in various ways:
 * id:    753026c2-1d1e-40df-bf5b-847f5d23c1df
 * path:  /Sites/bloomington-arts-commission/documentLibrary/Agendas
 * query: select id from cmis:document where ...
 *
 * @implements hook_field_validate()
 * @see https://api.drupal.org/api/drupal/modules!field!field.api.php/function/hook_field_validate/7
 */
function cmisro_field_validate($entity_type, $entity, $field, $instance, $lang, &$items, &$errors)
{
	foreach ($items as $delta=>$item) {
		if (!empty($item['reference'])) {
            $type = _cmisro_referenceType($item['reference']);
            try {
                $o = $type === 'query'
                    ? _cmisro_getQuery ($item['reference'])
                    : _cmisro_getObject($item['reference']);
            }
			catch (Exception $e) {
				$errors[$field['field_name']][$lang][$delta][] = [
					'error'   => 'unknownAttachment',
					'message' => "$instance[label]: {$e->getMessage()}"
				];
			}
		}
	}
}

/**
 * @implements hook_field_is_empty()
 * @see https://api.drupal.org/api/drupal/modules!field!field.api.php/function/hook_field_is_empty/7
 */
function cmisro_field_is_empty($item, $field)
{
    // Both our fields use the same input element
    return empty($item['reference']);
}

/**
 * @implements hook_field_formatter_info()
 * @see https://api.drupal.org/api/drupal/modules!field!field.api.php/function/hook_field_formatter_info/7
 */
function cmisro_field_formatter_info()
{
	return [
		'cmisro_reference_formatter' => [
			'label'       => t('Default'),
			'field types' => ['cmisro_reference']
		],
		'cmisro_directorylisting_formatter' => [
            'label'       => t('Default'),
            'field types' => ['cmisro_directorylisting']
		]
	];
}

/**
 * @implements hook_field_formatter_view()
 * @see https://api.drupal.org/api/drupal/modules!field!field.api.php/function/hook_field_formatter_view/7
 */
function cmisro_field_formatter_view($entity_type, $entity, $field, $instance, $lang, $items, $display)
{
	$element = [];
	switch ($display['type']) {
		case 'cmisro_reference_formatter':
			foreach ($items as $delta => $item) {
                try {
                    $element[$delta] = [
                        '#markup' => theme('cmisro_reference', ['reference'=>$item['reference']])
                    ];
                }
                catch (\Exception $e) {
                    // The item references should have already been checked when the user
                    // originally saved them in the content.
                    // If there's a problem with one of them at display-time,
                    // we can just ignore it.
                }
            }
		break;

		/**
		 * Render a link to the custom route we've declared
		 * @see cmisro_menu()
		 */
		case 'cmisro_directorylisting_formatter':
            foreach ($items as $i => $item) {
                try {
                    $o    = _cmisro_getObject($item['reference']);
                    $uri  = _cmisro_folder_uri($entity->nid, $o['id']);
                    $attr = (current_path() === $uri) ? ['attributes' => ['class' => ['current']]] : [];

                    $element[$i] = ['#markup' => l($o['title'], $uri, $attr)];
                }
                catch (\Exception $e) {
                    // The item references should have already been checked when the user
                    // originally saved them in the content.
                    // If there's a problem with one of them at display-time,
                    // we can just ignore it.
                }
            }
		break;
	}
	return $element;
}
