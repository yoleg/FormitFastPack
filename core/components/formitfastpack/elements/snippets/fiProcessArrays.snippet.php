<?php/* 
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
$field_suffix = $modx->getOption('fipaFieldSuffix',$scriptProperties,'_values');
$separator = $modx->getOption('fipaValueSeparator',$scriptProperties,', ');
$excluded_fields = explode(',',$modx->getOption('fipaExcludedFields',$scriptProperties,''));
$allFormFields = $hook->getValues();
foreach ($allFormFields as $fieldName => $fieldValue) {
  if (is_array($fieldValue) && !in_array($fieldName,$excluded_fields)) {
    $imploded = '';
    $count=0;
    foreach ($fieldValue as $value) {
      if (!empty($value)) {
        if ($count) {$imploded .= $separator;}
        $imploded .= $value;
        $count++;
      }
    }
    $hook->setValue($fieldName.$field_suffix,$imploded);
  }
}
return true;