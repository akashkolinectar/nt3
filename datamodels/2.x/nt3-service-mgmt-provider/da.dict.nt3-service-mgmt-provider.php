<?php

Dict::Add('DA DA', 'Danish', 'Dansk', array(
	'Class:ContractType' => 'Kontrakt-Type',
	'Class:ContractType+' => '',
	'Class:CustomerContract' => 'Kundekontrakt',
	'Class:CustomerContract+' => '',
	'Class:CustomerContract/Attribute:services_list' => 'Ydelser',
	'Class:CustomerContract/Attribute:services_list+' => '',
	'Class:CustomerContract/Attribute:functionalcis_list' => 'CIs',
	'Class:CustomerContract/Attribute:functionalcis_list+' => '',
	'Class:CustomerContract/Attribute:providercontracts_list' => 'Leverandørkontrakter',
	'Class:CustomerContract/Attribute:providercontracts_list+' => '',
	'Class:ProviderContract' => 'Leverandørkontrakt',
	'Class:ProviderContract+' => '',
	'Class:ProviderContract/Attribute:functionalcis_list' => 'CIs',
	'Class:ProviderContract/Attribute:functionalcis_list+' => '',
	'Class:ProviderContract/Attribute:sla' => 'SLA',
	'Class:ProviderContract/Attribute:sla+' => '',
	'Class:ProviderContract/Attribute:coverage' => 'Servicetider',
	'Class:ProviderContract/Attribute:coverage+' => '',
	'Class:lnkContactToContract' => 'Sammenhæng Kontakt/Kontrakt',
	'Class:lnkContactToContract+' => '',
	'Class:lnkContactToContract/Attribute:contract_id' => 'Kontrakt',
	'Class:lnkContactToContract/Attribute:contract_id+' => '',
	'Class:lnkContactToContract/Attribute:contact_id' => 'Kontakt',
	'Class:lnkContactToContract/Attribute:contact_id+' => '',
	'Class:lnkContractToDocument' => 'Sammenhæng Kontrakt/Dokument',
	'Class:lnkContractToDocument+' => '',
	'Class:lnkContractToDocument/Attribute:contract_id' => 'Kontrakt',
	'Class:lnkContractToDocument/Attribute:contract_id+' => '',
	'Class:lnkContractToDocument/Attribute:document_id' => 'Dokument',
	'Class:lnkContractToDocument/Attribute:document_id+' => '',
	'Class:lnkFunctionalCnt3roviderContract' => 'Sammenhæng FunctionalCI/Leverandørkontrakt',
	'Class:lnkFunctionalCnt3roviderContract+' => '',
	'Class:lnkFunctionalCnt3roviderContract/Attribute:providercontract_id' => 'Leverandørkontrakt',
	'Class:lnkFunctionalCnt3roviderContract/Attribute:providercontract_id+' => '',
	'Class:lnkFunctionalCnt3roviderContract/Attribute:functionalci_id' => 'CI',
	'Class:lnkFunctionalCnt3roviderContract/Attribute:functionalci_id+' => '',
	'Class:ServiceFamily' => 'Ydelsesfamilie',
	'Class:ServiceFamily+' => '',
	'Class:ServiceFamily/Attribute:name' => 'Navn',
	'Class:ServiceFamily/Attribute:name+' => '',
	'Class:ServiceFamily/Attribute:icon' => 'Icon~~',
	'Class:ServiceFamily/Attribute:icon+' => '',
	'Class:ServiceFamily/Attribute:services_list' => 'Ydelser',
	'Class:ServiceFamily/Attribute:services_list+' => '',
	'Class:Service' => 'Ydelse',
	'Class:Service+' => '',
	'Class:Service/Attribute:name' => 'Navn',
	'Class:Service/Attribute:name+' => '',
	'Class:Service/Attribute:org_id' => 'Organisation',
	'Class:Service/Attribute:org_id+' => '',
	'Class:Service/Attribute:description' => 'Beskrivelse',
	'Class:Service/Attribute:description+' => '',
	'Class:Service/Attribute:documents_list' => 'Dokument',
	'Class:Service/Attribute:documents_list+' => '',
	'Class:Service/Attribute:contacts_list' => 'Kontakt',
	'Class:Service/Attribute:contacts_list+' => '',
	'Class:Service/Attribute:status' => 'Status',
	'Class:Service/Attribute:status+' => '',
	'Class:Service/Attribute:status/Value:implementation' => 'Implementering',
	'Class:Service/Attribute:status/Value:implementation+' => '',
	'Class:Service/Attribute:status/Value:obsolete' => 'Forældet',
	'Class:Service/Attribute:status/Value:obsolete+' => '',
	'Class:Service/Attribute:status/Value:production' => 'Produktion',
	'Class:Service/Attribute:status/Value:production+' => '',
	'Class:Service/Attribute:icon' => 'Icon~~',
	'Class:Service/Attribute:icon+' => '',
	'Class:Service/Attribute:customercontracts_list' => 'Kundekontrakt',
	'Class:Service/Attribute:customercontracts_list+' => '',
	'Class:Service/Attribute:servicesubcategories_list' => 'Ydelses underkategorier',
	'Class:Service/Attribute:servicesubcategories_list+' => '',
	'Class:lnkDocumentToService' => 'Sammenhæng Dokument/Ydelse',
	'Class:lnkDocumentToService+' => '',
	'Class:lnkDocumentToService/Attribute:service_id' => 'Ydelse',
	'Class:lnkDocumentToService/Attribute:service_id+' => '',
	'Class:lnkDocumentToService/Attribute:document_id' => 'Dokument',
	'Class:lnkDocumentToService/Attribute:document_id+' => '',
	'Class:lnkContactToService' => 'Sammenhæng Kontakt/Ydelse',
	'Class:lnkContactToService+' => '',
	'Class:lnkContactToService/Attribute:service_id' => 'Ydelse',
	'Class:lnkContactToService/Attribute:service_id+' => '',
	'Class:lnkContactToService/Attribute:contact_id' => 'Kontakt',
	'Class:lnkContactToService/Attribute:contact_id+' => '',
	'Class:ServiceSubcategory' => 'Ydelses underkategori',
	'Class:ServiceSubcategory+' => '',
	'Class:ServiceSubcategory/Attribute:name' => 'Navn',
	'Class:ServiceSubcategory/Attribute:name+' => '',
	'Class:ServiceSubcategory/Attribute:description' => 'Beskrivelse',
	'Class:ServiceSubcategory/Attribute:description+' => '',
	'Class:ServiceSubcategory/Attribute:service_id' => 'Ydelse',
	'Class:ServiceSubcategory/Attribute:service_id+' => '',
	'Class:ServiceSubcategory/Attribute:request_type' => 'Anmodnings type',
	'Class:ServiceSubcategory/Attribute:request_type+' => '',
	'Class:ServiceSubcategory/Attribute:request_type/Value:incident' => 'Incident',
	'Class:ServiceSubcategory/Attribute:request_type/Value:incident+' => '',
	'Class:ServiceSubcategory/Attribute:request_type/Value:service_request' => 'Service Anmodning',
	'Class:ServiceSubcategory/Attribute:request_type/Value:service_request+' => '',
	'Class:ServiceSubcategory/Attribute:status' => 'Status',
	'Class:ServiceSubcategory/Attribute:status+' => '',
	'Class:ServiceSubcategory/Attribute:status/Value:implementation' => 'Implementering',
	'Class:ServiceSubcategory/Attribute:status/Value:implementation+' => '',
	'Class:ServiceSubcategory/Attribute:status/Value:obsolete' => 'Forældet',
	'Class:ServiceSubcategory/Attribute:status/Value:obsolete+' => '',
	'Class:ServiceSubcategory/Attribute:status/Value:production' => 'Produktion',
	'Class:ServiceSubcategory/Attribute:status/Value:production+' => '',
	'Class:SLA' => 'SLA',
	'Class:SLA+' => '',
	'Class:SLA/Attribute:name' => 'Navn',
	'Class:SLA/Attribute:name+' => '',
	'Class:SLA/Attribute:description' => 'Beskrivelse',
	'Class:SLA/Attribute:description+' => '',
	'Class:SLA/Attribute:org_id' => 'Organisation',
	'Class:SLA/Attribute:org_id+' => '',
	'Class:SLA/Attribute:slts_list' => 'SLTs',
	'Class:SLA/Attribute:slts_list+' => '',
	'Class:SLA/Attribute:customercontracts_list' => 'Kundekontrakt',
	'Class:SLA/Attribute:customercontracts_list+' => '',
	'Class:SLT' => 'SLT',
	'Class:SLT+' => '',
	'Class:SLT/Attribute:name' => 'Navn',
	'Class:SLT/Attribute:name+' => '',
	'Class:SLT/Attribute:priority' => 'Prioritet',
	'Class:SLT/Attribute:priority+' => '',
	'Class:SLT/Attribute:priority/Value:1' => 'Kritisk',
	'Class:SLT/Attribute:priority/Value:1+' => '',
	'Class:SLT/Attribute:priority/Value:2' => 'Høj',
	'Class:SLT/Attribute:priority/Value:2+' => '',
	'Class:SLT/Attribute:priority/Value:3' => 'Middel',
	'Class:SLT/Attribute:priority/Value:3+' => '',
	'Class:SLT/Attribute:priority/Value:4' => 'Lav',
	'Class:SLT/Attribute:priority/Value:4+' => '',
	'Class:SLT/Attribute:request_type' => 'Anmodnings type',
	'Class:SLT/Attribute:request_type+' => '',
	'Class:SLT/Attribute:request_type/Value:incident' => 'Incident',
	'Class:SLT/Attribute:request_type/Value:incident+' => '',
	'Class:SLT/Attribute:request_type/Value:service_request' => 'Service Anmodning',
	'Class:SLT/Attribute:request_type/Value:service_request+' => '',
	'Class:SLT/Attribute:metric' => 'Metrisk',
	'Class:SLT/Attribute:metric+' => '',
	'Class:SLT/Attribute:metric/Value:tto' => 'TTO (Time To Own)',
	'Class:SLT/Attribute:metric/Value:tto+' => '',
	'Class:SLT/Attribute:metric/Value:ttr' => 'TTR (Time To Resolve)',
	'Class:SLT/Attribute:metric/Value:ttr+' => '',
	'Class:SLT/Attribute:value' => 'Værdi',
	'Class:SLT/Attribute:value+' => '',
	'Class:SLT/Attribute:unit' => 'Enhed',
	'Class:SLT/Attribute:unit+' => '',
	'Class:SLT/Attribute:unit/Value:hours' => 'Timer',
	'Class:SLT/Attribute:unit/Value:hours+' => '',
	'Class:SLT/Attribute:unit/Value:minutes' => 'Minutter',
	'Class:SLT/Attribute:unit/Value:minutes+' => '',
	'Class:lnkSLAToSLT' => 'Sammenhæng SLA/SLT',
	'Class:lnkSLAToSLT+' => '',
	'Class:lnkSLAToSLT/Attribute:sla_id' => 'SLA',
	'Class:lnkSLAToSLT/Attribute:sla_id+' => '',
	'Class:lnkSLAToSLT/Attribute:slt_id' => 'SLT',
	'Class:lnkSLAToSLT/Attribute:slt_id+' => '',
	'Class:lnkCustomerContractToService' => 'Sammenhæng Kundekontrakt/Ydelse',
	'Class:lnkCustomerContractToService+' => '',
	'Class:lnkCustomerContractToService/Attribute:customercontract_id' => 'Kundekontrakt',
	'Class:lnkCustomerContractToService/Attribute:customercontract_id+' => '',
	'Class:lnkCustomerContractToService/Attribute:service_id' => 'Ydelse',
	'Class:lnkCustomerContractToService/Attribute:service_id+' => '',
	'Class:lnkCustomerContractToService/Attribute:sla_id' => 'SLA',
	'Class:lnkCustomerContractToService/Attribute:sla_id+' => '',
	'Class:lnkCustomerContractToProviderContract' => 'Sammenhæng Kundekontrakt/Leverandørkontrakt',
	'Class:lnkCustomerContractToProviderContract+' => '',
	'Class:lnkCustomerContractToProviderContract/Attribute:customercontract_id' => 'Kundekontrakt',
	'Class:lnkCustomerContractToProviderContract/Attribute:customercontract_id+' => '',
	'Class:lnkCustomerContractToProviderContract/Attribute:providercontract_id' => 'Leverandørkontrakt',
	'Class:lnkCustomerContractToProviderContract/Attribute:providercontract_id+' => '',
	'Class:lnkCustomerContractToFunctionalCI' => 'Sammenhæng Kundekontrakt/FunctionalCI',
	'Class:lnkCustomerContractToFunctionalCI+' => '',
	'Class:lnkCustomerContractToFunctionalCI/Attribute:customercontract_id' => 'Kundekontrakt',
	'Class:lnkCustomerContractToFunctionalCI/Attribute:customercontract_id+' => '',
	'Class:lnkCustomerContractToFunctionalCI/Attribute:functionalci_id' => 'CI',
	'Class:lnkCustomerContractToFunctionalCI/Attribute:functionalci_id+' => '',
	'Class:DeliveryModel' => 'Leverings model',
	'Class:DeliveryModel+' => '',
	'Class:DeliveryModel/Attribute:name' => 'Navn',
	'Class:DeliveryModel/Attribute:name+' => '',
	'Class:DeliveryModel/Attribute:org_id' => 'Organisation',
	'Class:DeliveryModel/Attribute:org_id+' => '',
	'Class:DeliveryModel/Attribute:description' => 'Beskrivelse',
	'Class:DeliveryModel/Attribute:description+' => '',
	'Class:DeliveryModel/Attribute:contacts_list' => 'Kontakt',
	'Class:DeliveryModel/Attribute:contacts_list+' => '',
	'Class:DeliveryModel/Attribute:customers_list' => 'Kunde',
	'Class:DeliveryModel/Attribute:customers_list+' => '',
	'Class:lnkDeliveryModelToContact' => 'Sammenhæng Leveringsmodel/Kontakt',
	'Class:lnkDeliveryModelToContact+' => '',
	'Class:lnkDeliveryModelToContact/Attribute:deliverymodel_id' => 'Leverings model',
	'Class:lnkDeliveryModelToContact/Attribute:deliverymodel_id+' => '',
	'Class:lnkDeliveryModelToContact/Attribute:contact_id' => 'Kontakt',
	'Class:lnkDeliveryModelToContact/Attribute:contact_id+' => '',
	'Class:lnkDeliveryModelToContact/Attribute:role_id' => 'Rolle',
	'Class:lnkDeliveryModelToContact/Attribute:role_id+' => '',
	'Menu:ServiceManagement' => 'Service-Management',
	'Menu:ServiceManagement+' => '',
	'Menu:Service:Overview' => 'Oversigt',
	'Menu:Service:Overview+' => '',
	'UI-ServiceManagementMenu-ContractsBySrvLevel' => 'Kontrakter efter Service Level',
	'UI-ServiceManagementMenu-ContractsByStatus' => 'Kontrakter efter Status',
	'UI-ServiceManagementMenu-ContractsEndingIn30Days' => 'Kontrakter som udløber om mindre end 30 dage',
	'Menu:ProviderContract' => 'Leverandørkontrakter',
	'Menu:ProviderContract+' => '',
	'Menu:CustomerContract' => 'Kundekontrakter',
	'Menu:CustomerContract+' => '',
	'Menu:ServiceSubcategory' => 'Ydelses underkategori',
	'Menu:ServiceSubcategory+' => '',
	'Menu:Service' => 'Ydelser',
	'Menu:Service+' => '',
	'Menu:ServiceElement' => 'Ydelses elementer',
	'Menu:ServiceElement+' => '',
	'Menu:SLA' => 'SLAs',
	'Menu:SLA+' => '',
	'Menu:SLT' => 'SLTs',
	'Menu:SLT+' => '',
	'Menu:DeliveryModel' => 'Leveringsmodel',
	'Menu:DeliveryModel+' => '',
	'Class:Contract' => 'Kontrakt',
	'Class:Contract+' => '',
	'Class:Contract/Attribute:name' => 'Navn',
	'Class:Contract/Attribute:name+' => '',
	'Class:Contract/Attribute:org_id' => 'Organisation',
	'Class:Contract/Attribute:org_id+' => '',
	'Class:Contract/Attribute:organization_name' => 'Organisations navn',
	'Class:Contract/Attribute:organization_name+' => '',
	'Class:Contract/Attribute:contacts_list' => 'Kontakter',
	'Class:Contract/Attribute:contacts_list+' => '',
	'Class:Contract/Attribute:documents_list' => 'Dokumenter',
	'Class:Contract/Attribute:documents_list+' => '',
	'Class:Contract/Attribute:description' => 'Beskrivelse',
	'Class:Contract/Attribute:description+' => '',
	'Class:Contract/Attribute:start_date' => 'Startdato',
	'Class:Contract/Attribute:start_date+' => '',
	'Class:Contract/Attribute:end_date' => 'Slutdato',
	'Class:Contract/Attribute:end_date+' => '',
	'Class:Contract/Attribute:cost' => 'Omkostninger',
	'Class:Contract/Attribute:cost+' => '',
	'Class:Contract/Attribute:cost_currency' => 'Valuta',
	'Class:Contract/Attribute:cost_currency+' => '',
	'Class:Contract/Attribute:cost_currency/Value:dollars' => 'Dollar',
	'Class:Contract/Attribute:cost_currency/Value:dollars+' => '',
	'Class:Contract/Attribute:cost_currency/Value:euros' => 'Euro',
	'Class:Contract/Attribute:cost_currency/Value:euros+' => '',
	'Class:Contract/Attribute:cost_currency/Value:kroner' => 'Kroner',
	'Class:Contract/Attribute:cost_currency/Value:kroner+' => 'Danske kroner',
	'Class:Contract/Attribute:contracttype_id' => 'Kontrakttype',
	'Class:Contract/Attribute:contracttype_id+' => '',
	'Class:Contract/Attribute:contracttype_name' => 'Kontrakt type navn',
	'Class:Contract/Attribute:contracttype_name+' => '',
	'Class:Contract/Attribute:billing_frequency' => 'Afregnings frekvens',
	'Class:Contract/Attribute:billing_frequency+' => '',
	'Class:Contract/Attribute:cost_unit' => 'Enhedsomkostninger',
	'Class:Contract/Attribute:cost_unit+' => '',
	'Class:Contract/Attribute:provider_id' => 'Leverandør',
	'Class:Contract/Attribute:provider_id+' => '',
	'Class:Contract/Attribute:provider_name' => 'Leverandør navn',
	'Class:Contract/Attribute:provider_name+' => '',
	'Class:Contract/Attribute:status' => 'Status',
	'Class:Contract/Attribute:status+' => '',
	'Class:Contract/Attribute:status/Value:implementation' => 'Implementering',
	'Class:Contract/Attribute:status/Value:implementation+' => '',
	'Class:Contract/Attribute:status/Value:obsolete' => 'Forældet',
	'Class:Contract/Attribute:status/Value:obsolete+' => '',
	'Class:Contract/Attribute:status/Value:production' => 'Produktion',
	'Class:Contract/Attribute:status/Value:production+' => '',
	'Class:Contract/Attribute:finalclass' => 'Kontrakttype',
	'Class:Contract/Attribute:finalclass+' => '',
	'Class:lnkContactToContract/Attribute:contract_name' => 'Kontrakt navn',
	'Class:lnkContactToContract/Attribute:contract_name+' => '',
	'Class:lnkContactToContract/Attribute:contact_name' => 'Kontakt navn',
	'Class:lnkContactToContract/Attribute:contact_name+' => '',
	'Class:lnkContractToDocument/Attribute:contract_name' => 'Kontrakt navn',
	'Class:lnkContractToDocument/Attribute:contract_name+' => '',
	'Class:lnkContractToDocument/Attribute:document_name' => 'Dokument navn',
	'Class:lnkContractToDocument/Attribute:document_name+' => '',
	'Class:lnkFunctionalCnt3roviderContract/Attribute:providercontract_name' => 'Leverandørkontrakt navn',
	'Class:lnkFunctionalCnt3roviderContract/Attribute:providercontract_name+' => '',
	'Class:lnkFunctionalCnt3roviderContract/Attribute:functionalci_name' => 'CI navn',
	'Class:lnkFunctionalCnt3roviderContract/Attribute:functionalci_name+' => '',
	'Class:Service/Attribute:organization_name' => 'Navn',
	'Class:Service/Attribute:organization_name+' => '',
	'Class:lnkDocumentToService/Attribute:service_name' => 'Ydelses navn',
	'Class:lnkDocumentToService/Attribute:service_name+' => '',
	'Class:lnkDocumentToService/Attribute:document_name' => 'Dokument navn',
	'Class:lnkDocumentToService/Attribute:document_name+' => '',
	'Class:lnkContactToService/Attribute:service_name' => 'Ydelses navn',
	'Class:lnkContactToService/Attribute:service_name+' => '',
	'Class:lnkContactToService/Attribute:contact_name' => 'Kontakt navn',
	'Class:lnkContactToService/Attribute:contact_name+' => '',
	'Class:ServiceSubcategory/Attribute:service_name' => 'Ydelses navn',
	'Class:ServiceSubcategory/Attribute:service_name+' => '',
	'Class:SLA/Attribute:organization_name' => 'Organisations navn',
	'Class:SLA/Attribute:organization_name+' => '',
	'Class:lnkSLAToSLT/Attribute:sla_name' => 'SLA navn',
	'Class:lnkSLAToSLT/Attribute:sla_name+' => '',
	'Class:lnkSLAToSLT/Attribute:slt_name' => 'SLT navn',
	'Class:lnkSLAToSLT/Attribute:slt_name+' => '',
	'Class:lnkCustomerContractToService/Attribute:customercontract_name' => 'Kundekontrakt navn',
	'Class:lnkCustomerContractToService/Attribute:customercontract_name+' => '',
	'Class:lnkCustomerContractToService/Attribute:service_name' => 'Ydelses navn',
	'Class:lnkCustomerContractToService/Attribute:service_name+' => '',
	'Class:lnkCustomerContractToService/Attribute:sla_name' => 'SLA-Navn',
	'Class:lnkCustomerContractToService/Attribute:sla_name+' => '',
	'Class:lnkCustomerContractToProviderContract/Attribute:customercontract_name' => 'Kundekontrakt navn',
	'Class:lnkCustomerContractToProviderContract/Attribute:customercontract_name+' => '',
	'Class:lnkCustomerContractToProviderContract/Attribute:providercontract_name' => 'Leverandørkontrakt navn',
	'Class:lnkCustomerContractToProviderContract/Attribute:providercontract_name+' => '',
	'Class:lnkCustomerContractToFunctionalCI/Attribute:customercontract_name' => 'Kundekontrakt navn',
	'Class:lnkCustomerContractToFunctionalCI/Attribute:customercontract_name+' => '',
	'Class:lnkCustomerContractToFunctionalCI/Attribute:functionalci_name' => 'CI navn',
	'Class:lnkCustomerContractToFunctionalCI/Attribute:functionalci_name+' => '',
	'Class:DeliveryModel/Attribute:organization_name' => 'Organisations navn',
	'Class:DeliveryModel/Attribute:organization_name+' => '',
	'Class:lnkDeliveryModelToContact/Attribute:deliverymodel_name' => 'Leveringsmodel navn',
	'Class:lnkDeliveryModelToContact/Attribute:deliverymodel_name+' => '',
	'Class:lnkDeliveryModelToContact/Attribute:contact_name' => 'Kontaktnavn',
	'Class:lnkDeliveryModelToContact/Attribute:contact_name+' => '',
	'Class:lnkDeliveryModelToContact/Attribute:role_name' => 'Rolle navn',
	'Class:lnkDeliveryModelToContact/Attribute:role_name+' => '',
	'Class:Organization/Attribute:deliverymodel_id' => 'Leverings model',
	'Class:Organization/Attribute:deliverymodel_name' => 'Leverings model navn',
	'Class:ServiceSubcategory/Attribute:service_provider' => 'Provider Name~~',
	'Class:ServiceSubcategory/Attribute:service_org_id' => 'Provider~~',
));
?>