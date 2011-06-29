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
 * FormitFastPack properties English language file
 *
 * @package FormitFastPack
 * @subpackage lexicon
 */
/* options */
$_lang['ffp_field_debug_desc'] = 'Turn on debugging.';
$_lang['ffp_field_name_desc'] = 'The name of the field.';
$_lang['ffp_field_type_desc'] = 'The field type. Used to decide which subset of the tpl chunk to use. ';
$_lang['ffp_field_prefix_desc'] = 'The prefix used by the FormIt call this field is for - may also work with EditProfile, Register, etc... snippet calls. ';
$_lang['ffp_field_key_prefix_desc'] = 'To use the same field names for different forms on the same page, specify a key prefix. ';
$_lang['ffp_field_outer_tpl_desc'] = 'The outer template chunk, which can be used for any HTML that stays consistent between fields. This is a good place to put your label tags and any wrapping li or div elements that wrap each field in your form. ';
$_lang['ffp_field_tpl_desc'] = 'The template chunk to use for templating all of the various fields. Each field is separated from the others by wrapping it - both above and below - with the following HTML comment: !-- fieldtype --, where fieldtype is the field type. For example, for a text field: !-- text -- input type="[[+type]]" name="[[+name]]" value="[[+current_value]]" / !-- text -- Use the fieldTypesTpl.chunk.tpl in the chunks directory as the starting point. ';
$_lang['ffp_field_inner_override_desc'] = 'Specify your own HTML instead of using the field template. Useful if you want to use the outer_tpl and smart caching but specify your own HTML for the field. ';
$_lang['ffp_field_inner_element_desc'] = 'Similar to inner_override, but accepts the name of an element (a chunk or snippet for example). All of the placeholders and parameters are passed to the chunk. You can also specify an optional chunks_path parameter that allows file-based chunks in the form name.chunk.tpl';
$_lang['ffp_field_inner_element_class_desc'] = 'The element class (modSnippet or modChunk).';
$_lang['ffp_field_inner_element_properties_desc'] = 'A JSON array of additional parameters to pass. Example: {"tpl" : "myChunk"}';
$_lang['ffp_field_error_class_desc'] = 'The name of the class to use for the [[+error_class]] placeholder. This placeholder is generated along with [[+error]] if a FormIt error is found for this field. ';
$_lang['ffp_field_to_placeholders_desc'] = 'If set, will set all of the placeholders as global MODx placeholders as well. ';
$_lang['ffp_field_cache_desc'] = 'Whether to enable smart caching for the field, which tries to cache as much as possible without caching the current_value, error, error_class, or selected/ checked status. ';
$_lang['ffp_field_options_desc'] = 'If your field is a nested or group type, such as checkbox, radio, or select, specify the options in tv-style format like so: Label One==value1||Label Two==value2||Label Three==value3 or Value1||Value2||Value3. The field snippet uses a sub-type (specified by option_type) to template the options. Setting this parameter causes smart caching to be enabled by default and "selected" or "checked" to be added to the currently selected option, as appropriate. See "mark_slected" and "cache" parameters. ';
$_lang['ffp_field_option_type_desc'] = 'Specify the field type used for each option. .';
$_lang['ffp_field_selected_text_desc'] = 'The te';
$_lang['ffp_field_mark_selected_desc'] = 'If left blank or set to zero, disables option marking. You can also use this to specify a string to use for the marker (such as \'checked="checked"\'. By default if "options" or an options override is specified, the field snippet will add a marker such as \' checked="checked"\' or (if the field type is "select") \' selected="selected"\' in the right place, assuming you are using HTML syntax for value (value="X"). This is a lot faster than using FormItIsSelected or FormItIsChecked.   ';
$_lang['ffp_field_options_override_desc'] = 'You can specify your own HTML instead of using the &options parameter to generate options. For example, you might decide to pass in option value="something" data="something"hello/option if the type is set to "select". It otherwise acts exactly as if you had specified the options parameter for marking and caching purposes. ';
$_lang['ffp_field_options_element_desc'] = 'Similar to options_override, but accepts the name of an element (a chunk or snippet for example). All of the placeholders and parameters are passed to the chunk. You can also specify an optional chunks_path parameter that allows file-based chunks in the form name.chunk.tpl';
$_lang['ffp_field_options_element_class_desc'] = 'The element class (modSnippet or modChunk).';
$_lang['ffp_field_options_element_properties_desc'] = 'A JSON array of additional parameters to pass. Example: {"tpl" : "myChunk"}';
