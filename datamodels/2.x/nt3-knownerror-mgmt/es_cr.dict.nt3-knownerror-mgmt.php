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

//
// Class: KnownError
//

Dict::Add('ES CR', 'Spanish', 'Español, Castellano', array(
	'Class:KnownError' => 'Error Conocido',
	'Class:KnownError+' => 'Documentación para un error conocido',
	'Class:KnownError/Attribute:name' => 'Nombre',
	'Class:KnownError/Attribute:name+' => 'Nombre del Error Conocido',
	'Class:KnownError/Attribute:org_id' => 'Organización',
	'Class:KnownError/Attribute:org_id+' => 'Organización',
	'Class:KnownError/Attribute:cust_name' => 'Nombre',
	'Class:KnownError/Attribute:cust_name+' => 'Nombre',
	'Class:KnownError/Attribute:problem_id' => 'Problema Relacionado',
	'Class:KnownError/Attribute:problem_id+' => 'Problema',
	'Class:KnownError/Attribute:problem_ref' => 'Referencia',
	'Class:KnownError/Attribute:problem_ref+' => 'Refencia',
	'Class:KnownError/Attribute:symptom' => 'Síntoma',
	'Class:KnownError/Attribute:symptom+' => 'Síntoma',
	'Class:KnownError/Attribute:root_cause' => 'Causa Raíz',
	'Class:KnownError/Attribute:root_cause+' => 'Causa Raíz',
	'Class:KnownError/Attribute:workaround' => 'Solución Temporal',
	'Class:KnownError/Attribute:workaround+' => 'Solución Temporal',
	'Class:KnownError/Attribute:solution' => 'Solución Final',
	'Class:KnownError/Attribute:solution+' => 'Solución Final',
	'Class:KnownError/Attribute:error_code' => 'Código de Error',
	'Class:KnownError/Attribute:error_code+' => 'Código de Error',
	'Class:KnownError/Attribute:domain' => 'Dominio',
	'Class:KnownError/Attribute:domain+' => 'Dominio',
	'Class:KnownError/Attribute:domain/Value:Application' => 'Aplicación',
	'Class:KnownError/Attribute:domain/Value:Application+' => 'Aplicación',
	'Class:KnownError/Attribute:domain/Value:Desktop' => 'Escritorio',
	'Class:KnownError/Attribute:domain/Value:Desktop+' => 'Escritorio',
	'Class:KnownError/Attribute:domain/Value:Network' => 'Red',
	'Class:KnownError/Attribute:domain/Value:Network+' => 'Red',
	'Class:KnownError/Attribute:domain/Value:Server' => 'Servidor',
	'Class:KnownError/Attribute:domain/Value:Server+' => 'Servidor',
	'Class:KnownError/Attribute:vendor' => 'Proveedor',
	'Class:KnownError/Attribute:vendor+' => 'Proveedor',
	'Class:KnownError/Attribute:model' => 'Modelo',
	'Class:KnownError/Attribute:model+' => 'Modelo',
	'Class:KnownError/Attribute:version' => 'Versión',
	'Class:KnownError/Attribute:version+' => 'Versión',
	'Class:KnownError/Attribute:ci_list' => 'ECs',
	'Class:KnownError/Attribute:ci_list+' => 'ECs',
	'Class:KnownError/Attribute:document_list' => 'Documentos',
	'Class:KnownError/Attribute:document_list+' => 'Documentos',
));

//
// Class: lnkErrorToFunctionalCI
//

Dict::Add('ES CR', 'Spanish', 'Español, Castellano', array(
	'Class:lnkErrorToFunctionalCI' => 'Relación Error Conocido y EC Funcional',
	'Class:lnkErrorToFunctionalCI+' => 'Relación Error Conocido y EC Funcional',
	'Class:lnkErrorToFunctionalCI/Attribute:functionalci_id' => 'EC',
	'Class:lnkErrorToFunctionalCI/Attribute:functionalci_id+' => 'Elemento de Configuración',
	'Class:lnkErrorToFunctionalCI/Attribute:functionalci_name' => 'CI',
	'Class:lnkErrorToFunctionalCI/Attribute:functionalci_name+' => 'Elemento de Configuración',
	'Class:lnkErrorToFunctionalCI/Attribute:error_id' => 'Error Conocido',
	'Class:lnkErrorToFunctionalCI/Attribute:error_id+' => 'Error Conocido',
	'Class:lnkErrorToFunctionalCI/Attribute:error_name' => 'Error Conocido',
	'Class:lnkErrorToFunctionalCI/Attribute:error_name+' => 'Error Conocido',
	'Class:lnkErrorToFunctionalCI/Attribute:reason' => 'Motivo',
	'Class:lnkErrorToFunctionalCI/Attribute:reason+' => 'Motivo',
));

