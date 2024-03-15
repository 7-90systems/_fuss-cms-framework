jQuery (document).ready (function () {

    let posttype_builder_main = jQuery ('#fuse_posttype_builder_metaboxes_main');
    let posttype_builder_side = jQuery ('#fuse_posttype_builder_metaboxes_side');
    
    jQuery ('#fuse_builder_new_metabox_add').click (function (e) {
        e.preventDefault ();
        
        let html = jQuery ('#fuse-builder_meta_box').html ();
        
        if (jQuery ('#fuse_builder_new_metabox_location').val () == 'side') {
            posttype_builder_side.find ('p.none').remove ();
            posttype_builder_side.append (html);
        } // if ()
        else {
            posttype_builder_main.find ('p.none').remove ();
            posttype_builder_main.append (html);
        } // else
    });

});