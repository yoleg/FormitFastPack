<?php
/*
 * fiProcessArrays formit hook. Processes all values stored as arrays and implodes them.
 * 
 * options: 
 * &fipaFieldSuffix - the suffix to add to the array name - default: "_values" - example resulting placeholder: [[+colors_values]]
 * &fipaValueSeparator - the separator to use between the values - default: ", "
 * &fipaExcludedFields - a comma-separated list of field names to exclude
 * 
 * Copyright Oleg Pryadko (websitezen.com) 2011
 * License GPL v.3 or later 
*/
/**
 * @var MODx $modx
 * @var array $scriptProperties
 */
$field_suffix = $modx->getOption('fipaFieldSuffix',$scriptProperties,'_values');
$separator = $modx->getOption('fipaValueSeparator',$scriptProperties,', ');
$list = $modx->getOption('fipaList',$scriptProperties,false);
$excluded_fields = explode(',',$modx->getOption('fipaExcludedFields',$scriptProperties,''));
$allFormFields = $hook->getValues();
foreach ($allFormFields as $fieldName => $fieldValue) {
  if (is_array($fieldValue) && !in_array($fieldName,$excluded_fields)) {
    $imploded = '';
    $count=0;
    foreach ($fieldValue as $value) {
      if (!empty($value)) {
        if ($list) {$imploded .= '<li>';}
        else if ($count) {$imploded .= $separator;}
        $imploded .= $value;
        if ($list) {$imploded .= '</li>';}
        $count++;
      }
    }
    if ($list) {$imploded = '<ul>'.$imploded.'</ul>';}
    $hook->setValue($fieldName.$field_suffix,$imploded);
  }
}
return true;
