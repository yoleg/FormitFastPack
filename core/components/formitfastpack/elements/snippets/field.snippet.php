<?php
/**
 * FormitFastPack
 *
 * Copyright 2010-11 by Oleg Pryadko <oleg@websitezen.com>
 *
 * This file is part of FormitFastPack, a geougmedia integration for MODx Revolution.
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

$options_delimiter = '||';
$options_inner_delimiter = '==';

// For checkboxes, radios, selects, etc... that require inner fields, parse options
$inner_static = $modx->fromJSON($modx->getOption('ffp.inner_options_static',null,'[]'));
if (empty($inner_static)) {
    $inner_static = array();
    $inner_static['checkbox'] = array('option_tpl' => 'bool');
    $inner_static['radio'] = array('option_tpl' => 'bool');
    $inner_static['select'] = array('option_tpl' => 'option');
}
$options = $modx->getOption('options',$scriptProperties,'');


// Required properties
$type = $modx->getOption('type',$scriptProperties,'text');
$prefix = $modx->getOption('prefix',$scriptProperties,'fi.');
$remote_prefix = $modx->getOption('type',$scriptProperties,'profile.remote.');
$error_class = ' '.$modx->getOption('error_class',$scriptProperties,'error');

// delimiter each field type is bordered by. 
// example: <!-- textarea --> <input type="textarea" name="[[+name]]">[[+current_value]]</input> <!-- textarea -->
$delimiter = '<!-- '.$type.' -->';
// The outer template 
$outer_tpl = $modx->getOption('outer_tpl',$scriptProperties,'field');
// The main template (contains all field types separated by the delimiter)
$tpl = $modx->getOption('tpl',$scriptProperties,'fieldTypes');

// Grab existing placeholders
$error = $modx->getPlaceholder($prefix.'error.'.$name);
$current_value = $modx->getPlaceholder($prefix.$name);

// Set placeholders
$placeholders = array();
$placeholders['type'] = $type;
$placeholders['prefix'] = $prefix;
$placeholders['name'] = $name;
$placeholders['remote_prefix'] = $remote_prefix;
$placeholders['class'] = $modx->getOption('class',$scriptProperties,'');
$placeholders['error'] = $error;
$placeholders['error_class'] = $error ? $error_class : '';
$placeholders['current_value'] = $current_value;

// All other placeholders equal scriptProperties
$placeholders = array_merge($scriptProperties,$placeholders);

// Set overrides for options and inner_html
$options_html = $modx->getOption('options_override',$scriptProperties,'');
$inner_html = $modx->getOption('inner_override',$scriptProperties,'');
$override_type = $modx->getOption('override_type',$scriptProperties,'');
if ($override_type == 'modChunk') {
    $options_html = $options_html ? $modx->getChunk($options_html,$placeholders) : '';
    $inner_html = $inner_html ? $modx->getChunk($inner_html,$placeholders) : '';
}

if (empty($inner_html)) {
    // Parse options for checkboxes, radios, etc... if &options is passed
    if ($options && !$options_html) {
        $inner_delimiter = '<!-- '.$inner_static[$type]['option_tpl'].' -->';
        $options_html = '';
        $options = explode($options_delimiter,$options);
        foreach ($options as $option) {
            $option_array =  explode($options_inner_delimiter,$option);
            $inner_array = array();
            $inner_array['label'] = $option_array[0];
            $inner_array['value'] = isset($option_array[1]) ? $option_array[1] : $option_array[0];
            $inner_placeholders = array_merge($placeholders,$inner_array);
            $options_html .= $ffp->getChunk($tpl,$inner_placeholders,$inner_delimiter);
        }
    }
    $placeholders['options_html'] = $options_html;
    // If the inner_html has not already been specified, use the template and field type to get it
    $inner_html = $ffp->getChunk($tpl,$placeholders,$delimiter);
}
$placeholders['inner_html'] = $inner_html;

// If outer template is set, use it. Otherwise return the $inner_html
if ($outer_tpl) {
    $output = $ffp->getChunk($outer_tpl,$placeholders);
} else {
    return $placeholders['inner_html'];
}
return $output;