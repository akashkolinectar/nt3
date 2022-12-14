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


//
// Class: Ticket
//

Dict::Add('ES CR', 'Spanish', 'Español, Castellano', array(
	'Class:Ticket' => 'Ticket',
	'Class:Ticket+' => 'Ticket',
	'Class:Ticket/Attribute:ref' => 'Ref',
	'Class:Ticket/Attribute:ref+' => 'Ref',
	'Class:Ticket/Attribute:org_id' => 'Organización',
	'Class:Ticket/Attribute:org_id+' => 'Organización',
	'Class:Ticket/Attribute:org_name' => 'Organización',
	'Class:Ticket/Attribute:org_name+' => 'Organización',
	'Class:Ticket/Attribute:caller_id' => 'Reportado por',
	'Class:Ticket/Attribute:caller_id+' => 'Reportado por',
	'Class:Ticket/Attribute:caller_name' => 'Reportado por',
	'Class:Ticket/Attribute:caller_name+' => 'Reportado por',
	'Class:Ticket/Attribute:team_id' => 'Grupo',
	'Class:Ticket/Attribute:team_id+' => 'Grupo',
	'Class:Ticket/Attribute:team_name' => 'Grupo de Trabajo',
	'Class:Ticket/Attribute:team_name+' => 'Grupo de Trabajo',
	'Class:Ticket/Attribute:agent_id' => 'Analista',
	'Class:Ticket/Attribute:agent_id+' => 'Analista',
	'Class:Ticket/Attribute:agent_name' => 'Analista',
	'Class:Ticket/Attribute:agent_name+' => 'Analista',
	'Class:Ticket/Attribute:title' => 'Asunto',
	'Class:Ticket/Attribute:title+' => 'Asunto',
	'Class:Ticket/Attribute:description' => 'Descripción',
	'Class:Ticket/Attribute:description+' => 'Descripción',
	'Class:Ticket/Attribute:start_date' => 'Fecha de Inicio',
	'Class:Ticket/Attribute:start_date+' => 'Fecha de Inicio',
	'Class:Ticket/Attribute:end_date' => 'Fecha de Fin',
	'Class:Ticket/Attribute:end_date+' => 'Fecha de Fin',
	'Class:Ticket/Attribute:last_update' => 'Última Actualización',
	'Class:Ticket/Attribute:last_update+' => 'Última Actualización',
	'Class:Ticket/Attribute:close_date' => 'Fecha de Cierre',
	'Class:Ticket/Attribute:close_date+' => 'Fecha de Cierre',
	'Class:Ticket/Attribute:private_log' => 'Bitácora Privada',
	'Class:Ticket/Attribute:private_log+' => 'Bitácora Privada',
	'Class:Ticket/Attribute:contacts_list' => 'Contactos',
	'Class:Ticket/Attribute:contacts_list+' => 'Contactos',
	'Class:Ticket/Attribute:functionalcis_list' => 'ECs',
	'Class:Ticket/Attribute:functionalcis_list+' => 'Elementos de Configuración',
	'Class:Ticket/Attribute:workorders_list' => 'Ordenes de Trabajo',
	'Class:Ticket/Attribute:workorders_list+' => 'Ordenes de Trabajo',
	'Class:Ticket/Attribute:finalclass' => 'Clase',
	'Class:Ticket/Attribute:finalclass+' => 'Clase',
));


//
// Class: lnkContactToTicket
//

Dict::Add('ES CR', 'Spanish', 'Español, Castellano', array(
	'Class:lnkContactToTicket' => 'Relación Contacto y Ticket',
	'Class:lnkContactToTicket+' => 'Relación Contacto y Ticket',
	'Class:lnkContactToTicket/Attribute:ticket_id' => 'Ticket',
	'Class:lnkContactToTicket/Attribute:ticket_id+' => 'Ticket',
	'Class:lnkContactToTicket/Attribute:ticket_ref' => 'Ref',
	'Class:lnkContactToTicket/Attribute:ticket_ref+' => 'Ref',
	'Class:lnkContactToTicket/Attribute:contact_id' => 'Contacto',
	'Class:lnkContactToTicket/Attribute:contact_id+' => 'Contacto',
	'Class:lnkContactToTicket/Attribute:contact_email' => 'Correo Electrónico',
	'Class:lnkContactToTicket/Attribute:contact_email+' => 'Correo Electrónico',
	'Class:lnkContactToTicket/Attribute:role' => 'Rol',
	'Class:lnkContactToTicket/Attribute:role+' => 'Rol',
));

