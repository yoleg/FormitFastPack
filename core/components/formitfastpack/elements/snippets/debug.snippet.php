<?php
/**
 * Outputs debugging info for MODx revolution
 * Author: Oleg Pryadko (oleg@websitezen.com)
 * License: public domain
 */
$output = '';

// not used - for syntax checking only
global $modx;
if (false) $modx = new modX('');

// Display Errors
error_reporting(E_ALL); ini_set('display_errors',true);
$modx->setLogTarget('HTML');
$modx->setLogLevel(modx::LOG_LEVEL_WARN);

// display non-system placeholders
$output_array = array();
$array = $modx->placeholders;
foreach ($array as $key => $value) {
    if (strpos($key,'+') !== 0) $output_array[$key] = $value;
}
$output .= '<pre>';
$output .= '<br /><br />Placeholders: '.htmlentities(print_r($output_array,1));

// display global arrays
if (!empty($_REQUEST)) {
    $output .= '<br /><br />REQUEST: '.htmlentities(print_r($_REQUEST,1));
}
if (!empty($_POST)) {
    $output .= '<br /><br />POST: '.htmlentities(print_r($_POST,1));
}
if (!empty($_GET)) {
    $output .= '<br /><br />GET: '.htmlentities(print_r($_REQUEST,1));
}
$output .= '</pre>';

return $output;

