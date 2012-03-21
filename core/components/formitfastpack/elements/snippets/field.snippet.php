<?php /**
 * FormitFastPack
 *
 * Copyright 2010-11 by Oleg Pryadko <oleg@websitezen.com>
 *
 * This file is part of FormitFastPack, a FormIt helper package for MODx Revolution.
 *
 * FormitFastPack is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * FormitFastPack is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * FormitFastPack; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 */
/**
 * @package FormitFastPack
 */
/*
 * General Parameters:
 *
 * debug - turn on debugging (default: false)
 * name - the name of the field (default: '')
 * type - the field type. Used to decide which subset of the tpl chunk to use. (default: 'text')
 * outer_type - Override the type for the outer template. (default: '')
 * prefix - the prefix used by the FormIt call this field is for - may also work with EditProfile, Register, etc... snippet calls. (default: 'fi.')
 * error_prefix - Override the calculated prefix for field errors. Example: 'error.' (default: '')
 * key_prefix - To use the same field names for different forms on the same page, specify a key prefix. (default: '')
 * tpl - The template chunk to use for templating all of the various fields. Each field is separated from the others by wrapping it - both above and below - with the following HTML comment: <!-- fieldtype -->, where fieldtype is the field type. For example, for a text field: <!-- text --> <input type="[[+type]]" name="[[+name]]" value="[[+current_value]]" /> <!-- text --> Use the fieldTypesTpl.chunk.tpl in the chunks directory as the starting point. NEW: if the delimiter is not found, it tries using the default delimiter (<!-- default -->) if it is present. If neither the type delimiter or the default delimiter is present, it returns the entire chunk template. (default: 'fieldTypesTpl')
 * outer_tpl - The outer template chunk, which can be used for any HTML that stays consistent between fields. This is a good place to put your <label> tags and any wrapping <li> or <div> elements that wrap each field in your form. NEW: can now can use delimiters like the tpl.  (default: 'fieldWrapTpl')
 * chunks_path - Specify a path where file-based chunks are stored in the format lowercasechunkname.chunk.tpl, which will be used if the chunk is not found in the database.
 * inner_override - Specify your own HTML instead of using the field template. Useful if you want to use the outer_tpl and smart caching but specify your own HTML for the field. (default: '')
 * inner_element - Similar to inner_override, but accepts the name of an element (chunk, snippet...). All of the placeholders and parameters are passed to the element. Note: the inner_element override is not as useful as the options_element, which benefits much more from the smart caching. (default: '')
 * inner_element_class - Specify the classname of the element (such as modChunk, modSnippet, etc...). If using modChunk, you can specify an additional chunks_path parameter to allow file-based chunks. (default: 'modChunk')
 * inner_element_properties - A JSON array of properties to be passed to the element when it is processed. (default: '')
 * error_class - The name of the class to use for the [[+error_class]] placeholder. This placeholder is generated along with [[+error]] if a FormIt error is found for this field. (default: 'error')
 * to_placeholders - If set, will set all of the placeholders as global MODx placeholders as well. (default: false)
 * cache - Whether to enable smart caching for the field, which tries to cache as much as possible without caching the current_value, error, error_class, or selected/ checked status. (default: if the system setting 'ffp.field_default_cache' is found, uses that. Otherwise defaults to true if the field uses options or overrides and false if it doesn't.)
 *
 * Nested or Boolean Fields Parameters
 *
 * options - If your field is a nested or group type, such as checkbox, radio, or select, specify the options in tv-style format like so: Label One==value1||Label Two==value2||Label Three==value3 or Value1||Value2||Value3. The field snippet uses a sub-type (specified by option_type) to template the options. Setting this parameter causes smart caching to be enabled by default and "selected" or "checked" to be added to the currently selected option, as appropriate. See "mark_slected" and "cache" parameters. (default: '')
 * option_type - Specify the field type used for each option. If left blank, defaults to "bool" if &type is checkbox or radio and "option" if &type is select). (default: '')
 * options_override - same as inner_html, but for the options_html placeholder. Allows you to use your own custom elements while benefiting from the speed gains of the smart caching.  (default: '')
 * options_element - same as inner_element, but for the options_html placeholder (default: '')
 * options_element_class - same as inner_element_type, but for the options_html placeholder (default: 'modChunk')
 * options_element_properties - same as inner_element_properties, but for the options_html placeholder (default: '')
 * mark_selected - If left blank or set to zero, disables option marking. By default if "options" or an options override is specified, the field snippet will add a marker such as ' checked="checked"' or (if the field type is "select") ' selected="selected"' in the right place, assuming you are using HTML syntax for value (value="X"). This is a lot faster than using FormItIsSelected or FormItIsChecked.   (default: true)
 * selected_text - The text to mark selected options with (such as checked="checked" or selected="selected"). If left blank or set to false, defaults to checked="checked" unless the field type is "select", in which case it uses selected="selected". (default: '')
 *
 * Custom Parameters
 *
 * You can add an infinite number of custom parameters, all of which will be set as placeholders in the template chunks.
 * Example parameters: class, req (required), note, help
 * Example parameter usage: 
 *  - class="[[+type]] [[+class]][[+error_class]][[+req:notempty=` required`]]"
 *  - label="[[+label:default=`[[+name:replace=`_== `:ucwords]]`]][[+req:notempty=` *`]]"
 *  - [[+note:notempty=`<span class="notice">[[+note]]</span>`]]
 * 
 * Placeholders:
 * 
 * The values of all the parameters listed above and any other parameters you pass to the snippet are automatically set as placeholders. 
 * This allows you to add custom placeholders such as "required", "class", etc.... (see custom parameters above)
 * In addition, the following special placeholders are generated:
 *
 * inner_html - Used in the outer_tpl to position the generated content, which will vary by field type. Simple example: <li>[[+inner_html]]</li>
 * options_html - Used in the tpl to position the options html (only when using &options or an options override). Example: <select name="[[+name]]">[[+options_html]]</select>
 * current_value - The value of the FormIt value for the field name. Exactly the same as writing [[!fi.fieldname]] for each fieldname (if the prefix is fi.). Never gets cached.
 * error - The value of the FormIt error message for the field name, if one is found. Exactly the same as writing [[!fi.error.fieldname]] for each fieldname (if the prefix is fi.). Never gets cached.
 * error_class - set to the value of the error_class parameter (default is " error") ONLY if a FormIt error for the field name is found. Exactly the same as using [[+error:notempty=` error`]].
 * key - A unique but human-friendly identifier for each field or sub-field (useful for HTML id attributes). Generated from the key_prefix, prefix, field name, and (only if using an option field) value.
 *
 */

