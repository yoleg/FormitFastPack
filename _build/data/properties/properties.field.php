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
$properties = array(
    array(
        'name' => 'debug',
        'desc' => 'ffp_field_debug_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '1',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'name',
        'desc' => 'ffp_field_name_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'type',
        'desc' => 'ffp_field_type_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'text',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'prefix',
        'desc' => 'ffp_field_prefix_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'fi.',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'key_prefix',
        'desc' => 'ffp_field_key_prefix_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'outer_tpl',
        'desc' => 'ffp_field_outer_tpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'field',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'tpl',
        'desc' => 'ffp_field_tpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'fieldTypes',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'inner_override',
        'desc' => 'ffp_field_inner_override_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'inner_chunk',
        'desc' => 'ffp_field_inner_chunk_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'error_class',
        'desc' => 'ffp_field_error_class_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'error',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'to_placeholders',
        'desc' => 'ffp_field_to_placeholders_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'false',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'options',
        'desc' => 'ffp_field_options_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'option_type',
        'desc' => 'ffp_field_option_type_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'selected_text',
        'desc' => 'ffp_field_selected_text_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'mark_selected',
        'desc' => 'ffp_field_mark_selected_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'true',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'options_override',
        'desc' => 'ffp_field_options_override_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'formitfastpack:properties',
    ),
    array(
        'name' => 'options_chunk',
        'desc' => 'ffp_field_options_chunk_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'formitfastpack:properties',
    ),
);

return $properties;