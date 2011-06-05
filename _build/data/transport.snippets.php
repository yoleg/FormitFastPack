<?php
/**
 * FormitFastPack
 *
 * Copyright 2011 by Oleg Pryadko (websitezen.com)
 *
 * This file is part of FormitFastPack, a FormIt helper pack for MODx Revolution.
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
 *
 * @package FormitFastPack
 */
/**
 * @package FormitFastPack
 * @subpackage build
 */
$snippets = array();

$idx = 1;
$name_lower = 'field';
$snippets[$idx]= $modx->newObject('modSnippet');
$snippets[$idx]->fromArray(array(
    'id' => $idx,
    'name' => 'field',
    'description' => 'A form-field generator compatible with FormIt.',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/'.$name_lower.'.snippet.php'),
));
$properties = include $sources['data'].'properties/properties.'.$name_lower.'.php';
$snippets[$idx]->setProperties($properties);

$idx++;
$name_lower = 'fiprocessarrays';
$snippets[$idx]= $modx->newObject('modSnippet');
$snippets[$idx]->fromArray(array(
    'id' => $idx,
    'name' => 'fiProcessArrays',
    'description' => 'A FormIt hook which transforms array values into concatenated strings.',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/'.$name_lower.'.snippet.php'),
));

$idx++;
$name_lower = 'figeneratereport';
$snippets[$idx]= $modx->newObject('modSnippet');
$snippets[$idx]->fromArray(array(
    'id' => $idx,
    'name' => 'fiGenerateReport',
    'description' => 'A FormIt hook which generates an email report by iterating all field names and values through a row template.',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/'.$name_lower.'.snippet.php'),
));

return $snippets;