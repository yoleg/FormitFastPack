<?php
/* 
 * fiGenerateReport formit hook. Creates a single placeholder with a complete email report for all fields that have not otherwise been excluded. 
 * 
 * IMPORTANT: if you use array values, call the fiProcessArrays hook BEFORE the fiGenerateReport hook OR use another method to process the arrays
 *
 * Please see forum post: http://modxcms.com/forums/index.php?topic=64656.0
 * 
 * Parameter
 * &figrTpl	- the row template	- default: formReportRow
 * &figrAllValuesLabel	- the name of the placeholder generated for the complete report	- default: figr_values
 * &figrExcludedFields	- a list of all fields to be excluded from the report	- default: nospam,blank,recaptcha_challenge_field,recaptcha_response_field
 * 
 * Copyright 2011 Oleg Pryadko (websitezen.com)
 * License GPL v.3 or later
*/ 
$fieldTpl = $modx->getOption('figrTpl',$scriptProperties,'formReportRow');
$allValuesLabel = $modx->getOption('figrAllValuesLabel',$scriptProperties,'figr_values');
$excludedFields = explode(',',$modx->getOption('figrExcludedFields',$scriptProperties,'nospam,blank,recaptcha_challenge_field,recaptcha_response_field'));
$excludedFields[] = $modx->getOption('submitVar',$scriptProperties,'submit');

// implode fields
$imploded = '';
$allFormFields = $hook->getValues();
foreach ($allFormFields as $fieldName => $fieldValue) {
  if ((!in_array($fieldName,$excludedFields)) && (!is_array($fieldValue))) {
    $imploded .= $modx->getChunk($fieldTpl,array(
      'field' => $fieldName,
      'value' => $fieldValue
    ));
  }
}
  
//  generate placeholder
$hook->setValue($allValuesLabel,$imploded);
return true;