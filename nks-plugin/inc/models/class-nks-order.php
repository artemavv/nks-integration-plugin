<?php

class Nks_Order {
	
	private $orderId;
    private $customerEmail;
    private $firstName;
    private $lastName;
    private $language;
    private $customerReference;
    private $items; // array

    private $fields = array(
        'orderId',
        'customerEmail',
        'firstName',
        'lastName',
        'language',
        'customerReference',
    );
    
    public function __construct( $data, $items /* must be array of Nks_Order_Item */) {
        
        foreach ( $this->fields as $fieldName ) {
            if ( isset( $data[$fieldName] ) ) {
                $methodName = 'set' . ucfirst($fieldName);
                $this->$methodName( $data[$fieldName] );
            }
        }

        $this->setItems( $items );
    }
		
		
    public function getId() {
        return $this->orderId;
    }
		
    public function getOrderId() {
        return $this->orderId;
    }

    public function setOrderId($orderId) {
        $this->orderId = $orderId;
    }

    public function getCustomerEmail() {
        return $this->customerEmail;
    }

    public function setCustomerEmail($customerEmail) {
        $this->customerEmail = $customerEmail;
    }
		
    public function getFirstName() {
        return $this->firstName;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($language) {
        $this->language = $language;
    }

    public function getCustomerReference() {
        return $this->customerReference;
    }

    public function setCustomerReference($customerReference) {
        $this->customerReference = $customerReference;
    }
		
	public function getItems() {
        return $this->items; // returns array of Nks_Order_Item
    }

    public function setItems( $items ) { // must be array of Nks_Order_Item
        $this->items = $items;
    }
}