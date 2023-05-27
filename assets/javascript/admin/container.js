/**
 *  @package fuse-cms-framework
 *
 *  @version 1.0
 *
 *  This is the JavaScript functionality for our forms.
 */

jQuery (document).ready (function () {
    
    fuseFormsSetup ();
    fuseFormRequired ();

});




/**
 *  Set up our forms.
 */
function fuseFormsSetup () {
    // Set up our container display
    jQuery ('.fuse-forms-container').each (function () {
        var container = jQuery (this);
        var tab_buttons = container.find ('.fuse-form-panel-tabs li a');
        var panels = container.find ('.fuse-forms-panel');
        
        tab_buttons.removeClass ('active').first ().addClass ('active');
        panels.hide ().first ().show ();
    });
    
    jQuery ('.fuse-forms-container').on ('click', '.fuse-form-panel-tabs li a', function (e) {
        e.preventDefault ();
        
        var btn = jQuery (this);
        var container = btn.closest ('.fuse-forms-container');
        
        container.find ('.fuse-forms-panel').hide ();
        container.find ('.fuse-form-panel-tabs li a').removeClass ('active');
        
        jQuery (btn.attr ('href')).show ();
        btn.addClass ('active');
    });
    
    // Set up our display conditions
    jQuery ('.fuse-forms-container').on ('input', 'input[type=text]', _checkFuseFormFieldConditions);
    jQuery ('.fuse-forms-container').on ('input', 'input[type=number]', _checkFuseFormFieldConditions);
    jQuery ('.fuse-forms-container').on ('input', 'input[type=url]', _checkFuseFormFieldConditions);
    jQuery ('.fuse-forms-container').on ('input', 'input[type=email]', _checkFuseFormFieldConditions);
    jQuery ('.fuse-forms-container').on ('input', 'input[type=hidden]', _checkFuseFormFieldConditions);
    jQuery ('.fuse-forms-container').on ('input', 'textarea', _checkFuseFormFieldConditions);
    
    jQuery ('.fuse-forms-container').on ('change', 'input[type=checkbox]', _checkFuseFormFieldConditions);
    jQuery ('.fuse-forms-container').on ('change', 'input[type=radio]', _checkFuseFormFieldConditions);
    jQuery ('.fuse-forms-container').on ('change', 'select', _checkFuseFormFieldConditions);
    
    _checkFuseFormFieldConditions ();
    
    // Set up our various fields
    _fuseFormsSetupToggleFields ();
    _fuseFormsSetupDateFields ();
} // fuseFormsSetup ()




/**
 *  Check fields for conditions.
 */
function _checkFuseFormFieldConditions () {
    /**
     *  Check field visibility.
     */
    jQuery ('.fuse-forms-container').find ('input, select, textarea, .fuse-field-group').each (function () {
        var element = jQuery (this);
        var container = element.closest ('.fuse-forms-panel-field-container, .fuse-field-group-column');
        var conditions = element.data ('conditions');
// console.log ("Checking field conditions '" + conditions + "'...");
        
        if (typeof conditions !== 'undefined' && conditions.length > 0) {
// console.log (" - Field has conditions!");
            if (_checkFieldConditions (conditions) !== true) {
                container.hide ();
            } // if ()
            else {
                container.show ();
            } // else
        } // if ()
    });
    
    
    /**
     *  Check panel visibility.
     */
    jQuery ('.fuse-forms-panel').each (function () {
        var panel = jQuery (this);
        
        var conditions = panel.data ('conditions');
// console.log ("Checking panel conditions...");
        
        if (typeof conditions !== 'undefined' && conditions.length > 0) {
// console.log (" - Panel has conditions!");
            var panel_link = jQuery ('.fuse-form-panel-tabs').find ('a[href$=#' + panel.attr ('id') + ']').closest ('li');
            
            if (_checkFieldConditions (conditions) !== true) {
                panel.hide ();
                panel_link.hide ();
            } // if ()
            else {
                panel_link.show ();
            } // else
        } // if ()
    });
} // _checkFuseFormFieldConditions ()




/**
 *  Set up our togle fields.
 */
function _fuseFormsSetupToggleFields () {
    // Set up listeners
    jQuery ('.fuse-forms-container').on ('click', '.fuse-forms-field-toggle li', function (e) {
        e.preventDefault ();
// confirm ("Clicked on option '" + jQuery (this).data ('value') + "'");
        var btn = jQuery (this);
        
        if (btn.hasClass ('selected') === false) {
            var container = btn.closest ('.fuse-forms-field-toggle');
            var fields = container.find ('li');
            var value = btn.data ('value');
            
            fields.removeClass ('selected');
            btn.addClass ('selected');
            
            container.data ('value', value);
            container.find ('input').val (value).trigger ('input');
        } // if ()
    });
    
    // Check for a default value
    jQuery ('.fuse-forms-field-toggle').each (function () {
        var set_value = jQuery (this).data ('value');
        
        if (set_value.length == 0) {
            jQuery (this).find ('li').first ().trigger ('click');
        } // if ()
    });
} // _fuseFormsSetupToggleFields ()




