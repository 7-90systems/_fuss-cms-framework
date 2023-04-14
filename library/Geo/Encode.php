<?php
    /**
     *  This class is used to geo-encode an address.
     */
    
    namespace Fuse\Geo;
    
    use Fuse\Geo\Map\Point;
    
    
    class Encode {
        
        /**
         *  @var string The address that we want to encode.
         */
        protected $_address;
        
        /**
         *  @var Fuse\Geo\Map\Point The map point for the current address if set.
         */
        protected $_point;
        
        
        
        
        /**
         *  Object constructor.
         *
         *  @param string $address The address to encode.
         */
        public function __construct ($address = '') {
            $this->setAddress ($address);
        } // __construct ()
        
        
        
        
        /**
         *  Set the address. Note that this does not generate the map point now. This is only done as required.
         *
         *  @param string $address The address to set.
         *
         *  @return Fuse\Geo\Encode This object.
         */
        public function setAddress ($address) {
            $this->_address = $address;
            $this->_point = NULL;
            
            return $this;
        } // setAddress ()
        
        
        
        
        /**
         *  Get the map point for the current address.
         *
         *  @param string $address Set an address here to over-ride the current address and force point generation.
         *
         *  @return Fuse\Geo\Map\Point|false The mao point or false if an error has occured.
         */
        public function getPoint ($address = NULL) {
            $point = false;
            
            if (empty ($address) === false) {
                $this->setAddress ($address);
            } // if ()
            
            if (empty ($this->_point) === false) {
                $point = $this->_point;
            } // if ()
            else {
                $point = $this->_encode ();
            } // else
            
            return $point;
        } // getPoint ()
        
        
        
        
        /**
         *  Encode the current address to get the map point;
         *
         *  @return Fuse\Geo\Map\Point|false The mao point or false if an error has occured.
         */
        protected function _encode () {
            $point = false;
            
            $geo_key = get_fuse_option ('google_api_key', '');
            
            if (empty ($geo_key) === false) {
                // Check that we have an address est!
                if (strlen ($this->_address) > 0) {
                    $this->_point = NULL;
                    $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode ($this->_address).'&key='.urlencode ($geo_key);

                    $ch = curl_init ($url);
                    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
                    
                    $result = json_decode (curl_exec ($ch));
                    curl_close ($ch);
                    
                    if (count ($result->results) > 0) {
                        $record = $result->results [0];
                        $geometry = $record->geometry->location;
                        
                        $point = new Point ($geometry->lat, $geometry->lng);
                        $this->_point = $point;
                    } // if ()
                } // if ()
            } // if ()
            else {
                throw new \Exception (__ ('Google API key not defined.', 'fuse'));
            } // else
            
            return $point;
        } // _encode ()
        
    } // class Encode