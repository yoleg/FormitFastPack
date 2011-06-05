<?php/**
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
 * prefix - the prefix used by the FormIt call this field is for - may also work with EditProfile, Register, etc... snippet calls. (default: 'fi.')
 * key_prefix - To use the same field names for different forms on the same page, specify a key prefix. (default: '')
 * outer_tpl - The outer template chunk, which can be used for any HTML that stays consistent between fields. This is a good place to put your <label> tags and any wrapping <li> or <div> elements that wrap each field in your form. (default: 'fieldWrapTpl')
 * tpl - The template chunk to use for templating all of the various fields. Each field is separated from the others by wrapping it - both above and below - with the following HTML comment: <!-- fieldtype -->, where fieldtype is the field type. For example, for a text field: <!-- text --> <input type="[[+type]]" name="[[+name]]" value="[[+current_value]]" /> <!-- text --> Use the fieldTypesTpl.chunk.tpl in the chunks directory as the starting point. (default: 'fieldTypesTpl')
 * inner_override - Specify your own HTML instead of using the field template. Useful if you want to use the outer_tpl and smart caching but specify your own HTML for the field. (default: '')
 * inner_chunk - Similar to inner_override, but accepts the name of a chunk. All of the placeholders and parameters are passed to the chunk. (default: '')
 * error_class - The name of the class to use for the [[+error_class]] placeholder. This placeholder is generated along with [[+error]] if a FormIt error is found for this field. (default: 'error')
 * to_placeholders - If set, will set all of the placeholders as global MODx placeholders as well. (default: false)
 * cache - Whether to enable smart caching for the field, which tries to cache as much as possible without caching the current_value, error, error_class, or selected/ checked status. (default: if the system setting 'ffp.field_default_cache' is found, uses that. Otherwise defaults to true if the field uses options or overrides and false if it doesn't.)
 *
 * Nested or Boolean Fields Parameters
 *
 * options - If your field is a nested or group type, such as checkbox, radio, or select, specify the options in tv-style format like so: Label One==value1||Label Two==value2||Label Three==value3 or Value1||Value2||Value3. The field snippet uses a sub-type (specified by option_type) to template the options. Setting this parameter causes smart caching to be enabled by default and "selected" or "checked" to be added to the currently selected option, as appropriate. See "mark_slected" and "cache" parameters. (default: '')
 * option_type - Specify the field type used for each option. If left blank, defaults to "bool" if &type is checkbox or radio and "option" if &type is select). (default: '')
 * options_override - you can specify your own HTML instead of using the &options parameter to generate options. For example, you might decide to pass in <option value="something" data="something">hello</option> if the type is set to "select". It otherwise acts exactly as if you had specified the options parameter for marking and caching purposes. (default: '')
 * options_chunk - Similar to options_override, but accepts the name of a chunk. All of the placeholders and parameters are passed to the chunk. (default: '')
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
if (!($ffp instanceof FormitFastPack)) return 'Package not found.';

// Important properties
$name = $modx->getOption('name',$scriptProperties,'');
$type = $modx->getOption('type',$scriptProperties,'text');
$prefix = $modx->getOption('prefix',$scriptProperties,'fi.');
$key_prefix = $modx->getOption('key_prefix',$scriptProperties,'');

// delimiter each field type is bordered by. 
// example: <!-- textarea --> <input type="textarea" name="[[+name]]">[[+current_value]]</input> <!-- textarea -->
$delimiter = '<!-- '.$type.' -->';
// The outer template 
$outer_tpl = $modx->getOption('outer_tpl',$scriptProperties,'fieldWrapTpl');
// The main template (contains all field types separated by the delimiter)
$tpl = $modx->getOption('tpl',$scriptProperties,'fieldTypesTpl');

// For checkboxes, radios, selects, etc... that require inner fields, parse options
$options = $modx->getOption('options',$scriptProperties,'');
$options_delimiter = '||';
$options_inner_delimiter = '==';

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
$option_tpl = $modx->getOption('option_type',$scriptProperties, '');
$option_tpl = $option_tpl ? $option_tpl : $default_option_tpl;
$selected_text = $modx->getOption('selected_text',$scriptProperties, '');
$selected_text = $selected_text ? $selected_text : $default_selected_text;

/*      CACHING         */
// See if caching is set system-wide or in the scriptProperties
$cache = $modx->getOption('cache',$scriptProperties,$modx->getOption('ffp.field_default_cache'));
// By default, only cache elements that have options.
$cache = isset($cache) ? $cache : array_key_exists($type,$inner_static) || $modx->getOption('options_chunk',$scriptProperties,false) || $modx->getOption('inner_chunk',$scriptProperties,false);
$already_cached = false;
if ($cache) {
    if (empty($cacheKey)) $cacheKey = $modx->getOption('cache_resource_key', null, 'resource');
    if (empty($cacheHandler)) $cacheHandler = $modx->getOption('cache_resource_handler', null, $modx->getOption(xPDO::OPT_CACHE_HANDLER, null, 'xPDOFileCache'));
    if (!isset($cacheExpires)) $cacheExpires = (integer) $modx->getOption('cache_resource_expires', null, $modx->getOption(xPDO::OPT_CACHE_EXPIRES, null, 0));
    if (empty($cacheElementKey)) $cacheElementKey = $modx->resource->getCacheKey() . '/' . md5($modx->toJSON($scriptProperties) . implode('', $modx->request->getParameters()));
    $cacheOptions = array(
        xPDO::OPT_CACHE_KEY => $cacheKey,
        xPDO::OPT_CACHE_HANDLER => $cacheHandler,
        xPDO::OPT_CACHE_EXPIRES => $cacheExpires,
    );
    $cached = $modx->cacheManager->get($cacheElementKey, $cacheOptions);
    // Get the cached values and set them as necessary
    if (isset($cached['options_html']) && isset($cached['placeholders']) && isset($cached['inner_html'])) {
        $options_html = $cached['options_html'];
        $placeholders = $cached['placeholders'];
        $inner_html = $cached['inner_html'];
        $already_cached = true;
    }
}

