<?php

namespace Combodo\nt3\Portal\Router;

class ManageBrickRouter extends AbstractRouter
{
	static $aRoutes = array(
        array(
            'pattern' => '/manage/{sBrickId}/{sGroupingTab}',
            'callback' => 'Combodo\\nt3\\Portal\\Controller\\ManageBrickController::DisplayAction',
            'bind' => 'p_manage_brick',
            'asserts' => array(),
            'values' => array(
                'sGroupingTab' => null,
            )
        ),
        array(
            'pattern' => '/manage/{sBrickId}/display-as/{sDisplayMode}/{sGroupingTab}',
            'callback' => 'Combodo\\nt3\\Portal\\Controller\\ManageBrickController::DisplayAction',
            'bind' => 'p_manage_brick_display_as',
            'asserts' => array(
                'sDisplayMode' => 'list|pie-chart|bar-chart'
            ),
            'values' => array(
                'sGroupingTab' => null,
            )
        ),
        array(
			'pattern' => '/manage/{sBrickId}/{sGroupingTab}/{sGroupingArea}/page/{iPageNumber}/show/{iListLength}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\ManageBrickController::DisplayAction',
			'bind' => 'p_manage_brick_lazy',
			'asserts' => array(
				'iPageNumber' => '\d+',
				'iListLength' => '\d+',
			),
			'values' => array(
				'sDataLoading' => 'lazy',
				'iPageNumber' => '1',
				'iListLength' => '20',
			)
		),
		array(
			'pattern' => '/manage/export/excel/start/{sBrickId}/{sGroupingTab}/{sGroupingArea}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\ManageBrickController::ExcelExportStartAction',
			'bind' => 'p_manage_brick_excel_export_start',
			'asserts' => array(),
			'values' => array(),
		),
	);

}
