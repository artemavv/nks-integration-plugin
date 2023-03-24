<?php

require_once( "includes.php");

$req_manager = new Nks_Integration\RequestManager();

if ( $req_manager->authenticate() ) {
	
	// If arrives here, is a valid user.
	$req_manager->processCustomerRequest();
}


/* example JSON: {
"source" : "Native Instruments Webshop",
"customerDetails" : {
"sdbsUserId" : "3605000",
"business" : false,
"firstName" : "German55555",
"lastName" : "Customer1",
"emailAddress" : "german_customer1@s.ni.de",
"phoneNumber" : "030 3480324803",
"language" : "en_US",
"vatId" : null,
"category" : "DIRECT SALES"
},
"addresses" : {
"address" : [ {
"location" : {
"firstName" : "German",
"lastName" : "Customer1",
"companyName" : null,
"address1" : "Deutsche Str. 1",
"address2" : null,
"address3" : null,
"city" : "Berlin",
"state" : null,
"zipCode" : "10244",
"county" : null,
"province" : null,
"country" : "DE",
"addressee" : null,
"addressType" : "BILL_TO"
}
}, {
"location" : {
"firstName" : "German",
"lastName" : "Customer1",
"companyName" : null,"address1" : "Deutsche Str. 1",
"address2" : null,
"address3" : null,
"city" : "Berlin",
"state" : null,
"zipCode" : "10244",
"county" : null,
"province" : null,
"country" : "DE",
"addressee" : null,
"addressType" : "SHIP_TO"
}
} ]
}
}

*/
