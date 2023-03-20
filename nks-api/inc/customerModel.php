<?php

class Customer {
	
    private $sdbsUserId;
    private $business;
    private $firstName;
    private $lastName;
    private $emailAddress;
    private $phoneNumber;
    private $language;
    private $vatId;
    private $category;
    private $addressIds = array();

    public function getSdbsUserId() {
        return $this->sdbsUserId;
    }

    public function setSdbsUserId($sdbsUserId) {
        $this->sdbsUserId = $sdbsUserId;
    }

    public function isBusiness() {
        return (bool) $this->business;
    }

    public function setBusiness($business) {
        $this->business = (bool) $business;
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

    public function getEmailAddress() {
        return $this->emailAddress;
    }

    public function setEmailAddress($emailAddress) {
        $this->emailAddress = $emailAddress;
    }

    public function getPhoneNumber() {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($language) {
        $this->language = $language;
    }

    public function getVatId() {
        return $this->vatId;
    }

    public function setVatId($vatId) {
        $this->vatId = $vatId;
    }

    public function getCategory() {
        return $this->category;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function getAddressIds() {
        return $this->addressIds;
    }

    public function addAddressId($addressId) {
        $this->addressIds[] = $addressId;
    }
}
