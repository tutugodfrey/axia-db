Merchant Products elements contained in this directory that are used specifically to
display merchant's products data are inserted dynamically. In order for this to work
elements must adhere to the following file naming conventions and rules:

1.- The element file name must match the product name minus spaces, periods or any other delimeters i.e.:
	Product name		=>	 ElementName.ctp (except for file extension name)
	Authorize-Product.net => AuthorizeProductNet.ctp 
	
2.- This is exactly the same as camel case except when all or part of the Product Name is in all caps:
	EBT => EBT.ctp
	EBT Authorizations => EBTAuthorizations.ctp
	Web Based ACH => WebBasedACH.ctp