//
// Class: lnkDocumentToError
//

Dict::Add('ES CR', 'Spanish', 'Español, Castellano', array(
	'Class:lnkDocumentToError' => 'Relación Documento y Error Conocido',
	'Class:lnkDocumentToError+' => 'Relación Documento y Error Conocido',
	'Class:lnkDocumentToError/Attribute:document_id' => 'Documento',
	'Class:lnkDocumentToError/Attribute:document_id+' => 'Documento',
	'Class:lnkDocumentToError/Attribute:document_name' => 'Documento',
	'Class:lnkDocumentToError/Attribute:document_name+' => 'Documento',
	'Class:lnkDocumentToError/Attribute:error_id' => 'Error Conocido',
	'Class:lnkDocumentToError/Attribute:error_id+' => 'Error Conocido',
	'Class:lnkDocumentToError/Attribute:error_name' => 'Error Conocido',
	'Class:lnkDocumentToError/Attribute:error_name+' => 'Error Conocido',
	'Class:lnkDocumentToError/Attribute:link_type' => 'Tipo',
	'Class:lnkDocumentToError/Attribute:link_type+' => 'Tipo',
));

//
// Class: FAQ
//

Dict::Add('ES CR', 'Spanish', 'Español, Castellano', array(
	'Class:FAQ' => 'Preguntas y Respuestas Frecuentes',
	'Class:FAQ+' => 'Preguntas y Respuestas Frecuentes',
	'Class:FAQ/Attribute:title' => 'Asunto',
	'Class:FAQ/Attribute:title+' => 'Asunto',
	'Class:FAQ/Attribute:summary' => 'Resumen',
	'Class:FAQ/Attribute:summary+' => 'Resumen',
	'Class:FAQ/Attribute:description' => 'Descripción',
	'Class:FAQ/Attribute:description+' => 'Descripción',
	'Class:FAQ/Attribute:category_id' => 'Categoría',
	'Class:FAQ/Attribute:category_id+' => 'Categoría',
	'Class:FAQ/Attribute:category_name' => 'Categoría',
	'Class:FAQ/Attribute:category_name+' => 'Categoría',
	'Class:FAQ/Attribute:error_code' => 'Código de Error',
	'Class:FAQ/Attribute:error_code+' => 'Código de Error',
	'Class:FAQ/Attribute:key_words' => 'Palabras Clave',
	'Class:FAQ/Attribute:key_words+' => 'Palabras Clave',
));

//
// Class: FAQcategory
//

Dict::Add('ES CR', 'Spanish', 'Español, Castellano', array(
	'Class:FAQCategory' => 'Categoría de Preguntas y Respuesta Frecuentes',
	'Class:FAQCategory+' => 'Categoría de Preguntas y Respuesta Frecuentes',
	'Class:FAQCategory/Attribute:name' => 'Nombre',
	'Class:FAQCategory/Attribute:name+' => 'Nombre de Categoría de Preguntas y Respuestas Frecuentes',
	'Class:FAQCategory/Attribute:faq_list' => 'FAQs',
	'Class:FAQCategory/Attribute:faq_list+' => 'FAQs',
));
Dict::Add('ES CR', 'Spanish', 'Español, Castellano', array(
	'Menu:ProblemManagement' => 'Gestión de problemas',
	'Menu:ProblemManagement+' => 'Gestión de problemas',
	'Menu:Problem:Shortcuts' => 'Acceso Rápido',
	'Menu:NewError' => 'Nuevo Error Conocido',
	'Menu:NewError+' => 'Nuevo Error Conocido',
	'Menu:SearchError' => 'Búsqueda de Errores Conocidos',
	'Menu:SearchError+' => 'Búsqueda de Errores Conocidos',
        'Menu:Problem:KnownErrors' => 'Errores Conocidos',
        'Menu:Problem:KnownErrors+' => 'Errores Conocidos',
	'Menu:FAQCategory' => 'Categorías de FAQ',
	'Menu:FAQCategory+' => 'Categorías FAQ',
	'Menu:FAQ' => 'Preguntas y Respuestas Frecuentes',
	'Menu:FAQ+' => 'Preguntas y Respuestas Frecuentes',

	'Brick:Portal:FAQ:Menu' => 'Preguntas y Respuetas',
	'Brick:Portal:FAQ:Title' => 'Preguntas y Respuestas Frecuentes',
	'Brick:Portal:FAQ:Title+' => '<p>¿En una prisa?</p><p>Vea la lista de las preguntas más comunes y encontrará (tal vez) la respuesta inmediata a sus necesidades.</p>',
));
