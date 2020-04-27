<?php
    /**
     *  @package fusecms
     *
     *  Repeater row looping.
     */
    
    namespace Fuse\Block\Util;
    
    
    class Loop {
        
        /**
         *  @var Fuse\Block\Util\Loop The single instance of this class.
         */
        static private $_instance;
        
        
        
    
        /**
         * Current pointer in active loops.
         *
         * An associative array of $loop_name => $pointer.
         * The $pointer is an int of the current iteration, e.g: 0, 1, or 2.
         *
         * @var array
         */
        public $loops = array ();
    
        /**
         * Currently active loop
         *
         * @var string
         */
        public $active;
        
        
        
        
        /**
         *  Block direct instantiation.
         */
        private function __construct () {
            // Nothing to do here.
        } // __consturct ()
        
        
        
        
        /**
         *  Set a loop up to start.
         *
         *  @param string #name The name of the loop to set up.
         *
         *  @return int|false Returns the item count or false if not found. Be
         *  aware that a valid loop can have 0 rows, so test for false and not 0!
         */
        public function setLoop ($name) {
            $loop = false;
            
            $attributes = \Fuse\Block\Util::$data ['attributes'];
            
            if (array_key_exists ($name, $attributes)) {
                if (array_key_exists ('rows', $attributes [$name]) && is_array ($attributes [$name]['rows'])) {
                    $loop = count ($attributes [$name]['rows']);
                    $this->loops [$name] = 0;
                } // if ()
            } // if ()
            
            return $loop;
        } // setLoop ()
        
        /**
         *  Reset a loop.
         *
         *  @param string $name The name of the loop to reset.
         *
         *  @return \Fuse\Block\Util\Loop This loop object.
         */
        public function resetLoop ($name) {
            if (array_key_exists ($name, $this->loops)) {
                $this->loops [$name] = 0;
            } // if ()
            
            return $this;
        } // resetLoop ()
        
        /**
         *  Incrfement a repeater.
         *
         *  @param string $name The name of the repeater to increment.
         *
         *  @return \Fuse\Block\Util\Loop This loop object.
         */
        public function increment ($name) {
            if (array_key_exists ($name, $this->loops)) {
                $this->loops [$name]++;
            } // if ()
            
            return $this;
        } // increment ()
        
        /**
         *  Get the current row of the given loop.
         *
         *  @param string $name The name of the loop to get.
         *
         *  @return mixed|false Returns the row data or false if the row doesn't
         *  exist.
         */
        public function getRepeaterRow ($name, $increment = false) {
            $row = false;
            
            $attributes = \Fuse\Block\Util::$data ['attributes'];
            
            if (array_key_exists ($name, $this->loops) === false) {
                $this->setLoop ($name);
            } // if ()
            
            if (array_key_exists ($name, $attributes) && array_key_exists ($name, $this->loops)) {
                $loop_count = $this->loops [$name];
                
                if (array_key_exists ('rows', $attributes [$name]) && is_array ($attributes [$name]['rows']) && array_key_exists ($loop_count, $attributes [$name]['rows'])) {
                    $row = $attributes [$name]['rows'][$loop_count];
                    
                    if ($increment === true) {
                        $this->loops [$name]++;
                    } // if ()
                } // if ()
            } // if ()
            
            return $row;
        } // getRepeaterRow ()
        
        /**
         *  Get the requested row of the given loop.
         *
         *  @param string $name The name of the loop to get.
         *  @param int $index The index to return.
         *
         *  @return mixed|false Returns the row data or false if the row doesn't
         *  exist.
         */
        public function getRepeaterRowAt ($name, $index = false) {
            $row = false;
            
            if (array_key_exists ($name, $this->loops) === false) {
                $this->setLoop ($name);
            } // if ()
            
            if ($index === false) {
                $index = $this->loops [$name];
            } // if ()
            
            $attributes = \Fuse\Block\Util::$data ['attributes'];
            
            if (array_key_exists ($name, $this->loops) === false) {
                $this->setLoop ($name);
            } // if ()
            
            if (array_key_exists ($name, $attributes) && array_key_exists ($name, $this->loops)) {
                if (array_key_exists ('rows', $attributes [$name]) && is_array ($attributes [$name]['rows']) && array_key_exists ($index, $attributes [$name]['rows'])) {
                    $row = $attributes [$name]['rows'][$index];
                } // if ()
            } // if ()
            
            return $row;
        } // getRepeaterRowAt ()
        
        
        
        
        /**
         *  Get the class instance.
         */
        static public function getInstance () {
            if (empty (self::$_instance)) {
                self::$_instance = new Loop ();
            } // if ()
            
            return self::$_instance;
        } // getInstance ()
        
    } // class Loop