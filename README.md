# nks-integration-plugin
Wordpress plugin allowing to receive data from NKS API via "Order" endpoint and "Customer" endpoint.

See enclosed _NKS_Integration_Requirements.pdf_  for integration details.

* **nks-plugin** folder contains the Wordpress plugin.
* **nks-plugin/nks-endpoints/** folder contains endpoints for external service to send data to.
* there are two built-in endpoints: _nks-plugin/nks-endpoints/order-endpoint.php_ and _nks-plugin/nks-endpoints/customer-endpoint.php_ . 

To set up NKS endpoint, follow these steps:
* install nks-plugin as standard Wordpress plugin ( place _nks-plugin_ folder into _wp-content/plugins_ folder and activate this plugin in Wordpress admin panel )
* define username and password for HTTP Basic Auth in _nks-plugin/nks-endpoints/inc/requestManager.php_ , method **authenticate()**
* send some customer info to the Customer endpoint, in the JSON format (explained in _NKS_Integration_Requirements.pdf_ ). 
* go to **Tools** -> NKS Customers and check that you see here a list of customers from the DB.

Here is an example of CURL request to create a test customer entry ( change here endpoint URL and auth string ): 

`curl --location 'https://your.site/nks-api/customer-endpoint.php' \
--header 'Authorization: Basic RDVdfgfg^VSsvserwe=' \
--header 'Content-Type: text/plain' \
--data-raw '{
"source" : "Native Instruments Webshop",
"customerDetails" : {
"sdbsUserId" : "3605000",
"business" : false,
"firstName" : "Frank100",
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
}'`


