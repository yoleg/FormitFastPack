<?php
/**
 * Default properties for the field snippet
 *
 * June 2011
 *
 * @package formitfastpack
 * @subpackage build
 */
/*
 * The description fields should match
 * keys in the lexicon property file
 * */
// Property set for field snippet.
$defaults = array(
    'debug' => false,
    'cache' => 'auto',
    'default_value' => '',
    'name' => '',
    'type' => 'text',
    'outer_type' => '',
    'prefix' => 'fi.',
    'error_prefix' => '', // add "error." to default prefix
    'key_prefix' => '',
    // delimiter each field type is bordered by.
    // example: <!-- textarea --> <input type="textarea" name="[[+name]]">[[+current_value]]</input> <!-- textarea -->
    'delimiter_template' => '<!-- [[+type]] -->',
    'default_delimiter' => 'default',
    'outer_tpl' => 'fieldWrapTpl',
    // The main template (contains all field types separated by the delimiter)
    'tpl' => 'fieldTypesTpl',
    'options' => '',
    'options_delimiter_outer' => '||',
    'options_delimiter_inner' => '==',
    'option_type' => '',
    'selected_text' => '',
    'custom_ph' => 'class,multiple,array,header,default,class,outer_class,label,note,note_class,size,title,req,message,clear_message',
    'set_type_ph' => 'text,textarea,checkbox,radio,select',
    // inner and options should be identical
    'options_html' => '',
    'options_element' => '',
    'options_element_class' => 'modChunk',
    'options_element_properties' => '[]',
    'inner_html' => '',
    'inner_element' => '',
    'inner_element_class' => 'modChunk',
    'inner_element_properties' => '[]',
    'use_formit' => 1,
    'use_get' => 0,
    'use_request' => 0,
    'use_session' => 0,
    'use_cookies' => 0,
    'error_class' => 'error',
    'mark_selected' => 1,
    'to_placeholders' => 0,
    'use_session_prefix' => 'field.',
    'use_cookies_prefix' => 'field.',
);


$properties = array();
foreach($defaults as $k => $v) {
    $properties[] = array(
        'name' => $k,
        'desc' => "ffp_field_{$k}_desc",
        'type' => is_string($v) ? 'textfield' : 'combo-boolean',
        'options' => '',
        'value' => $v,
        'lexicon' => 'formitfastpack:properties',
    );
}
return $properties;
