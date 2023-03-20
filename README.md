# nks-integration-plugin
Wordpress plugin allowing to receive data from NKS API. 

See enclosed _NKS_Integration_Requirements.pdf_  for integration details.

* **nks-plugin** folder containds plugins itself.
* **nks-api** folder contains endpoints for external service to send data to.


To set up NKS endpoint, follow these steps:
* place _nks-api_ folder somewhere where is is accessible by HTTPS
* define username and password for HTTP Basic Auth in _nks-api/requestManager.php_ , method **authenticate()**
* define DB connection parameters in _nks-api/requestManager.php_ ( start of the file )
* send some customer info to the endpoint, in the JSON format (explained in _NKS_Integration_Requirements.pdf_ ). 

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

To install Wordpress plugin, follow these steps:
* place _nks-plugin_ folder into _wp-content/plugins_ folder
* activate this plugin in Wordpress admin panel
* go to **Tools** -> NKS Customers and check that you see here a list of customers from the DB.