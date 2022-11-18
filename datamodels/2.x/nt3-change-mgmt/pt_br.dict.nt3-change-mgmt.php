<?php

Dict::Add('PT BR', 'Brazilian', 'Brazilian', array(
	'Menu:ChangeManagement' => 'Gerenciamento Mudanças',
	'Menu:Change:Overview' => 'Visão geral',
	'Menu:Change:Overview+' => '',
	'Menu:NewChange' => 'Nova mudança',
	'Menu:NewChange+' => 'Criar uma nova solicitação de mudança',
	'Menu:SearchChanges' => 'Pesquisar por mudanças',
	'Menu:SearchChanges+' => 'Pesquisar por solicitação de mudança',
	'Menu:Change:Shortcuts' => 'Atalho',
	'Menu:Change:Shortcuts+' => '',
	'Menu:WaitingAcceptance' => 'Mudanças à espera de aceitação',
	'Menu:WaitingAcceptance+' => '',
	'Menu:WaitingApproval' => 'Mudanças aguardando aprovação',
	'Menu:WaitingApproval+' => '',
	'Menu:Changes' => 'Mudanças abertas',
	'Menu:Changes+' => 'Todas mudanças abertas',
	'Menu:MyChanges' => 'Mudanças atribuídas a mim',
	'Menu:MyChanges+' => 'Mudanças atribuídas a mim (como Agente)',
	'UI-ChangeManagementOverview-ChangeByCategory-last-7-days' => 'Mudanças por categoria nos últimos 7 dias',
	'UI-ChangeManagementOverview-Last-7-days' => 'Número de mudanças nos últimos 7 dias',
	'UI-ChangeManagementOverview-ChangeByDomain-last-7-days' => 'Mudanças por domínio nos últimos 7 dias',
	'UI-ChangeManagementOverview-ChangeByStatus-last-7-days' => 'Mudanças por status nos últimos 7 dias',
));

// Dictionnay conventions
// Class:<class_name>
// Class:<class_name>+
// Class:<class_name>/Attribute:<attribute_code>
// Class:<class_name>/Attribute:<attribute_code>+
// Class:<class_name>/Attribute:<attribute_code>/Value:<value>
// Class:<class_name>/Attribute:<attribute_code>/Value:<value>+
// Class:<class_name>/Stimulus:<stimulus_code>
// Class:<class_name>/Stimulus:<stimulus_code>+


//
// Class: Change
//

