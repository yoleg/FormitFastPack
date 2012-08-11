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
/**
 * Each form field field is one snippet call.
 *
 * Example Usage:
 * [[!formIt? &prefix=`myprefix.` &submitVar=`submitForm`]]
 * [[!fieldSetDefaults? &prefix=`myprefix.` &resetDefaults=`1`]]
 * [[!field &name=`full_name` &type=`text` &class=`required`]]
 * [[!field &name=`favorite_color` &type=`checkbox` &options=`Blue||Red||Yellow`]]
 * [[!field &name=`location` &type=`select` &label=`Where are you from?` &options=`United States==US||New Zealand==NZ||Never Never Land==NNL`]]
 * [[!field &name=`message` &type=`textarea`]]
 * [[!field &name=`submitForm` &type=`submit` &label=`&nbsp;` &message=`Submit Form`]]
 *
 * Chunks:
 * You can override the chunk templates by creating two chunks called fieldTypesTpl and fieldWrapTpl.
 * The template chunks use SPECIAL DELIMITERS. See the example chunks in core/components/formitfastpack/elements/chunks/
 * You can use different chunk names by changing the &tpl and &outer_tpl parameters, respectively.
 *
 * Parameters.
 * All parameters passed to the snippet (including custom ones) will be set as placeholders for processing the template chunks.
 * See property set in the fieldPropSetExample snippet for up-to-date defaults and descriptions.
 *
 * &cache_desc: Whether to enable smart caching for the field, which tries to cache as much as possible without caching the current_value, error, error_class, or selected/ checked status. Set to empty or zero to disable, or leave at auto for future compatibility.
 * &custom_ph_desc: (OPTIONAL) Speed improvement. Listing your custom placeholders here or in a fieldSetDefaults call speeds up chunk processing by setting a blank default value. You do not need to list placeholders here that already have a value set somewhere else.
 * &debug_desc: Turn on debugging.
 * &default_delimiter_desc: If no outer_type or type is specified, this will be used as the type for the purposes of processing the template chunk.
 * &default_value_desc: The default value to use if no value is found.
 * &error_class_desc: The name of the class to use for the [[+error_class]] placeholder. This placeholder is generated along with [[+error]] if a FormIt error is found for this field.
 * &error_prefix_desc: Usually automatically determined, you can override the prefix that is prepended to the field name for the purpose of getting the field errors from the MODX placeholders.
 * &inner_element_class_desc: The element class (modSnippet or modChunk).
 * &inner_element_desc: Similar to inner_override, but accepts the name of an element (a chunk or snippet for example). All of the placeholders and parameters are passed to the chunk. You can also specify an optional chunks_path parameter that allows file-based chunks in the form name.chunk.tpl
 * &inner_element_properties_desc: A JSON array of additional parameters to pass. Example: {"tpl" : "myChunk"}
 * &inner_html_desc: Specify your own HTML instead of using the field template. Useful if you want to use the outer_tpl and smart caching but specify your own HTML for the field.
 * &key_prefix_desc: To use the same field names for different forms on the same page, specify a key prefix.
 * &mark_selected_desc: If left blank or set to zero, disables option marking. You can also use this to specify a string to use for the marker (such as \'checked="checked"\'. By default if "options" or an options override is specified, the field snippet will add a marker such as \' checked="checked"\' or (if the field type is "select") \' selected="selected"\' in the right place, assuming you are using HTML syntax for value (value="X"). This is a lot faster than using FormItIsSelected or FormItIsChecked.
 * &name_desc: The name of the field.
 * &options_delimiter_inner_desc: The delimiter used to separate the label from the value in the options parameter.
 * &options_delimiter_outer_desc: The delimiter used to separate each option in the options parameter.
 * &options_desc: If your field is a nested or group type, such as checkbox, radio, or select, specify the options in tv-style format like so: Label One==value1||Label Two==value2||Label Three==value3 or Value1||Value2||Value3. The field snippet uses a sub-type (specified by option_type) to template the options. Setting this parameter causes smart caching to be enabled by default and "selected" or "checked" to be added to the currently selected option, as appropriate. See "mark_slected" and "cache" parameters.
 * &options_element_class_desc: The element class (modSnippet or modChunk).
 * &options_element_desc: Similar to options_override, but accepts the name of an element (a chunk or snippet for example). All of the placeholders and parameters are passed to the chunk. You can also specify an optional chunks_path parameter that allows file-based chunks in the form name.chunk.tpl
 * &options_element_properties_desc: A JSON array of additional parameters to pass. Example: {"tpl" : "myChunk"}
 * &options_html_desc: You can specify your own HTML instead of using the &options parameter to generate options. For example, you might decide to pass in option value="something" data="something" if the type is set to "select". It otherwise acts exactly as if you had specified the options parameter for marking and caching purposes.
 * &option_type_desc: Specify the field type used for each option. Usually automatically determined.
 * &outer_tpl_desc: The outer template chunk, which can be used for any HTML that stays consistent between fields. This is a good place to put your label tags and any wrapping li or div elements that wrap each field in your form.
 * &outer_type_desc: The outer type. The outer template chunk can divided just like the fieldTypesTpl, but with names unrelated to field types.
 * &prefix_desc: The prefix used by the FormIt call this field is for - may also work with EditProfile, Register, etc... snippet calls. The prefix is also used for other purposes, such as getting and setting the value in the session.
 * &selected_text_desc: The text to use for marking options as selected, checked, or whatnot. Usually automatically determined based on the type, but this can be useful if you have a custom type or want something else.
 * &set_type_ph_desc: Sets these placeholders to either true or false depending on whether they match this field type. So, if the field type is text and text is listed here, the placeholder "text" will be set to 1, and the other types listed will be set to an empty string.
 * &to_placeholders_desc: If set, will set all of the placeholders as global MODx placeholders as well.
 * &tpl_desc: The template chunk to use for templating all of the various fields. Each field is separated from the others by wrapping it - both above and below - with the following HTML comment: !-- fieldtype --, where fieldtype is the field type. For example, for a text field: !-- text -- input type="[[+type]]" name="[[+name]]" value="[[+current_value]]" / !-- text -- Use the fieldTypesTpl.chunk.tpl in the chunks directory as the starting point.
 * &type_desc: The field type. Used to decide which subset of the tpl chunk to use.
 * &use_cookies_desc: If no value is found, get the value from the $_COOKIES global array. The array key is use_cookies_prefix+prefix+name.
 * &use_cookies_prefix_desc: The prefix to use for cookie storage.
 * &use_formit_desc: Get the value from MODX placeholders such as those set by formit. The placeholder key is PREFIX+NAME (so you can make this compatible with Login and similar snippets by changing the prefix).
 * &use_get_desc: If no value is found, get the value from the $_GET global array. The array key is the name of the field.
 * &use_request_desc: If no value is found, get the value from the $_REQUEST global array. The array key is the name of the field.
 * &use_session_desc: If no value is found, get the value from the $_SESSION global array. The array key is use_session_prefix+prefix+name.
 * &use_session_prefix_desc: The prefix to use for session storage. *
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
