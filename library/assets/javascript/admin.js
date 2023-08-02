/**
 *  @package fusecms
 *
 *  This takes care of our administration functions.
 */
jQuery (document).ready (function () {
        
    // We want to make it harder to change the site URL's
    fuseMaskSiteUrls ();
    
    // Set up our input fields
    fuseSetupInputFields ();
    
});




/**
 *  Mask the site URLs to see if we can make it harder for people to change
 *  the sites URls.
 */
function fuseMaskSiteUrls () {
    var fields = [
        'input#siteurl',
        'input#home'
    ];
    
    jQuery (fields.join (',')).prop ('readonly', 'readonly');
    
    for (var i in fields) {
        var el = jQuery (fields [i]);
        
        el.after (' &nbsp; <button class="button url-button enable" data-target="' + fields [i] + '">' + fuse_admin.fuse_url_button_disabled + '</button>');
    } // for ()
    
    jQuery ('.url-button').click (function (e) {
        e.preventDefault ();
        
        var btn = jQuery (this);
        var show_message = true;
        
        if (btn.hasClass ('enable')) {
            show_message = confirm (fuse_admin.fuse_url_button_message);
        } // if ()
        
        if (show_message) {
            var field = jQuery (btn.data ('target'));
            
            if (btn.hasClass ('enable')) {
                btn.text (fuse_admin.fuse_url_button_enabled);
                field.removeProp ('readonly');
            } // if ()
            else {
                btn.text (fuse_admin.fuse_url_button_disabled);
                field.prop ('readonly', 'readonly');
            } // else
            
            btn.toggleClass ('enable');
        } // if ()
    });
} // fuseMaskSiteUrls ()




/**
 *  Set up our input fields.
 */
function fuseSetupInputFields () {
    fuseSetupImageInput ();
    fuseSetupGalleryInput ();
} // fuseSetupinputFields ()

/**
 *  Set up the image input fields.
 */
function fuseSetupImageInput () {
    jQuery ('.fuse-input-image-container').each (function () {
        var el = jQuery (this);
        var btn = el.find ('button');
        var img = el.find ('.image-container');
        var input = el.find ('input');
        var del = el.find ('.delete');
        
        // Upload file...
        var file_frame;
 
        btn.on ('click', function (e) {
            e.preventDefault ();
            
            // If the media frame already exists, reopen it.
            if (file_frame) {
                // Open frame
                file_frame.open ();
                return;
            } // if ()
            
            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media ({
                title: 'Select Image',
                button: {
                    text: 'Set Image',
                },
                library: {
                    type: [
                        'image'
                    ]
                },
                multiple: false	// Set to true to allow multiple files to be selected
            });
            
            // When an image is selected, run a callback.
            file_frame.on ('select', function () {
                // We set multiple to false so only get one image from the uploader
                attachment = file_frame.state ().get ('selection').first ().toJSON ();

                var src = attachment.sizes.full.url;
                
                if (typeof attachment.sizes.thumbnail != 'undefined') {
                    src = attachment.sizes.thumbnail.url;
                } // if ()
                
                btn.hide ();
                img.find ('img').attr ('src', src);
                img.show ();
                input.val (attachment.id);
            });
            
            // Finally, open the modal
            file_frame.open ();
        });
        
       del.click (function (e) {
            e.preventDefault ();
            btn.show ();
            img.hide ();
            input.val ('');
        });
    });
} // fuseSetupImageInput ()

/**
 *  Set up the image input fields.
 */
function fuseSetupGalleryInput () {
    jQuery ('.fuse-input-gallery-container').each (function () {
        var el = jQuery (this);
        var container = el.find ('.gallery-images');
        var btn = el.find ('button');
        var input = el.find ('input');
        var template = el.find ('template');
        
        container.sortable ({
            update: function () {
                _fuseGalleryInputSetIds (input);
            }
        });
        
        // Upload file...
        var file_frame;
 
        btn.on ('click', function (e) {
            e.preventDefault ();
            
            // If the media frame already exists, reopen it.
            if (file_frame) {
                // Open frame
                file_frame.open ();
                return;
            } // if ()
            
            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media ({
                title: 'Add Gallery Images',
                button: {
                    text: 'Add Gallery Images',
                },
                library: {
                    type: [
                        'image'
                    ]
                },
                multiple: true
            });
            
            // When an image is selected, run a callback.
            file_frame.on ('select', function () {
                var images = file_frame.state ().get ('selection');
                
                for (var attachment of images) {
                    attachment = attachment.toJSON ();
// console.log (attachment);
                //  var attachment = images [i].toJSON ();
                    
                    var src = attachment.sizes.full.url;
                    var id = attachment.id;
                
                    if (typeof attachment.sizes.thumbnail != 'undefined') {
                        src = attachment.sizes.thumbnail.url;
                    } // if ()
console.log ("Added image '" + id + "': '" + src + "'...");

                    var html = template.html ();
                    html = html.replace ('%%ID%%', id);
                    html = html.replace ('%%THUMBNAIL%%', src);
                    
                    container.append (html);
                } // for ()
                
                _fuseGalleryInputSetIds (input);
            });
            
            // Finally, open the modal
            file_frame.open ();
        });
        
        el.on ('click', '.delete', function (e) {
            e.preventDefault ();
            
            jQuery (this).closest ('.image-container').remove ();
            _fuseGalleryInputSetIds (input);
        });
    });
} // fuseSetupGalleryInput ()

function _fuseGalleryInputSetIds (input) {
    var ids = [];
    
    input.siblings ('.gallery-images').find ('.image-container').each (function () {
        ids.push (jQuery (this).data ('id'));
    });
    
    input.val (ids);
} // _fuseGalleryInputSetIds ()