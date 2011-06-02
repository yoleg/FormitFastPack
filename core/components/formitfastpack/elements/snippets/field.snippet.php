<?php
/**
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
$name = $modx->getOption('name',$scriptProperties,'');
if (empty($name)) return '';
$debug = $modx->getOption('debug',$scriptProperties,false);
$ffp = $modx->getService('formitfastpack','FormitFastPack',$modx->getOption('ffp.core_path',null,$modx->getOption('core_path').'components/formitfastpack/').'model/formitfastpack/',$scriptProperties);
if (!($ffp instanceof FormitFastPack)) return 'Package not found.';

// Required properties
$type = $modx->getOption('type',$scriptProperties,'text');
$prefix = $modx->getOption('prefix',$scriptProperties,'fi.');
$key_prefix = $modx->getOption('key_prefix',$scriptProperties,'');

// delimiter each field type is bordered by. 
// example: <!-- textarea --> <input type="textarea" name="[[+name]]">[[+current_value]]</input> <!-- textarea -->
$delimiter = '<!-- '.$type.' -->';
// The outer template 
$outer_tpl = $modx->getOption('outer_tpl',$scriptProperties,'field');
// The main template (contains all field types separated by the delimiter)
$tpl = $modx->getOption('tpl',$scriptProperties,'fieldTypes');

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
$option_tpl = $modx->getOption('option_type',$scriptProperties, $default_option_tpl);
$selected_text = $modx->getOption('selected_text',$scriptProperties, $default_selected_text);

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
    $placeholders['type'] = $type;
    $placeholders['prefix'] = $prefix;
    $placeholders['name'] = $name;
    $placeholders['remote_prefix'] = $modx->getOption('type',$scriptProperties,'profile.remote.');
    $placeholders['class'] = $modx->getOption('class',$scriptProperties,'');
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

// Set varying placeholders and properties that cannot be cached
$placeholders['error'] = $error;
$placeholders['current_value'] = $current_value; // ToDo: add caching and take this out to a str_replace function.
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