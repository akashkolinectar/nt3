<?php

// Class: KnownError

Dict::Add('FR FR', 'French', 'Français', array(
	'Class:KnownError' => 'Erreur Connue',
	'Class:KnownError+' => 'Erreur documenté pour un problème connu',
	'Class:KnownError/Attribute:name' => 'Nom',
	'Class:KnownError/Attribute:name+' => '',
	'Class:KnownError/Attribute:org_id' => 'Client',
	'Class:KnownError/Attribute:org_id+' => '',
	'Class:KnownError/Attribute:cust_name' => 'Nom du client',
	'Class:KnownError/Attribute:cust_name+' => '',
	'Class:KnownError/Attribute:problem_id' => 'Problème lié',
	'Class:KnownError/Attribute:problem_id+' => '',
	'Class:KnownError/Attribute:problem_ref' => 'Rérérence problème lié',
	'Class:KnownError/Attribute:problem_ref+' => '',
	'Class:KnownError/Attribute:symptom' => 'Symptome',
	'Class:KnownError/Attribute:symptom+' => '',
	'Class:KnownError/Attribute:root_cause' => 'Cause première',
	'Class:KnownError/Attribute:root_cause+' => '',
	'Class:KnownError/Attribute:workaround' => 'Contournement',
	'Class:KnownError/Attribute:workaround+' => '',
	'Class:KnownError/Attribute:solution' => 'Solution',
	'Class:KnownError/Attribute:solution+' => '',
	'Class:KnownError/Attribute:error_code' => 'Code d\'erreur',
	'Class:KnownError/Attribute:error_code+' => '',
	'Class:KnownError/Attribute:domain' => 'Domaine',
	'Class:KnownError/Attribute:domain+' => '',
	'Class:KnownError/Attribute:domain/Value:Application' => 'Application',
	'Class:KnownError/Attribute:domain/Value:Application+' => 'Application',
	'Class:KnownError/Attribute:domain/Value:Desktop' => 'Bureautique',
	'Class:KnownError/Attribute:domain/Value:Desktop+' => 'Bureautique',
	'Class:KnownError/Attribute:domain/Value:Network' => 'Réseau',
	'Class:KnownError/Attribute:domain/Value:Network+' => 'Réseau',
	'Class:KnownError/Attribute:domain/Value:Server' => 'Serveur',
	'Class:KnownError/Attribute:domain/Value:Server+' => 'Serveur',
	'Class:KnownError/Attribute:vendor' => 'Vendeur',
	'Class:KnownError/Attribute:vendor+' => '',
	'Class:KnownError/Attribute:model' => 'Modèle',
	'Class:KnownError/Attribute:model+' => '',
	'Class:KnownError/Attribute:version' => 'Version',
	'Class:KnownError/Attribute:version+' => '',
	'Class:KnownError/Attribute:ci_list' => 'CIs',
	'Class:KnownError/Attribute:ci_list+' => '',
	'Class:KnownError/Attribute:document_list' => 'Documents',
	'Class:KnownError/Attribute:document_list+' => '',
));

//
// Class: lnkErrorToFunctionalCI
//

Dict::Add('FR FR', 'French', 'Français', array(
	'Class:lnkErrorToFunctionalCI' => 'Lien Erreur / CI',
	'Class:lnkErrorToFunctionalCI+' => 'Lien entre une erreur et un ci',
	'Class:lnkErrorToFunctionalCI/Attribute:functionalci_id' => 'CI',
	'Class:lnkErrorToFunctionalCI/Attribute:functionalci_id+' => '',
	'Class:lnkErrorToFunctionalCI/Attribute:functionalci_name' => 'Nom CI',
	'Class:lnkErrorToFunctionalCI/Attribute:functionalci_name+' => '',
	'Class:lnkErrorToFunctionalCI/Attribute:error_id' => 'Erreur',
	'Class:lnkErrorToFunctionalCI/Attribute:error_id+' => '',
	'Class:lnkErrorToFunctionalCI/Attribute:error_name' => 'Nom erreur',
	'Class:lnkErrorToFunctionalCI/Attribute:error_name+' => '',
	'Class:lnkErrorToFunctionalCI/Attribute:reason' => 'Reason',
	'Class:lnkErrorToFunctionalCI/Attribute:reason+' => '',
));

