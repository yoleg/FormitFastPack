<?php /* 
 * fiGenerateReport formit hook. Creates a single placeholder with a complete email report for all non-array fields that have not otherwise been excluded. 
 * 
 * IMPORTANT: if you use array values, call the fiProcessArrays hook BEFORE the fiGenerateReport hook OR use another method to process the arrays
 *
 * Please see forum post: http://modxcms.com/forums/index.php?topic=64656.0
 * 
 * Parameter
 * &figrTpl    - the row template    - default: formReportRow
 * &figrAllValuesLabel    - the name of the placeholder generated for the complete report    - default: figr_values
 * &figrExcludedFields    - a list of all fields to be excluded from the report
 * &figrDefaultExcludedFields - also override the default excluded fields. Unless this option is set, the following additional fields are always excluded: nospam,blank,recaptcha_challenge_field,recaptcha_response_field.
 * 
 * Copyright 2011 Oleg Pryadko (websitezen.com)
 * License GPL v.3 or later
*/ 
$fieldTpl = $modx->getOption('figrTpl',$scriptProperties,'formReportRow');
$allValuesLabel = $modx->getOption('figrAllValuesLabel',$scriptProperties,'figr_values');
$default_excluded_fields = explode(',',$modx->getOption('figrDefaultExcludedFields',$scriptProperties,'nospam,blank,recaptcha_challenge_field,recaptcha_response_field'));
$excluded_fields = explode(',',$modx->getOption('figrExcludedFields',$scriptProperties,''));
$excluded_fields = array_merge($default_excluded_fields,$excluded_fields);
// Unless the default excluded fields have been overriden, also exclude the submitVar
if ($modx->getOption('submitVar',$scriptProperties,false) && !$modx->getOption('figrDefaultExcludedFields',$scriptProperties,true)) {
    $excluded_fields[] = $modx->getOption('submitVar',$scriptProperties,'submit');
}

// Get the FormitFastPack as a service - used to process the template chunk
$ffp = $modx->getService('formitfastpack','FormitFastPack',$modx->getOption('ffp.core_path',null,$modx->getOption('core_path').'components/formitfastpack/').'model/formitfastpack/',$scriptProperties);
if (!($ffp instanceof FormitFastPack)) return 'FFP Package not found.';

// implode fields
$imploded = '';
$allFormFields = $hook->getValues();
foreach ($allFormFields as $fieldName => $fieldValue) {
  if ((!in_array($fieldName,$excluded_fields)) && (!is_array($fieldValue))) {
    $imploded .= $ffp->getChunk($fieldTpl,array(
      'field' => $fieldName,
      'value' => $fieldValue
    ));
  }
}
  
//  generate placeholder
$hook->setValue($allValuesLabel,$imploded);
return true;