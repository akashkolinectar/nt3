<?php

namespace Combodo\nt3\Portal\Router;

use Silex\Application;

class ObjectRouter extends AbstractRouter
{
	static $aRoutes = array(
		array('pattern' => '/object/create/{sObjectClass}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::CreateAction',
			'bind' => 'p_object_create'
		),
		array('pattern' => '/object/create-from-factory/{sObjectClass}/{sObjectId}/{sEncodedMethodName}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::CreateFromFactoryAction',
			'bind' => 'p_object_create_from_factory'
		),
		array('pattern' => '/object/edit/{sObjectClass}/{sObjectId}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::EditAction',
			'bind' => 'p_object_edit'
		),
		array('pattern' => '/object/view/{sObjectClass}/{sObjectId}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::ViewAction',
			'bind' => 'p_object_view'
		),
		array('pattern' => '/object/apply-stimulus/{sStimulusCode}/{sObjectClass}/{sObjectId}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::ApplyStimulusAction',
			'bind' => 'p_object_apply_stimulus'
		),
		array('pattern' => '/object/search',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::SearchRegularAction',
			'bind' => 'p_object_search_regular'
		),
		array('pattern' => '/object/search/from-attribute/{sTargetAttCode}/{sHostObjectClass}/{sHostObjectId}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::SearchFromAttributeAction',
			'bind' => 'p_object_search_from_attribute',
			'values' => array(
				'sHostObjectClass' => null,
				'sHostObjectId' => null
			)
		),
		array('pattern' => '/object/search/autocomplete/{sTargetAttCode}/{sHostObjectClass}/{sHostObjectId}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::SearchAutocompleteAction',
			'bind' => 'p_object_search_autocomplete',
			'values' => array(
				'sHostObjectClass' => null,
				'sHostObjectId' => null
			)
		),
		array('pattern' => '/object/search/hierarchy/{sTargetAttCode}/{sHostObjectClass}/{sHostObjectId}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::SearchHierarchyAction',
			'bind' => 'p_object_search_hierarchy',
			'values' => array(
				'sHostObjectClass' => null,
				'sHostObjectId' => null
			)
		),
		array('pattern' => '/object/search/{sMode}/{sTargetAttCode}/{sHostObjectClass}/{sHostObjectId}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::SearchAction',
			'bind' => 'p_object_search_generic',
			'values' => array(
				'sMode' => '-sMode-',
				'sHostObjectClass' => null,
				'sHostObjectId' => null
			)
		),
		array('pattern' => '/object/get-informations/json',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::GetInformationsAsJsonAction',
			'bind' => 'p_object_get_informations_json',
		),
        array('pattern' => '/object/document/display/{sObjectClass}/{sObjectId}/{sObjectField}',
            'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::DocumentAction',
            'bind' => 'p_object_document_display',
            'values' => array(
                'sOperation' => 'display'
            )
        ),
        array('pattern' => '/object/document/download/{sObjectClass}/{sObjectId}/{sObjectField}',
            'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::DocumentAction',
            'bind' => 'p_object_document_download',
            'values' => array(
                'sOperation' => 'download'
            )
        ),
        array('pattern' => '/object/attachment/add',
            'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::AttachmentAction',
            'bind' => 'p_object_attachment_add'
        ),
        array('pattern' => '/object/attachment/download/{sAttachmentId}',
            'callback' => 'Combodo\\nt3\\Portal\\Controller\\ObjectController::AttachmentAction',
            'bind' => 'p_object_attachment_download',
            'values' => array(
                'sOperation' => 'download'
            )
        ),
	);

}

?>