$debug = $modx->getOption('debug',$scriptProperties,false);
$ffp = $modx->getService('formitfastpack','FormitFastPack',$modx->getOption('ffp.core_path',null,$modx->getOption('core_path').'components/formitfastpack/').'model/formitfastpack/',$scriptProperties);
if (false) $ffp = new FormitFastPack($modx); // never used - debug only
if (!($ffp instanceof FormitFastPack)) return 'Package not found.';


// load defaults
$defaults = $ffp->getConfig();
$config = array_merge($defaults,$scriptProperties);

// Important properties
$name = $modx->getOption('name',$config,'');
$type = $modx->getOption('type',$config,'text');
$outer_type = $modx->getOption('outer_type',$config,'');
$prefix = $modx->getOption('prefix',$config,'fi.');
$error_prefix = $modx->getOption('error_prefix',$config,'');
$key_prefix = $modx->getOption('key_prefix',$config,'');

// delimiter each field type is bordered by. 
// example: <!-- textarea --> <input type="textarea" name="[[+name]]">[[+current_value]]</input> <!-- textarea -->
$delimiter_template = $modx->getOption('delimiter_template',$config,'<!-- [[+type]] -->');
$delimiter = str_replace('[[+type]]',$type,$delimiter_template);
$default_delimiter = $modx->getOption('default_delimiter',$config,'default');
$default_delimiter = str_replace('[[+type]]',$default_delimiter,$delimiter_template);
// default to the field type for outer type. If the delimiter is not found, it will use the default delimiter. If the default delimiter is not found, it will use the entire outer_tpl.
$outer_delimiter = empty($outer_type) ? $delimiter : str_replace('[[+type]]',$outer_type,$delimiter_template);

