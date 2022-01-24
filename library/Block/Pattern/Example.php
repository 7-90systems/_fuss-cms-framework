<?php
    /**
     *  @package fuse-cms-framework
     *
     *  This is an example block pattern that you can use to shape your own
     *  patterns.
     */
    
    namespace Fuse\Block\Pattern;
    
    use Fuse\Block\Pattern;
    
    
    class Example extends Pattern {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            parent::__construct ('example-pattern', __ ('Fuse CMS Example Pattern', 'fuse'), __ ('This is an example of how to build patterns. Use this to create your own patterns to make your theme shine.', 'fuse'), '<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2>First Column Heading</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>First column content</p>
<!-- /wp:paragraph -->

<!-- wp:image -->
<figure class="wp-block-image"><img alt=""/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2>Second Column Heading</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Second column content</p>
<!-- /wp:paragraph -->

<!-- wp:image -->
<figure class="wp-block-image"><img alt=""/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->');
        } // __construct ()
        
    } // class Example