Dict::Add('PT BR', 'Brazilian', 'Brazilian', array(
	'Class:Change' => 'Mudanças',
	'Class:Change+' => '',
	'Class:Change/Attribute:status' => 'Status',
	'Class:Change/Attribute:status+' => '',
	'Class:Change/Attribute:status/Value:new' => 'Nova',
	'Class:Change/Attribute:status/Value:new+' => '',
	'Class:Change/Attribute:status/Value:assigned' => 'Atribuido',
	'Class:Change/Attribute:status/Value:assigned+' => '',
	'Class:Change/Attribute:status/Value:planned' => 'Planejado',
	'Class:Change/Attribute:status/Value:planned+' => '',
	'Class:Change/Attribute:status/Value:rejected' => 'Rejeitado',
	'Class:Change/Attribute:status/Value:rejected+' => '',
	'Class:Change/Attribute:status/Value:approved' => 'Aprovado',
	'Class:Change/Attribute:status/Value:approved+' => '',
	'Class:Change/Attribute:status/Value:closed' => 'Fechado',
	'Class:Change/Attribute:status/Value:closed+' => '',
	'Class:Change/Attribute:category' => 'Categoria',
	'Class:Change/Attribute:category+' => '',
	'Class:Change/Attribute:category/Value:application' => 'aplicação',
	'Class:Change/Attribute:category/Value:application+' => 'aplicação',
	'Class:Change/Attribute:category/Value:hardware' => 'hardware',
	'Class:Change/Attribute:category/Value:hardware+' => 'hardware',
	'Class:Change/Attribute:category/Value:network' => 'rede',
	'Class:Change/Attribute:category/Value:network+' => 'rede',
	'Class:Change/Attribute:category/Value:other' => 'outro',
	'Class:Change/Attribute:category/Value:other+' => 'outro',
	'Class:Change/Attribute:category/Value:software' => 'software',
	'Class:Change/Attribute:category/Value:software+' => 'software',
	'Class:Change/Attribute:category/Value:system' => 'sistema',
	'Class:Change/Attribute:category/Value:system+' => 'sistema',
	'Class:Change/Attribute:reject_reason' => 'Razão rejeição',
	'Class:Change/Attribute:reject_reason+' => '',
	'Class:Change/Attribute:changemanager_id' => 'Gerente mudança',
	'Class:Change/Attribute:changemanager_id+' => '',
	'Class:Change/Attribute:changemanager_email' => 'Email gerente mudança',
	'Class:Change/Attribute:changemanager_email+' => '',
	'Class:Change/Attribute:parent_id' => 'Parente mudança',
	'Class:Change/Attribute:parent_id+' => '',
	'Class:Change/Attribute:parent_name' => 'Ref parente mudança',
	'Class:Change/Attribute:parent_name+' => '',
	'Class:Change/Attribute:creation_date' => 'Data criação',
	'Class:Change/Attribute:creation_date+' => '',
	'Class:Change/Attribute:approval_date' => 'Data aprovação',
	'Class:Change/Attribute:approval_date+' => '',
	'Class:Change/Attribute:fallback_plan' => 'Plano de contingência',
	'Class:Change/Attribute:fallback_plan+' => '',
	'Class:Change/Attribute:related_request_list' => 'Solicitações relacionadas',
	'Class:Change/Attribute:related_request_list+' => 'Todas as solicitações de usuários ligados a esta mudança',
	'Class:Change/Attribute:related_incident_list' => 'Incidentes relacionados',
	'Class:Change/Attribute:related_incident_list+' => 'Todos os incidentes ligados a esta mudança',
	'Class:Change/Attribute:related_problems_list' => 'Problemas relacionados',
	'Class:Change/Attribute:related_problems_list+' => 'Todos os problemas relacionados com esta mudança',
	'Class:Change/Attribute:child_changes_list' => 'Mudanças filhas',
	'Class:Change/Attribute:child_changes_list+' => 'Todas as sub-mudanças ligadas a esta mudança',
	'Class:Change/Attribute:parent_id_friendlyname' => 'Nome amigável mudança relacionado',
	'Class:Change/Attribute:parent_id_friendlyname+' => '',
	'Class:Change/Stimulus:ev_assign' => 'Atribuir',
	'Class:Change/Stimulus:ev_assign+' => '',
	'Class:Change/Stimulus:ev_plan' => 'Planejar',
	'Class:Change/Stimulus:ev_plan+' => '',
	'Class:Change/Stimulus:ev_reject' => 'Rejeitar',
	'Class:Change/Stimulus:ev_reject+' => '',
	'Class:Change/Stimulus:ev_reopen' => 'Re-abrir',
	'Class:Change/Stimulus:ev_reopen+' => '',
	'Class:Change/Stimulus:ev_approve' => 'Aprovar',
	'Class:Change/Stimulus:ev_approve+' => '',
	'Class:Change/Stimulus:ev_finish' => 'Fechar',
	'Class:Change/Stimulus:ev_finish+' => '',
	'Tickets:Related:OpenChanges' => 'Open changes~~',
	'Tickets:Related:RecentChanges' => 'Recent changes (72h)~~',
	'Class:Change/Attribute:outage' => 'Outage',
	'Class:Change/Attribute:outage/Value:no' => 'Não',
	'Class:Change/Attribute:outage/Value:yes' => 'Sim',
));

?>