// The outer template
$outer_tpl = $modx->getOption('outer_tpl',$config,'fieldWrapTpl');
// The main template (contains all field types separated by the delimiter)
$tpl = $modx->getOption('tpl',$config,'fieldTypesTpl');

// For checkboxes, radios, selects, etc... that require inner fields, parse options
$options = $modx->getOption('options',$config,'');
$options_html = $modx->getOption('options_html',$config,'');
$options_delimiter = $modx->getOption('options_delimiter',$config,'||');
$options_inner_delimiter = $modx->getOption('options_inner_delimiter',$config,'==');
// Set defaults for the options of certain field types and allow to override from a system settings JSON array
$inner_static = $modx->fromJSON($modx->getOption('ffp.inner_options_static',null,'[]'));
if (empty($inner_static)) {
    $inner_static = array();
    $inner_static['bool'] = array('option_tpl' => 'bool','selected_text' => ' checked="checked"');
    $inner_static['checkbox'] = array('option_tpl' => 'bool','selected_text' => ' checked="checked"');
    $inner_static['radio'] = array('option_tpl' => 'bool','selected_text' => ' checked="checked"');
    $inner_static['select'] = array('option_tpl' => 'option','selected_text' => ' selected="selected"');
}
$inner_static['default'] = isset($inner_static['default']) ? $inner_static['default'] : array('option_tpl' => '','selected_text' => ' checked="checked" selected="selected"');
$default_option_tpl = isset($inner_static[$type]['option_tpl']) ? $inner_static[$type]['option_tpl'] : $inner_static['default']['option_tpl'];
$default_selected_text = isset($inner_static[$type]['selected_text']) ? $inner_static[$type]['selected_text'] : $inner_static['default']['selected_text'];

// Allow overriding the default settings for types from the script properties
$option_tpl = $modx->getOption('option_type',$config, '');
$option_tpl = $option_tpl ? $option_tpl : $default_option_tpl;
$selected_text = $modx->getOption('selected_text',$config, '');
$selected_text = $selected_text ? $selected_text : $default_selected_text;


/*      CACHING         */
// See if caching is set system-wide or in the scriptProperties
$cache = $modx->getOption('cache',$config,$modx->getOption('ffp.field_default_cache',null,'auto'));
// By default, only cache elements that have options.
if ($cache == 'auto') {
    $auto_cache = (array_key_exists($type,$inner_static) || $modx->getOption('options',$config,false)  || $modx->getOption('options_element',$config,false) || $modx->getOption('inner_element',$config,false));
    $cache = $auto_cache ? 1 : 0;
    // temporarily set auto_cach to always 1
    $cache = true;
}
$already_cached = false;
if ($cache) {
    if (empty($cacheKey)) $cacheKey = $modx->getOption('cache_resource_key', null, 'resource');
    if (empty($cacheHandler)) $cacheHandler = $modx->getOption('cache_resource_handler', null, $modx->getOption(xPDO::OPT_CACHE_HANDLER, null, 'xPDOFileCache'));
    if (!isset($cacheExpires)) $cacheExpires = (integer) $modx->getOption('cache_resource_expires', null, $modx->getOption(xPDO::OPT_CACHE_EXPIRES, null, 0));
    if (empty($cacheElementKey)) $cacheElementKey = $modx->resource->getCacheKey() . '/' . md5($modx->toJSON($config) . implode('', $modx->request->getParameters()));
    $cacheOptions = array(
        xPDO::OPT_CACHE_KEY => $cacheKey,
        xPDO::OPT_CACHE_HANDLER => $cacheHandler,
        xPDO::OPT_CACHE_EXPIRES => $cacheExpires,
    );
    $cached = $modx->cacheManager->get($cacheElementKey, $cacheOptions);
    // Get the cached values and set them as necessary
    if (isset($cached['options_html']) && isset($cached['placeholders']) && isset($cached['inner_html']) && isset($cached['outer_html'])) {
        $options_html = $cached['options_html'];
        $placeholders = $cached['placeholders'];
        $inner_html = $cached['inner_html'];
        $outer_html = $cached['outer_html'];
        $double_processing_needed = $cached['double_processing_needed'];
        $already_cached = true;
    }
}

