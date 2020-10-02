<?php
    /**
     *  Set up our map class.
     */
    
    namespace Fuse\Geo;
    
    use Fuse\Geo\Map\Point;
    
    
    class Map {
        
        /**
         *  @var array These are the points for the map.
         */
        protected $points = array ();
        
        
        
        
        /**
         *  Object constructor.
         *
         *  @param array $points An array of Fuse\Geo\Map\Points points for this map.
         */
        public function __construct ($points = array ()) {
            $this->addPoints ($points);
        } // __construct ()
        
        
        
        
        /**
         *  Add a set of points
         *
         *  @param array $points The points to add to this map.
         *
         *  @return Fuse\Geo]Map This map.
         */
        public function addPoints ($points) {
            foreach ($points as $point) {
                $this->addPoint ($point);
            } // foreach ()
            
            return $this;
        } // addPoints ()
        
        /**
         *  Add a single point to the map.
         *
         *  @param Fuse\Geo\Map\Point $point The point.
         *
         *  return Fuse\Geo]Map This map.
         */
        public function addPoint (Point $point) {
            $this->_points [] = $point;
            
            return $this;
        } // addPoint ()
        
        /**
         *  Clear the points from this map.
         *
         *  @return Fuse\Geo]Map This map.
         */
        public function clear () {
            $this->_points = array ();
            
            return $this;
        } // clear ()
        
        
        
        
        /**
         *  Get the points for this map.
         *
         *  @return array This returns the array of points set for this map.
         */
        public function getPoints () {
            return $this->_points ();
        } // getPoints ()
        
        
        
        
        /**
         *  Display our map code!
         *  */
        public function display () {
            $geo_key = get_option ('fuse_geo_key', '');
            
            if (empty ($geo_key) === false && count ($this->_points) > 0) {
                $map_id = uniqid ('fuse_geo_map_');
                
                if (count ($this->_points) > 1) {
                    $this->_displayMultiPointMap ($geo_key, $map_id);
                } // if ()
                else {
                    $this->_displaySinglePointMap ($geo_key, $map_id);
                } // else
            } // if ()
        } // display ()
        
        
        
        
        /**
         *  Show a map with a single point.
         *
         *  @param string $geo_key The Google API key.
         *  @param string $map_id The ID for the map area.
         */
        protected function _displaySinglePointMap ($geo_key, $map_id) {
            $map_zoom = get_option ('fuse_geo_zoom', 16);
            $point = $this->_points [0];
            
            ?>
                <div id="<?php esc_attr_e ($map_id); ?>" class="fuse-map-container" style="width: 100%; height: 100%;"></div>
                <script type="text/javascript">
                    function <?php echo $map_id; ?>_initMap () {
                        var marker_point = {
                            lat: <?php echo $point->lat; ?>,
                            lng: <?php echo $point->lng; ?>
                        };
                        
                        var map = new google.maps.Map (
                            document.getElementById ('<?php echo $map_id; ?>'),
                            {
                                zoom: <?php echo $map_zoom; ?>,
                                center: marker_point
                            }
                        );
                            
                        var marker = new google.maps.Marker ({
                            position: marker_point,
                            <?php if (strlen ($point->label) > 0): ?>
                                title: '<?php esc_attr_e ($point->label); ?>',
                            <?php endif; ?>
                            map: map
                        });
                    } // <?php echo $map_id; ?>_initMap ()
                </script>
                <script defer src="https://maps.googleapis.com/maps/api/js?key=<?php esc_attr_e ($geo_key); ?>&callback=<?php esc_attr_e ($map_id); ?>_initMap"></script>
            <?php
        } // _displaySinglePointMap ()
        
    } // class Map