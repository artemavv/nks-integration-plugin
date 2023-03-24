<?php

class Address {
	
		private $addressId;
    private $firstName;
    private $lastName;
    private $companyName;
    private $address1;
    private $address2;
    private $address3;
    private $city;
    private $state;
    private $zipCode;
    private $county;
    private $province;
    private $country;
    private $addressee;
    private $addressType;
		
    public function getAddressId() {
        return $this->addressId;
    }

    public function setAddressId($addressId) {
        $this->addressId = $addressId;
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

    public function getCompanyName() {
        return $this->companyName;
    }

    public function setCompanyName($companyName) {
        $this->companyName = $companyName;
    }

    public function getAddress1() {
        return $this->address1;
    }

    public function setAddress1($address1) {
        $this->address1 = $address1;
    }

    public function getAddress2() {
        return $this->address2;
    }

    public function setAddress2($address2) {
        $this->address2 = $address2;
    }
		
		public function getAddress3() {
        return $this->address3;
    }

    public function setAddress3($address3) {
        $this->address3 = $address3;
    }
		
		public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
    }
		
		public function getState() {
        return $this->state;
    }

    public function setState($state) {
        $this->state = $state;
    }
		
		public function getZipCode() {
        return $this->zipCode;
    }

    public function setZipCode($zipCode) {
        $this->zipCode = $zipCode;
    }
		
		public function getCounty() {
        return $this->county;
    }

    public function setCounty($county) {
        $this->county = $county;
    }
		
		public function getProvince() {
        return $this->province;
    }

    public function setProvince($province) {
        $this->province = $province;
    }
		
		public function getCountry() {
        return $this->country;
    }

    public function setCountry($country) {
        $this->country = $country;
    }
		
		public function getAddresse() {
        return $this->addressee;
    }

    public function setAddresse($addresse) {
        $this->addressee = $addresse;
    }

		public function getAddressType() {
        return $this->addressType;
    }

    public function setAddressType($addressType) {
        $this->addresseType = $addressType;
    }
}