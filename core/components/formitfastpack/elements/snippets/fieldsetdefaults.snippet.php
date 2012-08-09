<?php
/**
 * @var MODx $modx
 * @var formitFastPack $ffp
 * @var array $scriptProperties
 */
$ffp = $modx->getService('formitfastpack','FormitFastPack',$modx->getOption('ffp.core_path',null,$modx->getOption('core_path').'components/formitfastpack/').'model/formitfastpack/',$scriptProperties);
if (!($ffp instanceof FormitFastPack)) return 'Package not found.';
$ffp->setConfig($scriptProperties);
return '';
