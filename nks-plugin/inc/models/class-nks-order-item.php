<?php 

class Nks_Order_Item {

		private $skuNumber;
    private $description;
    private $serialNumbers; // array( number => reference ). Both "number" and "reference" are strings, e.g. "W01F2-E434E-823E4-4KD7A-3D954" => "W0120356363002"

    private $fields = array(
        'skuNumber',
        'description'
    );

    public function __construct( $data, $serialNumbers /* must be array( number => reference ) */ ) {
        
        foreach ( $this->fields as $fieldName ) {
            if ( isset( $data[$fieldName] ) ) {
                $methodName = 'set' . ucfirst($fieldName);
                $this->$methodName( $data[$fieldName] );
            }
        }

        $this->setSerialNumbers( $serialNumbers );
    }

    public static function createItemsFromRawData( $json ) {

        $items = array();
        $data = json_decode( $json, JSON_OBJECT_AS_ARRAY );

        if ( is_array( $data ) ) {
            foreach ( $data as $row ) {
                if ( isset( $row['serialNumbers'] ) ) {
                    $items[] = new Nks_Order_Item( $row, $row['serialNumbers'] );
                }
            }
        }

        return $items;
    }
		
    public function getSkuNumber() {
        return $this->skuNumber;
    }

    public function setSkuNumber($skuNumber) {
        $this->skuNumber = $skuNumber;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getSerialNumbers() {
        return $this->serialNumbers;
    }

    public function setSerialNumbers( $serialNumbers ) {
        if ( is_array( $serialNumbers ) ) {
            foreach( $serialNumbers as $serial ) {
                $this->serialNumbers[ $serial['serialNumber'] ] = $serial['serialReference'];
            }
        }
    }
}