/**
 *  @package fusecms
 *
 *  This takes care of our administration functions.
 */
jQuery (document).ready (function () {
        
    // We want to make it harder to change the site URLs
    fuseMaskSiteUrls ();
    fuseSettingsForms ();
    
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
 *  Set up our settings form actions.
 */
function fuseSettingsForms () {
    jQuery ('.fuse-settings-form-list li:first-child a').addClass ('active');
    jQuery ('.fuse-settings-panel').hide ().first ().show ();
    
    jQuery ('.fuse-settings-form-list a').click (function (e) {
        e.preventDefault ();
        
        var btn = jQuery (this);
        
        jQuery ('.fuse-settings-form-list a').removeClass ('active');
        btn.addClass ('active');
        
        jQuery ('.fuse-settings-panel').hide ();
        jQuery (btn.attr ('href')).show ();
    });
} // fuseSettingsForms ()