/**
 *  Check a field value for a comparison
 */
function _checkFieldConditions (conditions) {
    var condition_met = true;
    
    var field_id;
    var comparison_value;
    var comparison;
    var field_value;
            
    var display = true;
            
    for (var i in conditions) {
        field_id = conditions [i].field;
        comparison_value = conditions [i].value;
        comparison = conditions [i].comparison;
                
        var field = jQuery ('#fuse-form-field-' + field_id);
                
        if (typeof field !== 'undefined') {
            field_value = field.val ();
// console.log ("Comparison for '" + field_id + "': '" + comparison + "' (" + conditions [i].comparison + ") : '" + field_value + "' -> '" + comparison_value + "'");
                    
            if (comparison == '=' || comparison == 'equals') {
                if (field_value != comparison_value) {
                    condition_met = false;
                } // if ()
            } // if ()
            else if (comparison == '!' || comparison == '!=' || comparison == 'not') {
                if (field_value == comparison_value) {
                    condition_met = false;
                } // if ()
            } // else if()
            else if (comparison == 'in') {
                if (_isValueInList (field_value, comparison_value) === false) {
                    condition_met = false;
                } // if ()
            } // else if ()
            else if (comparison == 'not in') {
                if (_isValueInList (field_value, comparison_value) === true) {
                    condition_met = false;
                } // if ()
            } // else if ()
            else if (comparison == 'any') {
// console.log ("Checking ANY comparison: '" + field_value + "'");
                if (typeof field_value === 'undefined' || field_value.length == 0) {
                    condition_met = false;
                } // if ()
            } // else if ()
            else if (comparison == 'empty') {
                if (typeof field_value !== 'undefined' && field_value.length > 0) {
                    condition_met = false;
                } // if ()
            } // else if ()
        } // if ()
    } // for ()
    
    return condition_met;
} // _checkFieldConditions ()

/**
 *  Check to see if a value is in an array of values.
 *
 *  @param mixed value The value to check.
 *  @param array value_list The list of values.
 *
 *  @return bool True if the value exists or false if the value does not exist.
 *  Note that it the value is an array we will search for all array values.
 */
function _isValueInList (value, value_list) {
    var in_list = false;
    var i;
    
    if (typeof (value == 'array')) {
        var in_list_count = 0;
        
        for (i in value) {
            for (var j in value_list) {
                if (values_list [j] == value [i]) {
                    in_list_count++;
                } // if ()
            } // for ()
        } // for ()
        
        if (in_list_count == values.length) {
            in_list = true;
        } // if ()
    } // if ()
    else {
        for (i in value_list) {
            if (value_list [i] == value) {
                in_list = true;
            } // if ()
        } // for ()
    } // else
    
    return in_list;
} // _isValueInList ()




/**
 *  Set up our date fields.
 */
function _fuseFormsSetupDateFields () {
    jQuery ('.fuse-datepicker').each (function () {
        var field= jQuery (this);
        var alt_field = '#' + field.attr ('id').substr (16);
        
        field.datepicker ({
            dateFormat: 'd MM yy',
            altFormat: 'yy-mm-dd',
            altField: alt_field
        });
        
        field.on ('input', function () {
            if (jQuery (this).val ().length == 0) {
                jQuery (alt_field).val ('');
            } // if ()
        });
    });
} // _fuseFormsSetupDateFields ()




/**
 *  Set up the functionality to show required fields.
 */
function fuseFormRequired () {
    jQuery ('.fuse-forms-container input, .fuse-forms-container select, .fuse-forms-container textarea').on ('input', _fuseFormCheckRequired);
    jQuery ('.fuse-forms-container select').change (_fuseFormCheckRequired);
} // fuseFormRequired ()

function _fuseFormCheckRequired () {
    var el = jQuery (this);
    
    if (el.prop ('required')) {
        var label = el.parent ().siblings ('label');
        
        if (el.val ().length > 0) {
            label.removeClass (['required-empty', 'admin-bold', 'admin-red']);
        } // if ()
        else {
            label.addClass (['required-empty', 'admin-bold', 'admin-red']);
        } // else

    } // if ()
} // _fuseFormCheckRequired ()