// The following variables do not need to be set if cached content is found
if (!$cache || !$already_cached) {
    // Set placeholders
    $placeholders = $scriptProperties;
    // ToDo: Move custom placeholders like this into a setFieldDefaults snippet
    $placeholders['remote_prefix'] = $modx->getOption('remote_prefix',$scriptProperties,'profile.remote.');
    $placeholders['key'] = preg_replace("/[^a-zA-Z0-9\s]/", "", $key_prefix.$name);

    // Set overrides for options and inner_html
    $options_html = isset($options_html) ? $options_html : $modx->getOption('options_override',$scriptProperties,'');
    $inner_html = isset($inner_html) ? $inner_html : $modx->getOption('inner_override',$scriptProperties,'');

    // Process inner and outer chunks, if given.
    $options_chunk = $modx->getOption('options_chunk',$scriptProperties,'');
    $inner_chunk = $modx->getOption('inner_chunk',$scriptProperties,'');
    $options_html = $options_chunk ? $modx->getChunk($options_chunk,$placeholders) : $options_html;
    $inner_html = $inner_chunk ? $modx->getChunk($inner_chunk,$placeholders) : $inner_html;
}

// Parse options for checkboxes, radios, etc... if &options is passed
// Note: if cached options_html has been found, this part will be skipped
if ($options && !$options_html) {
    $inner_delimiter = '<!-- '.$option_tpl.' -->';
    $options_html = '';
    $options = explode($options_delimiter,$options);
    foreach ($options as $option) {
        $option_array =  explode($options_inner_delimiter,$option);
        $inner_array = $placeholders;
        $inner_array['label'] = $option_array[0];
        $inner_array['value'] = isset($option_array[1]) ? $option_array[1] : $option_array[0];
        $inner_array['key'] = $placeholders['key'].'-'.preg_replace("/[^a-zA-Z0-9-\s]/", "", $inner_array['value']);
        $options_html .= $ffp->getChunk($tpl,$inner_array,$inner_delimiter);
    }
}

// cache everything up to this point if cache is enabled
$cached = array('options_html' => $options_html,'inner_html' => $inner_html,'placeholders' => $placeholders);

// Grab the error and current value from FormIt placeholders
$error = $modx->getPlaceholder($prefix.'error.'.$name);
$current_value = $modx->getPlaceholder($prefix.$name);

// Set the error and current value placeholders.
$placeholders['error'] = $error;
$placeholders['current_value'] = $current_value; // ToDo: add better caching and take this out to a str_replace function.
$placeholders['error_class'] = $error ? ' '.$modx->getOption('error_class',$scriptProperties,'error') : '';

// Add selected markers to options - much faster than FormItIsSelected and FormItIsChecked for large forms
if ($options_html && $selected_text && $modx->getOption('mark_selected',$scriptProperties,true)) {
    $options_html = $ffp->markSelected($options_html,$current_value,$selected_text);
}
$placeholders['options_html'] = $options_html;

// Process inner_html
if (empty($inner_html)) {
    $inner_html = $ffp->getChunk($tpl,$placeholders,$delimiter);
}
$placeholders['inner_html'] = $inner_html;

if ($modx->getOption('to_placeholders',$scriptProperties,false)) {
    // Set all placeholders globally, not limited just to the template chunks
    $modx->toPlaceholders($placeholders,$key_prefix);
}

// If outer template is set, process it. Otherwise just use the $inner_html
if ($outer_tpl) {
    $output = $ffp->getChunk($outer_tpl,$placeholders);
} else {
    $output = $inner_html;
}

// Put the cache array into the cache.
if ($cache && !$already_cached && $modx->getCacheManager()) {
    $modx->cacheManager->set($cacheElementKey, $cached, $cacheExpires, $cacheOptions);
}

return $output;