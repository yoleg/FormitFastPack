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

$ffp = $modx->getService('formitfastpack','FormitFastPack',$modx->getOption('ffp.core_path',null,$modx->getOption('core_path').'components/formitfastpack/').'model/formitfastpack/',$scriptProperties);
if (!($ffp instanceof FormitFastPack)) return 'Package not found.';

// Required properties
$type = $modx->getOption('type',$scriptProperties,'text');
$prefix = $modx->getOption('prefix',$scriptProperties,'fi.');
$remote_prefix = $modx->getOption('type',$scriptProperties,'profile.remote.');
$error_class = ' '.$modx->getOption('error_class',$scriptProperties,'error');
$delimiter = '<!-- '.$type.' -->';
// $delimiter = '<!-- textarea -->';
// Templates
$tpl = $modx->getOption('tpl',$scriptProperties,'field');
$inner_tpl = $modx->getOption('inner_tpl',$scriptProperties,'fieldTypes');

$multi_types = explode(',',$modx->getOption('multiple_value_fields',$scriptProperties,'radio,checkbox,select,multiselect'));

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

// Miscellaneous
$placeholders['default'] = $modx->getOption('default',$scriptProperties,'');
$placeholders['value'] = $modx->getOption('value',$scriptProperties,'');
$placeholders['req'] = $modx->getOption('req',$scriptProperties,'');
$placeholders['options'] = $modx->getOption('options',$scriptProperties,'');
$placeholders['label'] = $modx->getOption('label',$scriptProperties,'');
$placeholders['message'] = $modx->getOption('message',$scriptProperties,'');

$placeholders = array_merge($scriptProperties,$placeholders);
$placeholders['input_field'] = $ffp->getChunk($inner_tpl,$placeholders,$delimiter);
if ($tpl) {
    $output = $ffp->getChunk($tpl,$placeholders);
} else {
    return $placeholders['input_field'];
}
return $output;