//
// Class: lnkFunctionalCIToTicket
//

Dict::Add('ES CR', 'Spanish', 'Español, Castellano', array(
	'Class:lnkFunctionalCIToTicket' => 'Relación EC Funcional y Ticket',
	'Class:lnkFunctionalCIToTicket+' => 'Relación EC Funcional y Ticket',
	'Class:lnkFunctionalCIToTicket/Attribute:ticket_id' => 'Ticket',
	'Class:lnkFunctionalCIToTicket/Attribute:ticket_id+' => 'Ticket',
	'Class:lnkFunctionalCIToTicket/Attribute:ticket_ref' => 'Ref.',
	'Class:lnkFunctionalCIToTicket/Attribute:ticket_ref+' => 'Ref.',
	'Class:lnkFunctionalCIToTicket/Attribute:functionalci_id' => 'EC',
	'Class:lnkFunctionalCIToTicket/Attribute:functionalci_id+' => 'Elemanto de Configuración',
	'Class:lnkFunctionalCIToTicket/Attribute:functionalci_name' => 'Elemanto de Configuración',
	'Class:lnkFunctionalCIToTicket/Attribute:functionalci_name+' => 'Elemanto de Configuración',
	'Class:lnkFunctionalCIToTicket/Attribute:impact' => 'Impacto',
	'Class:lnkFunctionalCIToTicket/Attribute:impact+' => 'Impacto',
));


//
// Class: WorkOrder
//

Dict::Add('ES CR', 'Spanish', 'Español, Castellano', array(
	'Class:WorkOrder' => 'Orden de Trabajo',
	'Class:WorkOrder+' => 'Orden de Trabajo',
	'Class:WorkOrder/Attribute:name' => 'Nombre',
	'Class:WorkOrder/Attribute:name+' => 'Nombre de la Orden de Trabajo',
	'Class:WorkOrder/Attribute:status' => 'Estatus',
	'Class:WorkOrder/Attribute:status+' => 'Estatus',
	'Class:WorkOrder/Attribute:status/Value:open' => 'Abierto',
	'Class:WorkOrder/Attribute:status/Value:open+' => 'Abierto',
	'Class:WorkOrder/Attribute:status/Value:closed' => 'Cerrado',
	'Class:WorkOrder/Attribute:status/Value:closed+' => 'Cerrado',
	'Class:WorkOrder/Attribute:description' => 'Descripción',
	'Class:WorkOrder/Attribute:description+' => 'Descripción',
	'Class:WorkOrder/Attribute:ticket_id' => 'Ticket',
	'Class:WorkOrder/Attribute:ticket_id+' => 'Ticket',
	'Class:WorkOrder/Attribute:ticket_ref' => 'Ref. Ticket',
	'Class:WorkOrder/Attribute:ticket_ref+' => 'Ref. Ticket',
	'Class:WorkOrder/Attribute:team_id' => 'Grupo',
	'Class:WorkOrder/Attribute:team_id+' => 'Grupo',
	'Class:WorkOrder/Attribute:team_name' => 'Grupo de Trabajo',
	'Class:WorkOrder/Attribute:team_name+' => 'Grupo de Trabajo',
	'Class:WorkOrder/Attribute:agent_id' => 'Analista',
	'Class:WorkOrder/Attribute:agent_id+' => 'Analista',
	'Class:WorkOrder/Attribute:agent_email' => 'Correo Electrónico del Analista',
	'Class:WorkOrder/Attribute:agent_email+' => 'Correo Electrónico del Analista',
	'Class:WorkOrder/Attribute:start_date' => 'Fecha de Inicio',
	'Class:WorkOrder/Attribute:start_date+' => 'Fecha de Inicio',
	'Class:WorkOrder/Attribute:end_date' => 'Fecha de Fin',
	'Class:WorkOrder/Attribute:end_date+' => 'Fecha de Fin',
	'Class:WorkOrder/Attribute:log' => 'Bitácora',
	'Class:WorkOrder/Attribute:log+' => 'Bitácora',
	'Class:WorkOrder/Stimulus:ev_close' => 'Cerrar',
	'Class:WorkOrder/Stimulus:ev_close+' => 'Cerrar',
));


