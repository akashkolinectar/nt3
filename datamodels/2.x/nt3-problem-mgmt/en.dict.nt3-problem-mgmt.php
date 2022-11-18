<?php

// Dictionnay conventions
// Class:<class_name>
// Class:<class_name>+
// Class:<class_name>/Attribute:<attribute_code>
// Class:<class_name>/Attribute:<attribute_code>+
// Class:<class_name>/Attribute:<attribute_code>/Value:<value>
// Class:<class_name>/Attribute:<attribute_code>/Value:<value>+
// Class:<class_name>/Stimulus:<stimulus_code>
// Class:<class_name>/Stimulus:<stimulus_code>+

//////////////////////////////////////////////////////////////////////
// Classes in 'bizmodel'
//////////////////////////////////////////////////////////////////////
//

// Dictionnay conventions
// Class:<class_name>
// Class:<class_name>+
// Class:<class_name>/Attribute:<attribute_code>
// Class:<class_name>/Attribute:<attribute_code>+
// Class:<class_name>/Attribute:<attribute_code>/Value:<value>
// Class:<class_name>/Attribute:<attribute_code>/Value:<value>+
// Class:<class_name>/Stimulus:<stimulus_code>
// Class:<class_name>/Stimulus:<stimulus_code>+




Dict::Add('EN US', 'English', 'English', array(
        'Menu:ProblemManagement' => 'Problem Management',
        'Menu:ProblemManagement+' => 'Problem Management',
    	'Menu:Problem:Overview' => 'Overview',
    	'Menu:Problem:Overview+' => 'Overview',
    	'Menu:NewProblem' => 'New problem',
    	'Menu:NewProblem+' => 'New problem',
    	'Menu:SearchProblems' => 'Search for problems',
    	'Menu:SearchProblems+' => 'Search for problems',
    	'Menu:Problem:Shortcuts' => 'Shortcuts',
        'Menu:Problem:MyProblems' => 'My problems',
        'Menu:Problem:MyProblems+' => 'My problems',
        'Menu:Problem:OpenProblems' => 'All open problems',
        'Menu:Problem:OpenProblems+' => 'All open problems',
	'UI-ProblemManagementOverview-ProblemByService' => 'Problems by service',
	'UI-ProblemManagementOverview-ProblemByService+' => 'Problems by service',
	'UI-ProblemManagementOverview-ProblemByPriority' => 'Problems by priority',
	'UI-ProblemManagementOverview-ProblemByPriority+' => 'Problems by priority',
	'UI-ProblemManagementOverview-ProblemUnassigned' => 'Unassigned problems',
	'UI-ProblemManagementOverview-ProblemUnassigned+' => 'Unassigned problems',
	'UI:ProblemMgmtMenuOverview:Title' => 'Dashboard for Problem Management',
	'UI:ProblemMgmtMenuOverview:Title+' => 'Dashboard for Problem Management',

));
//
// Class: Problem
//