// Skip all of the following if cached content is found
if ((!$cache) || (!$already_cached)) {
    // Set placeholders
    $placeholders = $config;
	
    // set defaults as placeholders as well
    $get_defaults = explode(',','name,type,outer_type,prefix,error_prefix,key_prefix,tpl,option_tpl,outer_tpl');
    foreach ($get_defaults as $var) {
        $placeholders[$var] = (string) ${$var};
    }
	
	// load custom placeholders - not essential, but helps a lot with speed.
	$custom_ph = $modx->getOption('custom_ph',$config,$modx->getOption('ffp.custom_ph',null,'class,multiple,array,header,default,class,outer_class,label,note,note_class,size,title,req,message,clear_message'));
	$custom_ph = explode(',',$custom_ph);
	foreach ($custom_ph as $key) {
		if (!isset($placeholders[$key])) $placeholders[$key] = '';
	}
	
	// set placeholders for field types (e.g [[+checkbox:notempty=`checkbox stuff`]])
	$set_type_ph = $modx->getOption('set_type_ph',$config,'text,textarea,checkbox,radio,select');
	if ($set_type_ph) {
		$types = explode(',',$set_type_ph);
		foreach ($types as $key) {
			$placeholders[$key] = ($key == $type) ? '1' : '';
		}
	}
	
	// generate unique key
    $unique_key = $placeholders['key'] = preg_replace("/[^a-zA-Z0-9\s]/", "", $key_prefix.$name);

    // Set overrides for options and inner_html
    $inner_html = isset($inner_html) ? $inner_html : $modx->getOption('inner_override',$config,'');

    // Process element overrides
    $possible_overrides = array('options','inner');
    foreach($possible_overrides as $level) {
        $level_html = $level.'_html';
        $level_element = $level.'_element';
        $level_element_class = $level.'_element_class';
        $level_element_properties = $level.'_element_properties';
        ${$level_html} = isset(${$level_html}) ? ${$level_html} : $modx->getOption($level_html,$config,'');
        ${$level_element} = $modx->getOption($level_element,$config,'');
        ${$level_element_class} = $modx->getOption($level_element_class,$config,'modChunk');
        ${$level_element_properties} = $modx->fromJSON($modx->getOption($level_element_properties,$config,'[]'));
        $properties = array_merge($placeholders,${$level_element_properties});
        if (${$level_element} && ${$level_element_class}) {
            if (${$level_element_class} === 'modChunk') {
                // Shortcut - use the cachable chunk method of FFP. Allows file-based chunks.
                ${$level_html} = $ffp->getChunk(${$level_element}, $properties);
            } else {
                // Full route for snippets & others
                $elementObj = $modx->getObject(${$level_element_class}, array('name' => ${$level_element}));
                if ($elementObj) {
                    ${$level_html} = $elementObj->process($properties);
                    $placeholders[$level.'_html'] = ${$level_html};
                }
            }
        }
    }

    // set almost-final inner and outer html
    if (empty($inner_html)) $inner_html = $ffp->getChunkContent($tpl,$delimiter,$default_delimiter);
    if (empty($outer_html)) $outer_html = $ffp->getChunkContent($outer_tpl,$outer_delimiter,$default_delimiter);

    // If outer template is set, process it. Otherwise just use the $inner_html
    $double_processing_needed = false;
    $outer_html = empty($outer_html) ? $inner_html : $outer_html;
    $inner_no_replace = '[[+inner_html:';
    $inner_replace = '[[+inner_html]]';
    if (strpos($outer_html,$inner_no_replace) !== false) {
        $double_processing_needed = true;
    } else {
        $outer_html = str_replace($inner_replace,$inner_html,$outer_html);
    }

    // unset any variable placeholders
    $variables = array('error','current_value','error_class','options_html','inner_html','outer_html');
    foreach ($variables as $key) {
        if (isset($placeholders[$key])) unset($placeholders[$key]);
    }
	$outer_html = $ffp->processContent($outer_html,$placeholders);

	
    // Parse options for checkboxes, radios, etc... if &options is passed
    // Note: if any provided options_html has been found, this part will be skipped
    if ($options && !$options_html) {
        $inner_delimiter = '<!-- '.$option_tpl.' -->';
        $options_html = '';
        $options = explode($options_delimiter,$options);
        foreach ($options as $option) {
            $option_array =  explode($options_inner_delimiter,$option);
            foreach ($option_array as $key => $value) {
                $option_array[$key] = trim($value);
            }
            $inner_array = $placeholders;
            $inner_array['label'] = $option_array[0];
            $inner_array['value'] = isset($option_array[1]) ? $option_array[1] : $option_array[0];
            $inner_array['key'] = $placeholders['key'].'-'.preg_replace("/[^a-zA-Z0-9-\s]/", "", $inner_array['value']);
            $options_html .= $ffp->getChunk($tpl,$inner_array,$inner_delimiter);
        }
    }

}