// Fieldset translation
Dict::Add('ES CR', 'Spanish', 'Español, Castellano', array(

	'Ticket:baseinfo' => 'Información General',
	'Ticket:date' => 'Fechas',
	'Ticket:contact' => 'Contactos',
	'Ticket:moreinfo' => 'Más Información',
	'Ticket:relation' => 'Relaciones',
	'Ticket:log' => 'Comunicaciones',
	'Ticket:Type' => 'Clasificación',
	'Ticket:support' => 'Soporte',
	'Ticket:resolution' => 'Solución',
	'Ticket:SLA' => 'Reporte de SLA',
	'WorkOrder:Details' => 'Detalles',
	'WorkOrder:Moreinfo' => 'Más Información',

	'Ticket:ImpactAnalysis' => 'Análisis de Impacto',
	'Class:lnkContactToTicket/Attribute:role_code' => 'Rol',
	'Class:lnkContactToTicket/Attribute:role_code/Value:manual' => 'Agregado manualmente',
	'Class:lnkContactToTicket/Attribute:role_code/Value:computed' => 'Calculado',
	'Class:lnkContactToTicket/Attribute:role_code/Value:do_not_notify' => 'No notificar',
	'Class:lnkFunctionalCIToTicket/Attribute:impact_code' => 'Impacto',
	'Class:lnkFunctionalCIToTicket/Attribute:impact_code/Value:manual' => 'Agregado manualmente',
	'Class:lnkFunctionalCIToTicket/Attribute:impact_code/Value:computed' => 'Calculado',
	'Class:lnkFunctionalCIToTicket/Attribute:impact_code/Value:not_impacted' => 'No impactado',
	'Tickets:ResolvedFrom' => 'Automaticamente resuelto de %1$s',
	'Class:cmdbAbstractObject/Method:Set' => 'Asignar',
	'Class:cmdbAbstractObject/Method:Set+' => 'Asignar campo con valor estático',
	'Class:cmdbAbstractObject/Method:Set/Param:1' => 'Campo Destino',
	'Class:cmdbAbstractObject/Method:Set/Param:1+' => 'El campo a asignar, en el objeto actual',
	'Class:cmdbAbstractObject/Method:Set/Param:2' => 'Valor',
	'Class:cmdbAbstractObject/Method:Set/Param:2+' => 'Valor a asignar',
	'Class:cmdbAbstractObject/Method:SetCurrentDate' => 'Asignar fecha actual',
	'Class:cmdbAbstractObject/Method:SetCurrentDate+' => 'Asignar fecha y hora actuales',
	'Class:cmdbAbstractObject/Method:SetCurrentDate/Param:1' => 'Campo Destino',
	'Class:cmdbAbstractObject/Method:SetCurrentDate/Param:1+' => 'El campo a asignar, en el objeto actual',
	'Class:cmdbAbstractObject/Method:SetCurrentUser' => 'Asignar Usuario actual',
	'Class:cmdbAbstractObject/Method:SetCurrentUser+' => 'Asignar Usuario actual',
	'Class:cmdbAbstractObject/Method:SetCurrentUser/Param:1' => 'Campo Destino',
	'Class:cmdbAbstractObject/Method:SetCurrentUser/Param:1+' => 'If the field is a string then the friendly name will be used, otherwise the identifier will be used. That friendly name is the name of the person if any is attached to the user, otherwise it is the login.',
	'Class:cmdbAbstractObject/Method:SetCurrentPerson' => 'Asignar Persona actual',
	'Class:cmdbAbstractObject/Method:SetCurrentPerson+' => 'Asignar Persona actual',
	'Class:cmdbAbstractObject/Method:SetCurrentPerson/Param:1' => 'Campo Destino',
	'Class:cmdbAbstractObject/Method:SetCurrentPerson/Param:1+' => 'If the field is a string then the friendly name will be used, otherwise the identifier will be used.',
	'Class:cmdbAbstractObject/Method:SetElapsedTime' => 'Asignar tiempo transcurrido',
	'Class:cmdbAbstractObject/Method:SetElapsedTime+' => 'Asignar tiempo transcurrido (segundos)',
	'Class:cmdbAbstractObject/Method:SetElapsedTime/Param:1' => 'Campo Destino',
	'Class:cmdbAbstractObject/Method:SetElapsedTime/Param:1+' => 'The field to set, in the current object',
	'Class:cmdbAbstractObject/Method:SetElapsedTime/Param:2' => 'Campo de Referencia',
	'Class:cmdbAbstractObject/Method:SetElapsedTime/Param:2+' => 'The field from which to get the reference date',
	'Class:cmdbAbstractObject/Method:SetElapsedTime/Param:3' => 'Horas Trabajadas',
	'Class:cmdbAbstractObject/Method:SetElapsedTime/Param:3+' => 'Dejar vacio para utilizar el horario de trabajo estandar, o dejar por omisión para usar esquema 7x24',
	'Class:cmdbAbstractObject/Method:Reset' => 'Restablecer',
	'Class:cmdbAbstractObject/Method:Reset+' => 'Restablecer a valor por omisión',
	'Class:cmdbAbstractObject/Method:Reset/Param:1' => 'Campo Destino',
	'Class:cmdbAbstractObject/Method:Reset/Param:1+' => 'Campo a restablecer',
	'Class:cmdbAbstractObject/Method:Copy' => 'Copiar',
	'Class:cmdbAbstractObject/Method:Copy+' => 'Copier el valor de un campo a otro',
	'Class:cmdbAbstractObject/Method:Copy/Param:1' => 'Campo Destino',
	'Class:cmdbAbstractObject/Method:Copy/Param:1+' => 'Campo a asignar',
	'Class:cmdbAbstractObject/Method:Copy/Param:2' => 'Campo Origen',
	'Class:cmdbAbstractObject/Method:Copy/Param:2+' => 'Campo de donde se obtendrá valor',
	'Class:ResponseTicketTTO/Interface:iMetricComputer' => 'Tiempo a Pertenencia',
	'Class:ResponseTicketTTO/Interface:iMetricComputer+' => 'Objetivo basado en SLT de tipo TTO',
	'Class:ResponseTicketTTR/Interface:iMetricComputer' => 'Tiempo a Resolución',
	'Class:ResponseTicketTTR/Interface:iMetricComputer+' => 'Objetivo basado en SLT de tipo TTR',
	'portal:nt3-portal' => 'Portal de Usuario',
	'Page:DefaultTitle' => 'nt3 - Portal de Usuario',
	'Brick:Portal:UserProfile:Title' => 'Mi perfil',
	'Brick:Portal:NewRequest:Title' => 'Nuevo Requerimiento',
	'Brick:Portal:NewRequest:Title+' => '¿Necesita ayuda? Elija del catálogo de servicios y envíe su requerimiento a nuestros equipos de soporte.',
	'Brick:Portal:OngoingRequests:Title' => 'Requerimientos en Proceso',
	'Brick:Portal:OngoingRequests:Title+' => 'Revise sus requerimientos en proceso. Compruebe el progreso, agregue comentarios, adjunte documentos, entienda la solución. </ P>',
	'Brick:Portal:OngoingRequests:Tab:OnGoing' => 'En proceso',
	'Brick:Portal:OngoingRequests:Tab:Resolved' => 'Resuelto',
	'Brick:Portal:ClosedRequests:Title' => 'Requerimientos Cerrados',
));