Dict::Add('EN US', 'English', 'English', array(
	'Class:Problem' => 'Problem',
	'Class:Problem+' => '',
	'Class:Problem/Attribute:status' => 'Status',
	'Class:Problem/Attribute:status+' => '',
	'Class:Problem/Attribute:status/Value:new' => 'New',
	'Class:Problem/Attribute:status/Value:new+' => '',
	'Class:Problem/Attribute:status/Value:assigned' => 'Assigned',
	'Class:Problem/Attribute:status/Value:assigned+' => '',
	'Class:Problem/Attribute:status/Value:resolved' => 'Resolved',
	'Class:Problem/Attribute:status/Value:resolved+' => '',
	'Class:Problem/Attribute:status/Value:closed' => 'Closed',
	'Class:Problem/Attribute:status/Value:closed+' => '',
	'Class:Problem/Attribute:service_id' => 'Service',
	'Class:Problem/Attribute:service_id+' => '',
	'Class:Problem/Attribute:service_name' => 'Service name',
	'Class:Problem/Attribute:service_name+' => '',
	'Class:Problem/Attribute:servicesubcategory_id' => 'Service subcategory',
	'Class:Problem/Attribute:servicesubcategory_id+' => '',
	'Class:Problem/Attribute:servicesubcategory_name' => 'Service subcategory',
	'Class:Problem/Attribute:servicesubcategory_name+' => '',
	'Class:Problem/Attribute:product' => 'Product',
	'Class:Problem/Attribute:product+' => '',
	'Class:Problem/Attribute:impact' => 'Impact',
	'Class:Problem/Attribute:impact+' => '',
	'Class:Problem/Attribute:impact/Value:1' => 'A Department',
	'Class:Problem/Attribute:impact/Value:1+' => '',
	'Class:Problem/Attribute:impact/Value:2' => 'A Service',
	'Class:Problem/Attribute:impact/Value:2+' => '',
	'Class:Problem/Attribute:impact/Value:3' => 'A person',
	'Class:Problem/Attribute:impact/Value:3+' => '',
	'Class:Problem/Attribute:urgency' => 'Urgency',
	'Class:Problem/Attribute:urgency+' => '',
	'Class:Problem/Attribute:urgency/Value:1' => 'critical',
	'Class:Problem/Attribute:urgency/Value:1+' => 'critical',
	'Class:Problem/Attribute:urgency/Value:2' => 'high',
	'Class:Problem/Attribute:urgency/Value:2+' => 'high',
	'Class:Problem/Attribute:urgency/Value:3' => 'medium',
	'Class:Problem/Attribute:urgency/Value:3+' => 'medium',
	'Class:Problem/Attribute:urgency/Value:4' => 'low',
	'Class:Problem/Attribute:urgency/Value:4+' => 'low',
	'Class:Problem/Attribute:priority' => 'Priority',
	'Class:Problem/Attribute:priority+' => '',
	'Class:Problem/Attribute:priority/Value:1' => 'Critical',
	'Class:Problem/Attribute:priority/Value:1+' => 'Critical',
	'Class:Problem/Attribute:priority/Value:2' => 'High',
	'Class:Problem/Attribute:priority/Value:2+' => 'High',
	'Class:Problem/Attribute:priority/Value:3' => 'Medium',
	'Class:Problem/Attribute:priority/Value:3+' => 'Medium',
	'Class:Problem/Attribute:priority/Value:4' => 'Low',
	'Class:Problem/Attribute:priority/Value:4+' => 'Low',
	'Class:Problem/Attribute:related_change_id' => 'Related Change',
	'Class:Problem/Attribute:related_change_id+' => '',
	'Class:Problem/Attribute:related_change_ref' => 'Related Change ref',
	'Class:Problem/Attribute:related_change_ref+' => '',
	'Class:Problem/Attribute:assignment_date' => 'Assignment Date',
	'Class:Problem/Attribute:assignment_date+' => '',
	'Class:Problem/Attribute:resolution_date' => 'Resolution Date',
	'Class:Problem/Attribute:resolution_date+' => '',
	'Class:Problem/Attribute:knownerrors_list' => 'Known Errors',
	'Class:Problem/Attribute:knownerrors_list+' => 'All the known errors that are linked to this problem',
	'Class:Problem/Attribute:related_request_list' => 'Related requests',
	'Class:Problem/Attribute:related_request_list+' => 'All the requests that are related to this problem',
	'Class:Problem/Attribute:related_incident_list' => 'Related incidents',
	'Class:Problem/Attribute:related_incident_list+' => 'All the incidents that are related to this problem',
	'Class:Problem/Stimulus:ev_assign' => 'Assign',
	'Class:Problem/Stimulus:ev_assign+' => '',
	'Class:Problem/Stimulus:ev_reassign' => 'Reassign',
	'Class:Problem/Stimulus:ev_reassign+' => '',
	'Class:Problem/Stimulus:ev_resolve' => 'Resolve',
	'Class:Problem/Stimulus:ev_resolve+' => '',
	'Class:Problem/Stimulus:ev_close' => 'Close',
	'Class:Problem/Stimulus:ev_close+' => '',
));

?>
