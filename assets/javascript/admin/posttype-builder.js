jQuery (document).ready (function () {
    jQuery ('.fuse_posttype_builder_metaboxes').sortable ();

    jQuery ('button.fuse_builder_new_metabox_add').click (function (e) {
        e.preventDefault ();
        
        let html = jQuery ('template#fuse_builder_meta_box').html ();
        let container = jQuery (this).siblings ('.fuse_posttype_builder_metaboxes');
        
        container.append (html);
        fuseBuilderSetMetaboxes ();
    });
    
    // Toggle fields
    jQuery ('.fuse_posttype_builder_metaboxes').on ('click', '.fuse_builder_metabox_title a.expand', function (e) {
        e.preventDefault ();
        
        let btn = jQuery (this);
        let container = btn.closest ('.fuse-builder-metabox').find ('.fuse_builder_metabox_fields');
        
        if (btn.hasClass ('open')) {
            btn.removeClass ('open');
            container.slideUp (200);
        } // if ()
        else {
            btn.addClass ('open');
            container.slideDown (200);
        } // else
    });
    
    // Delete metabox
    jQuery ('.fuse_posttype_builder_metaboxes').on ('click', '.fuse_builder_metabox_title a.delete', function (e) {
        e.preventDefault ();
        
        let do_delete = confirm ('Are you sure that you want to delete this metabox?');
        
        if (do_delete) {
            jQuery (this).closest ('.fuse-builder-metabox').remove ();
            fuseBuilderSetMetaboxes ();
        } // if ()
    });
    
    
    
    
    // Name field
    jQuery ('.fuse_posttype_builder_metaboxes').on ('keyup', '.fuse_builder_metabox_fields .metabox-name', function () {
        let field = jQuery (this);
        
        field.closest ('.fuse-builder-metabox').find ('h4.title').html (field.val ());
    });

});




/**
 *  Construct the JSON code to represent the metaboxes.
 */
function fuseBuilderSetMetaboxes () {
    return '';
} // fuseBuildersetMetaBoxes ()