// cache everything up to this point if cache is enabled
$cached = array(
    'options_html' => $options_html,
    'inner_html' => $inner_html,
    'outer_html' => $outer_html,
    'placeholders' => $placeholders,
    'double_processing_needed' => $double_processing_needed,
);

// Grab the error and current value from FormIt placeholders
$error_prefix = $error_prefix ? $error_prefix : $prefix.'error.';
$error = $modx->getPlaceholder($error_prefix.$name);
$current_value = $modx->getPlaceholder($prefix.$name);
$use_get = $modx->getOption('use_get',$config,false);
$use_request = $modx->getOption('use_request',$config,false);
$use_cookies = $modx->getOption('use_cookies',$config,false);
if (($current_value == '') && $use_get) {
	$current_value = isset($_GET[$name]) ? $_REQUEST[$name] : '';
}
if (($current_value == '') && $use_request) {
	$current_value = isset($_REQUEST[$name]) ? $_REQUEST[$name] : '';
}
if ($use_cookies) {
	$session_key = 'field.'.$placeholders['key'].$name;
	$current_value = ($current_value == '') ? $modx->getOption($session_key,$_SESSION,'') : $current_value;
	$_SESSION[$session_key] = $current_value;
}

// Set the error and current value placeholders.
$placeholders['error'] = $error;
$placeholders['current_value'] = $current_value; // ToDo: add better caching and take this out to a str_replace function.
$placeholders['error_class'] = $error ? (' '.$modx->getOption('error_class',$config,'error')) : '';

// Add selected markers to options - much faster than FormItIsSelected and FormItIsChecked for large forms
if ($options_html && $selected_text && $modx->getOption('mark_selected',$config,true)) {
    $options_html = $ffp->markSelected($options_html,$current_value,$selected_text);
}
$placeholders['options_html'] = $options_html;
$placeholders['inner_html'] = $inner_html;
$placeholders['outer_html'] = $outer_html;

// Process outer_tpl first ONLY if inner_html ph has output filters.
// Warning: this may cause unexpected results due to double processing.
if ($double_processing_needed) {
    $outer_html = $ffp->getChunk($outer_tpl,$placeholders);
    // $placeholders['outer_html'] = $outer_html;
}

// Optionally set all placeholders globally
if ($modx->getOption('to_placeholders',$config,false)) {
    $modx->toPlaceholders($placeholders,$key_prefix);
}
// Process the placeholders. With caching, this should be the only time a chunk is processed.
$output = $outer_html;
$output = $ffp->processContent($output,$placeholders);

// Put the cache array into the cache.
if ($cache && !$already_cached && $modx->getCacheManager()) {
    $modx->cacheManager->set($cacheElementKey, $cached, $cacheExpires, $cacheOptions);
}

if ($debug) {
	$unprocessed = str_replace('[','&#91;',htmlentities($outer_html));
	$unprocessed = str_replace(']','&#93;',$unprocessed);
	$placeholders['double_processing_needed'] = (string) ($double_processing_needed * 1);
	$output = '<pre>'.htmlentities(print_r($placeholders,1));
	$output .= "\nUnprocessed Output: ";
	$output .= $unprocessed;
	$output .= "\n\n </pre>";
}
// if (strpos($output,'[[+') !== false) die($output);
return $output;