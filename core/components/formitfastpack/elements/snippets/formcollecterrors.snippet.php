<?php
/**
 * Snippet: collectFormErrors
 * Author: Oleg Pryadko (oleg@websitezen.com)
 * License: GPL v. 3
 */
$output = '';

$ffp = $modx->getService('formitfastpack','FormitFastPack',$modx->getOption('ffp.core_path',null,$modx->getOption('core_path').'components/formitfastpack/').'model/formitfastpack/',$scriptProperties);
if (false) $ffp = new FormitFastPack($modx); // never used - debug only
if (false) $modx = new modX(''); // never used - debug only
if (!($ffp instanceof FormitFastPack)) return 'Package not found.';
$defaults = $ffp->getConfig();
$config = array_merge($defaults,$scriptProperties);

// Display Errors
$prefix = $modx->getOption('prefix',$config,'fi.');
$error_prefix = $modx->getOption('error_prefix',$config,'');
$error_prefix = $error_prefix ? $error_prefix : $prefix.'error.';
$output_array = array();

$fields = $modx->getOption('fields',$config,'');
if (!empty($fields)) {
    $fields = explode(',',$fields);
    // get the errors for the fields set by "fields" parameter
    foreach ($fields as $key) {
        $output_array[$key] = $modx->getPlaceholder($error_prefix.$key);
    }
} else {
    // or get all errors set for the error_prefix
    $global_placeholders = $modx->placeholders;
    foreach ($global_placeholders as $key => $value) {
        if (strpos($key,$error_prefix) === 0) {
            $new_key = str_replace($error_prefix,'',$key);
            $output_array[$new_key] = $value;
        }
    }
}
if (empty($output_array)) return '';

$tpl = $modx->getOption('tpl',$config,'');
if ($tpl) {
    // use a template chunk to output errors
    foreach ($output_array as $key => $value) {
        $output .= $ffp->getChunk($tpl,array('key' => $key, 'value' => $value));
    }
} else {
    // or just use glue and separator
    $glue = $modx->getOption('glue',$config,', ');
    $separator = $modx->getOption('separator',$config,': ');
    foreach ($output_array as $key => $value) {
        $output_array[$key] = ($key.$separator.$value);
    }
    $output = implode($glue, $output_array);
}

// set placeholder or return output
$toPlaceholder = $modx->getOption('toPlaceholder',$config,'');
if ($toPlaceholder) {
    $modx->setPlaceholder($toPlaceholder,$output);
    return '';
}
return $output;
