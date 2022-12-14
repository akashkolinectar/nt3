<?php

Dict::Add('HU HU', 'Hungarian', 'Magyar', array(
	'Class:KnownError' => 'Ismert hiba',
	'Class:KnownError+' => '',
	'Class:KnownError/Attribute:name' => 'Név',
	'Class:KnownError/Attribute:name+' => '',
	'Class:KnownError/Attribute:org_id' => 'Ügyfél',
	'Class:KnownError/Attribute:org_id+' => '',
	'Class:KnownError/Attribute:problem_id' => 'Kapcsolódó probléma',
	'Class:KnownError/Attribute:problem_id+' => '',
	'Class:KnownError/Attribute:symptom' => 'Jelenség',
	'Class:KnownError/Attribute:symptom+' => '',
	'Class:KnownError/Attribute:root_cause' => 'Gyökérok',
	'Class:KnownError/Attribute:root_cause+' => '',
	'Class:KnownError/Attribute:workaround' => 'Elkerülő megoldás',
	'Class:KnownError/Attribute:workaround+' => '',
	'Class:KnownError/Attribute:solution' => 'Megoldás',
	'Class:KnownError/Attribute:solution+' => '',
	'Class:KnownError/Attribute:error_code' => 'Hibakód',
	'Class:KnownError/Attribute:error_code+' => '',
	'Class:KnownError/Attribute:domain' => 'Hiba behatárolás',
	'Class:KnownError/Attribute:domain+' => '',
	'Class:KnownError/Attribute:domain/Value:Application' => 'Alkalmazás',
	'Class:KnownError/Attribute:domain/Value:Application+' => '',
	'Class:KnownError/Attribute:domain/Value:Desktop' => 'Desktop',
	'Class:KnownError/Attribute:domain/Value:Desktop+' => '',
	'Class:KnownError/Attribute:domain/Value:Network' => 'Hálózat',
	'Class:KnownError/Attribute:domain/Value:Network+' => '',
	'Class:KnownError/Attribute:domain/Value:Server' => 'Szerver',
	'Class:KnownError/Attribute:domain/Value:Server+' => '',
	'Class:KnownError/Attribute:vendor' => 'Szállító',
	'Class:KnownError/Attribute:vendor+' => '',
	'Class:KnownError/Attribute:model' => 'Model',
	'Class:KnownError/Attribute:model+' => '',
	'Class:KnownError/Attribute:version' => 'Verzió',
	'Class:KnownError/Attribute:version+' => '',
	'Class:KnownError/Attribute:ci_list' => 'CI-k',
	'Class:KnownError/Attribute:ci_list+' => '',
	'Class:KnownError/Attribute:document_list' => 'Dokumentumok',
	'Class:KnownError/Attribute:document_list+' => '',
	'Class:lnkInfraError' => 'Infrastruktúra problémák',
	'Class:lnkInfraError+' => '',
	'Class:lnkInfraError/Attribute:infra_id' => 'CI',
	'Class:lnkInfraError/Attribute:infra_id+' => '',
	'Class:lnkInfraError/Attribute:error_id' => 'Hiba',
	'Class:lnkInfraError/Attribute:error_id+' => '',
	'Class:lnkInfraError/Attribute:reason' => 'Ok',
	'Class:lnkInfraError/Attribute:reason+' => '',
	'Class:lnkDocumentError' => 'Dokumentum problémák',
	'Class:lnkDocumentError+' => '',
	'Class:lnkDocumentError/Attribute:doc_id' => 'Dokumentum',
	'Class:lnkDocumentError/Attribute:doc_id+' => '',
	'Class:lnkDocumentError/Attribute:error_id' => 'Hiba',
	'Class:lnkDocumentError/Attribute:error_id+' => '',
	'Class:lnkDocumentError/Attribute:link_type' => 'Információ',
	'Class:lnkDocumentError/Attribute:link_type+' => '',
	'Class:KnownError/Attribute:cust_name' => 'Ügyfél neve',
	'Class:KnownError/Attribute:cust_name+' => '',
	'Class:KnownError/Attribute:problem_ref' => 'Referencia',
	'Class:KnownError/Attribute:problem_ref+' => '',
	'Class:lnkInfraError/Attribute:infra_name' => 'CI neve',
	'Class:lnkInfraError/Attribute:infra_name+' => '',
	'Class:lnkInfraError/Attribute:infra_status' => 'CI státusz',
	'Class:lnkInfraError/Attribute:infra_status+' => '',
	'Class:lnkInfraError/Attribute:error_name' => 'Hiba megnevezése',
	'Class:lnkInfraError/Attribute:error_name+' => '',
	'Class:lnkDocumentError/Attribute:doc_name' => 'Dokumentum neve',
	'Class:lnkDocumentError/Attribute:doc_name+' => '',
	'Class:lnkDocumentError/Attribute:error_name' => 'Hiba megnevezése',
	'Class:lnkDocumentError/Attribute:error_name+' => '',
	'Menu:ProblemManagement' => 'Probléma menedzsment',
	'Menu:ProblemManagement+' => '',
	'Menu:Problem:Shortcuts' => 'Gyorsmenü',
	'Menu:NewError' => 'Új ismert hiba',
	'Menu:NewError+' => '',
	'Menu:SearchError' => 'Ismert hiba keresés',
	'Menu:SearchError+' => '',
	'Menu:Problem:KnownErrors' => 'Összes ismert hiba',
	'Menu:Problem:KnownErrors+' => '',
	'Class:lnkErrorToFunctionalCI' => 'Link Error / FunctionalCI~~',
	'Class:lnkErrorToFunctionalCI+' => 'Infra related to a known error~~',
	'Class:lnkErrorToFunctionalCI/Attribute:functionalci_id' => 'CI~~',
	'Class:lnkErrorToFunctionalCI/Attribute:functionalci_name' => 'CI name~~',
	'Class:lnkErrorToFunctionalCI/Attribute:error_id' => 'Error~~',
	'Class:lnkErrorToFunctionalCI/Attribute:error_name' => 'Error name~~',
	'Class:lnkErrorToFunctionalCI/Attribute:reason' => 'Reason~~',
	'Class:lnkDocumentToError' => 'Link Documents / Errors~~',
	'Class:lnkDocumentToError+' => 'A link between a document and a known error~~',
	'Class:lnkDocumentToError/Attribute:document_id' => 'Document~~',
	'Class:lnkDocumentToError/Attribute:document_name' => 'Document Name~~',
	'Class:lnkDocumentToError/Attribute:error_id' => 'Error~~',
	'Class:lnkDocumentToError/Attribute:error_name' => 'Error name~~',
	'Class:lnkDocumentToError/Attribute:link_type' => 'link_type~~',
	'Class:FAQ' => 'FAQ~~',
	'Class:FAQ+' => 'Frequently asked questions~~',
	'Class:FAQ/Attribute:title' => 'Title~~',
	'Class:FAQ/Attribute:summary' => 'Summary~~',
	'Class:FAQ/Attribute:description' => 'Description~~',
	'Class:FAQ/Attribute:category_id' => 'Category~~',
	'Class:FAQ/Attribute:category_name' => 'Category name~~',
	'Class:FAQ/Attribute:error_code' => 'Error code~~',
	'Class:FAQ/Attribute:key_words' => 'Key words~~',
	'Class:FAQCategory' => 'FAQ Category~~',
	'Class:FAQCategory+' => 'Category for FAQ~~',
	'Class:FAQCategory/Attribute:name' => 'Name~~',
	'Class:FAQCategory/Attribute:faq_list' => 'FAQs~~',
	'Class:FAQCategory/Attribute:faq_list+' => 'All the frequently asked questions related to this category~~',
	'Menu:FAQCategory' => 'FAQ categories~~',
	'Menu:FAQCategory+' => 'All FAQ categories~~',
	'Menu:FAQ' => 'FAQs~~',
	'Menu:FAQ+' => 'All FAQs~~',
));
?>