//
// Class: lnkDocumentToError
//

Dict::Add('FR FR', 'French', 'Français', array(
	'Class:lnkDocumentToError' => 'Lien Documents / Errors',
	'Class:lnkDocumentToError+' => 'Lien entre un document et une erreur',
	'Class:lnkDocumentToError/Attribute:document_id' => 'Document',
	'Class:lnkDocumentToError/Attribute:document_id+' => '',
	'Class:lnkDocumentToError/Attribute:document_name' => 'Nom Document',
	'Class:lnkDocumentToError/Attribute:document_name+' => '',
	'Class:lnkDocumentToError/Attribute:error_id' => 'Erreur',
	'Class:lnkDocumentToError/Attribute:error_id+' => '',
	'Class:lnkDocumentToError/Attribute:error_name' => 'Nom Erreur',
	'Class:lnkDocumentToError/Attribute:error_name+' => '',
	'Class:lnkDocumentToError/Attribute:link_type' => 'link_type',
	'Class:lnkDocumentToError/Attribute:link_type+' => '',
));

//
// Class: FAQ
//

Dict::Add('FR FR', 'French', 'Français', array(
	'Class:FAQ' => 'FAQ',
	'Class:FAQ+' => 'Question fréquement posée',
	'Class:FAQ/Attribute:title' => 'Titre',
	'Class:FAQ/Attribute:title+' => '',
	'Class:FAQ/Attribute:summary' => 'Résumé',
	'Class:FAQ/Attribute:summary+' => '',
	'Class:FAQ/Attribute:description' => 'Description',
	'Class:FAQ/Attribute:description+' => '',
	'Class:FAQ/Attribute:category_id' => 'Categorie',
	'Class:FAQ/Attribute:category_id+' => '',
	'Class:FAQ/Attribute:category_name' => 'Nom catégorie',
	'Class:FAQ/Attribute:category_name+' => '',
	'Class:FAQ/Attribute:error_code' => 'Code d\'erreur',
	'Class:FAQ/Attribute:error_code+' => '',
	'Class:FAQ/Attribute:key_words' => 'Mots clés',
	'Class:FAQ/Attribute:key_words+' => '',
));

//
// Class: FAQCategory
//

Dict::Add('FR FR', 'French', 'Français', array(
	'Class:FAQCategory' => 'Catégorie de FAQ',
	'Class:FAQCategory+' => 'Catégorie de FAQ',
	'Class:FAQCategory/Attribute:name' => 'Nom',
	'Class:FAQCategory/Attribute:name+' => '',
	'Class:FAQCategory/Attribute:faq_list' => 'FAQs',
	'Class:FAQCategory/Attribute:faq_list+' => '',
));

Dict::Add('FR FR', 'French', 'Français', array(
	'Menu:ProblemManagement' => 'Gestion des problèmes',
	'Menu:ProblemManagement+' => 'Gestion des problèmes',
	'Menu:Problem:Shortcuts' => 'Raccourcis',
	'Menu:NewError' => 'Nouvelle erreur connue',
	'Menu:NewError+' => 'Créer une erreur connue',
	'Menu:SearchError' => 'Rechercher une erreur connue',
	'Menu:SearchError+' => 'Rechercher une erreur connue',
	'Menu:Problem:KnownErrors' => 'Toutes les erreurs connues',
	'Menu:Problem:KnownErrors+' => 'Toutes les erreurs connues',
	'Menu:FAQCategory' => 'Catégories de FAQ',
	'Menu:FAQCategory+' => 'Toutes les catégories de FAQ',
	'Menu:FAQ' => 'FAQs',
	'Menu:FAQ+' => 'Toutes les  FAQs',

	'Brick:Portal:FAQ:Menu' => 'FAQ',
	'Brick:Portal:FAQ:Title' => 'Foire Aux Questions',
	'Brick:Portal:FAQ:Title+' => '<p>Vous êtes pressé&nbsp;?</p><p>Consultez la liste des questions les plus fréquentes et vous trouverez (peut-être) immédiatement la réponse à votre besoin.</p>',
));
