<?php
    /**
     *  This  class represents a map point.
     */
    
    namespace Fuse\Geo\Map;
    
    
    class Point {
        
        /**
         *  @var float The latitude value
         */
        protected $_lat;
        
        /**
         *  @var float The longitude value
         */
        protected $_lng;
        
        /**
         *  @var array The arguments for this point.
         */
        protected $_args;
        
        
        
        
        /**
         *  Object constructor.
         *
         *  @param float $lat The latitude position value.
         *  @param flaot $lng The longitude position value.
         *  @param array $args The additional arguments for the point. Standard values are:
         *      label           The label for the point
         *      description     A description of the point
         *      marker          The marker image URL
         */
        public function __construct ($lat, $lng, $args = array ()) {
            $this->_lat = $lat;
            $this->_lng = $lng;
            $this->_args = $args;
        } // __construct ()
        
        
        
        
        /**
         *  Get a value from this point.
         *
         *  @param string $name The name of the value to get.
         *
         *  @return mixed The value to be returned,
         */
        public function __get ($name) {
            $value = NULL;
            
            if ($name == 'lat') {
                $value = $this->_lat;
            } // if ()
            elseif ($name == 'lng') {
                $value = $this->_lng;
            } // elseif ()
            elseif (array_key_exists ($name, $this->_args)) {
                $value = $this->_args [$name];
            } // elseif ()
            
            return $value;
        } // __get ()
        
    } // class Point