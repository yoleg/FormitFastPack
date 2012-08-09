<?php /**
 * FormitFastPack
 *
 * Copyright 2010-11 by Oleg Pryadko <oleg@websitezen.com>
 *
 * This file is part of FormitFastPack, a FormIt helper package for MODx Revolution.
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
/* Info may be slightly out of date.
 *
 * General Parameters:
 *
 * debug - turn on debugging (default: false)
 * name - the name of the field (default: '')
 * default_value - the default value of the field (default: '')
 * type - the field type. Used to decide which subset of the tpl chunk to use. (default: 'text')
 * outer_type - Override the type for the outer template. (default: '')
 * prefix - the prefix used by the FormIt call this field is for - may also work with EditProfile, Register, etc... snippet calls. (default: 'fi.')
 * error_prefix - Override the calculated prefix for field errors. Example: 'error.' (default: '')
 * key_prefix - To use the same field names for different forms on the same page, specify a key prefix. (default: '')
 * tpl - The template chunk to use for templating all of the various fields. Each field is separated from the others by wrapping it - both above and below - with the following HTML comment: <!-- fieldtype -->, where fieldtype is the field type. For example, for a text field: <!-- text --> <input type="[[+type]]" name="[[+name]]" value="[[+current_value]]" /> <!-- text --> Use the fieldTypesTpl.chunk.tpl in the chunks directory as the starting point. NEW: if the delimiter is not found, it tries using the default delimiter (<!-- default -->) if it is present. If neither the type delimiter or the default delimiter is present, it returns the entire chunk template. (default: 'fieldTypesTpl')
 * outer_tpl - The outer template chunk, which can be used for any HTML that stays consistent between fields. This is a good place to put your <label> tags and any wrapping <li> or <div> elements that wrap each field in your form. NEW: can now can use delimiters like the tpl.  (default: 'fieldWrapTpl')
 * chunks_path - *NOT AVAILABLE IN CURRENT VERSION* - Specify a path where file-based chunks are stored in the format lowercasechunkname.chunk.tpl, which will be used if the chunk is not found in the database.
 * inner_html - Specify your own HTML instead of using the field template. Useful if you want to use the outer_tpl and smart caching but specify your own HTML for the field. (default: '')
 * inner_element - Similar to inner_html, but accepts the name of an element (chunk, snippet...). All of the placeholders and parameters are passed to the element. Note: the inner_element override is not as useful as the options_element, which benefits much more from the smart caching. (default: '')
 * inner_element_class - Specify the classname of the element (such as modChunk, modSnippet, etc...). If using modChunk, you can specify an additional chunks_path parameter to allow file-based chunks. (default: 'modChunk')
 * inner_element_properties - A JSON array of properties to be passed to the element when it is processed. (default: '')
 * error_class - The name of the class to use for the [[+error_class]] placeholder. This placeholder is generated along with [[+error]] if a FormIt error is found for this field. (default: 'error')
 * to_placeholders - If set, will set all of the placeholders as global MODx placeholders as well. (default: false)
 * cache - Whether to enable smart caching for the field, which tries to cache as much as possible without caching the current_value, error, error_class, or selected/ checked status. Default: auto.
 *
 * Nested or Boolean Fields Parameters
 *
 * options - If your field is a nested or group type, such as checkbox, radio, or select, specify the options in tv-style format like so: Label One==value1||Label Two==value2||Label Three==value3 or Value1||Value2||Value3. The field snippet uses a sub-type (specified by option_type) to template the options. Setting this parameter causes smart caching to be enabled by default and "selected" or "checked" to be added to the currently selected option, as appropriate. See "mark_slected" and "cache" parameters. (default: '')
 * option_type - Specify the field type used for each option. If left blank, defaults to "bool" if &type is checkbox or radio and "option" if &type is select). (default: '')
 * options_html - same as inner_html, but for the options_html placeholder. Allows you to use your own custom elements while benefiting from the speed gains of the smart caching.  (default: '')
 * options_element - same as inner_element, but for the options_html placeholder (default: '')
 * options_element_class - same as inner_element_type, but for the options_html placeholder (default: 'modChunk')
 * options_element_properties - same as inner_element_properties, but for the options_html placeholder (default: '')
 * mark_selected - If left blank or set to zero, disables option marking. By default if "options" or an options override is specified, the field snippet will add a marker such as ' checked="checked"' or (if the field type is "select") ' selected="selected"' in the right place, assuming you are using HTML syntax for value (value="X"). This is a lot faster than using FormItIsSelected or FormItIsChecked.   (default: true)
 * selected_text - The text to mark selected options with (such as checked="checked" or selected="selected"). If left blank or set to false, defaults to checked="checked" unless the field type is "select", in which case it uses selected="selected". (default: '')
 *
 * Custom Parameters
 *
 * You can add an infinite number of custom parameters, all of which will be set as placeholders in the template chunks.
 * Example parameters: class, req (required), note, help
 * Example parameter usage: 
 *  - class="[[+type]] [[+class]][[+error_class]][[+req:notempty=` required`]]"
 *  - label="[[+label:default=`[[+name:replace=`_== `:ucwords]]`]][[+req:notempty=` *`]]"
 *  - [[+note:notempty=`<span class="notice">[[+note]]</span>`]]
 * 
 * Placeholders:
 * 
 * The values of all the parameters listed above and any other parameters you pass to the snippet are automatically set as placeholders. 
 * This allows you to add custom placeholders such as "required", "class", etc.... (see custom parameters above)
 * In addition, the following special placeholders are generated:
 *
 * inner_html - Used in the outer_tpl to position the generated content, which will vary by field type. Simple example: <li>[[+inner_html]]</li>
 * options_html - Used in the tpl to position the options html (only when using &options or an options override). Example: <select name="[[+name]]">[[+options_html]]</select>
 * current_value - The value of the FormIt value for the field name. Exactly the same as writing [[!fi.fieldname]] for each fieldname (if the prefix is fi.). Never gets cached.
 * error - The value of the FormIt error message for the field name, if one is found. Exactly the same as writing [[!fi.error.fieldname]] for each fieldname (if the prefix is fi.). Never gets cached.
 * error_class - set to the value of the error_class parameter (default is " error") ONLY if a FormIt error for the field name is found. Exactly the same as using [[+error:notempty=` error`]].
 * key - A unique but human-friendly identifier for each field or sub-field (useful for HTML id attributes). Generated from the key_prefix, prefix, field name, and (only if using an option field) value.
 *
 */
/**
 * @var MODx $modx
 * @var array $scriptProperties
 */

$debug = $modx->getOption('debug',$scriptProperties,false);
/** @var $ffp FormitFastPack */
$ffp_core_path = $modx->getOption('formitfastpack.core_path', null, $modx->getOption('core_path') . 'components/formitfastpack/');
/** @define "$ffp_core_path" "../../" */
require_once $ffp_core_path.'model/formitfastpack/ffpfield.class.php';

// load ffp service
$ffp = $modx->getService('formitfastpack','FormitFastPack', $ffp_core_path .'model/formitfastpack/',$scriptProperties);
if (!($ffp instanceof FormitFastPack)) return 'Package not found.';

// load defaults
$defaults = $ffp->getConfig();
$config = array_merge($defaults,$scriptProperties);

$disable_cache = false;
if ($disable_cache) $config['cache'] = 0;

// create ffpField
$field = new ffpField($ffp);

// todo: move defaults to snippet?
$field->setSettings($config);
$output = $field->process();

return $output;
