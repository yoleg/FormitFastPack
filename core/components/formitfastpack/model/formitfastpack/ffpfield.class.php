<?php
/**
 * @package:
 * @author: Oleg Pryadko (oleg@websitezen.com)
 * @createdon: 3/28/12
 * @license: GPL v.3 or later
 */
class ffpField {
    /** @var FormitFastPack A reference to the FormitFastPack object. */
    public $ffp = null;
    /** @var modX A reference to the modX object. */
    public $modx = null;
    /** @var array A collection of properties to adjust behaviour. */
    public $config = array();
    /** @var array A collection of defaults */
    public $defaults = array();
    /** @var array Working copies of the various areas of output. */
    public $html = array();
    /** @var array The array of placeholders to set for chunk processing. */
    public $placeholders = array();
    /** @var bool Automatically detected. If true, the inner html is parsed first and set as an inner_html placeholder for the outer_html. Otherwise, this extra step is skipped with a str_replace for the [[+inner_html]] placeholder. */
    public $double_processing_needed = false;

    function __construct(FormitFastPack &$ffp, array $config = array()) {
        $this->ffp =& $ffp;
        $this->modx =& $ffp->modx;
        $this->config = $config;
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
        $this->defaults = $defaults;
    }

    public function setOption($key, $value) {
        $this->config[$key] = $value;
    }

    public function setSettings(array $settings) {
        foreach ($this->defaults as $key => $default) {
            $this->config[$key] = $this->modx->getOption($key, $settings, $default);
        }
        $this->config = array_merge($settings, $this->config);
        $this->calculateConfig();
    }

    public function calculateConfig() {
        // delimiters
        $this->config['delimiter'] = str_replace('[[+type]]', $this->config['type'], $this->config['delimiter_template']);
        $this->config['default_delimiter'] = str_replace('[[+type]]', $this->config['default_delimiter'], $this->config['delimiter_template']);
        // default to the field type for outer type. If the delimiter is not found, it will use the default delimiter. If the default delimiter is not found, it will use the entire outer_tpl.
        $this->config['outer_delimiter'] = empty($this->config['outer_type']) ? $this->config['delimiter'] : str_replace('[[+type]]', $this->config['outer_type'], $this->config['delimiter_template']);

        // For checkboxes, radios, selects, etc... that require inner fields, parse options
        // Set defaults for the options of certain field types and allow to override from a system settings JSON array
        $inner_static = $this->modx->fromJSON($this->modx->getOption('ffp.inner_options_static', null, '[]'));
        if (empty($inner_static)) {
            $inner_static = array();
            $inner_static['bool'] = array('option_tpl' => 'bool', 'selected_text' => ' checked="checked"');
            $inner_static['checkbox'] = array('option_tpl' => 'bool', 'selected_text' => ' checked="checked"');
            $inner_static['radio'] = array('option_tpl' => 'bool', 'selected_text' => ' checked="checked"');
            $inner_static['select'] = array('option_tpl' => 'option', 'selected_text' => ' selected="selected"');
        }
        $inner_static['default'] = isset($inner_static['default']) ? $inner_static['default'] : array('option_tpl' => '', 'selected_text' => ' checked="checked" selected="selected"');
        // options templates
        $this->config['default_option_tpl'] = isset($inner_static[$this->config['type']]['option_tpl']) ? $inner_static[$this->config['type']]['option_tpl'] : $inner_static['default']['option_tpl'];
        $this->config['default_selected_text'] = isset($inner_static[$this->config['type']]['selected_text']) ? $inner_static[$this->config['type']]['selected_text'] : $inner_static['default']['selected_text'];
        $this->config['inner_static'] = $inner_static;

        /*      CACHING         */
        // See if caching is set system-wide or in the scriptProperties
        $cache = $this->config['cache'];
        // By default, only cache elements that have options.
        if ($cache == 'auto') {
            $auto_cache = (array_key_exists($this->config['type'], $this->config['inner_static']) || $this->config['options'] || $this->config['options_element'] || $this->config['inner_element']);
            $cache = $auto_cache ? 1 : 0;
            // temporarily set auto_cach to always 1
            $cache = true;
        }
        $this->config['cache'] = ($cache && $this->modx->getCacheManager()) ? $cache : false;

        // Allow overriding the default settings for types from the script properties
        $this->config['option_tpl'] = $this->config['option_type'] ? $this->config['option_type'] : $this->config['default_option_tpl'];
        $this->config['selected_text'] = $this->config['selected_text'] ? $this->config['selected_text'] : $this->config['default_selected_text'];

        // used in variable calcs
        $this->config['error_prefix'] = $this->config['error_prefix'] ? $this->config['error_prefix'] : $this->config['prefix'] . 'error.';

        // generate unique key
        $this->config['key'] = preg_replace('/[^a-zA-Z0-9_-]/', '', ($this->config['key_prefix'] . $this->config['name']));
    }

    public function calculateCacheConfig() {
        if (empty($this->config['cacheKey'])) $this->config['cacheKey'] = $this->modx->getOption('cache_resource_key', null, 'resource');
        if (empty($this->config['cacheHandler'])) {
            $cache_resource_handler_default = $this->modx->getOption(xPDO::OPT_CACHE_HANDLER, null, 'xPDOFileCache');
            $this->config['cacheHandler'] = $this->modx->getOption('cache_resource_handler', null, $cache_resource_handler_default);
        }
        if (!isset($this->config['cacheExpires'])) {
            $cache_resource_expires_default = $this->modx->getOption(xPDO::OPT_CACHE_EXPIRES, null, 0);
            $this->config['cacheExpires'] = (integer)$this->modx->getOption('cache_resource_expires', null, $cache_resource_expires_default);
        }
        if (empty($this->config['cacheElementKey'])) $this->config['cacheElementKey'] = $this->modx->resource->getCacheKey() . '/field/' . md5($this->modx->toJSON($this->config) . $this->modx->toJSON($this->modx->request->getParameters()));
        $this->config['cacheOptions'] = array(
            xPDO::OPT_CACHE_KEY => $this->config['cacheKey'],
            xPDO::OPT_CACHE_HANDLER => $this->config['cacheHandler'],
            xPDO::OPT_CACHE_EXPIRES => $this->config['cacheExpires'],
        );
    }

    public function toCache(array $attributes_to_cache){
        $to_cache = array();
        foreach($attributes_to_cache as $attribute) {
            $to_cache[$attribute] = $this->$attribute;
        }
        $this->modx->cacheManager->set($this->config['cacheElementKey'], $to_cache, $this->config['cacheExpires'], $this->config['cacheOptions']);
    }
    public function fromCache(array $attributes_to_cache){
        $cached = true;
        $cache_array = $this->modx->cacheManager->get($this->config['cacheElementKey'], $this->config['cacheOptions']);
        // validate
        foreach($attributes_to_cache as $attribute) {
            if(!isset($cache_array[$attribute])) {
                $cached = false;
                break;
            }
        }
        // set attributes
        foreach($attributes_to_cache as $attribute) {
            $this->$attribute = $cache_array[$attribute];
        }
        return $cached;
    }

    /**
     * The main controller function.
     *
     * @return string The parsed output.
     */
    public function process() {
        $attributes_to_cache = array('html','placeholders','double_processing_needed');
        $cached = false;
        // try to get values from cache
        if ($this->config['cache']) {
            $this->calculateCacheConfig();
            $cached = $this->fromCache($attributes_to_cache);
        }
        if (!$cached) {
            // prime all vars
            $this->placeholders = $this->initializePlaceholders();
            $this->html = $this->initializeHtml();
        }
        // Store to cache if needed.
        if ($this->config['cache'] && !$cached) {
            $this->toCache($attributes_to_cache);
        }
        // get the current value of the field & FormIt validation error
        $current_value = $this->getCurrentValue();
        $error = $this->getError();
        // mark options using a str_replace based on the current value(s)
        $this->markOptions($current_value);
        // set final placeholders
        $this->placeholders['current_value'] = (string) is_array($current_value) ? join(',',$current_value) : $current_value;
        $this->placeholders['error'] = $error;
        $this->placeholders['error_class'] = $error ? (' ' . $this->config['error_class']) : '';
        $this->placeholders['options_html'] = $this->html['options'];
        // Process outer_tpl first ONLY if inner_html ph has output filters.
        // Warning: this may cause unexpected results due to double processing.
        if ($this->double_processing_needed) {
            $this->placeholders['inner_html'] = $this->ffp->processContent($this->html['inner'], $this->placeholders);
        }
        // Optionally set all placeholders globally
        if ($this->config['to_placeholders']) {
            $this->modx->toPlaceholders($this->placeholders, $this->config['key_prefix']);
        }
        // Process the placeholders. With caching, this should be the only time a chunk is processed.
        $output = $this->ffp->processContent($this->html['outer'], $this->placeholders);
        if ($this->config['debug']) {
            $output = $output.'<pre>'.print_r($this->placeholders,1).'</pre>';
        }
        return $output;
    }

    public function markOptions($current_value) { // Add selected markers to options - much faster than FormItIsSelected and FormItIsChecked for large forms
        if ($this->html['options'] && $this->config['selected_text'] && $this->config['mark_selected']) {
            $selected_values = is_array($current_value) ? $current_value : array($current_value);
            foreach ($selected_values as $selected_value) {
                $this->html['options'] = $this->markSelected($this->html['options'], $selected_value, $this->config['selected_text']);
            }
        }
    }

    public function initializePlaceholders() {
        $placeholders = array();
        foreach($this->config as $k => $v) {
            $placeholders[$k] = (string) $v;
        }
        // load custom placeholders - not essential, but helps a lot with speed.
        $custom_ph = explode(',', $this->config['custom_ph']);
        foreach ($custom_ph as $key) {
            if (!isset($placeholders[$key])) $placeholders[$key] = '';
        }
        // set placeholders for field types (e.g [[+checkbox:notempty=`checkbox stuff`]])
        if ($this->config['set_type_ph']) {
            $types = explode(',', $this->config['set_type_ph']);
            foreach ($types as $key) {
                $placeholders[$key] = ($key == $this->config['type']) ? '1' : '';
            }
        }
        // unset any variable placeholders
        $variables = array('error', 'current_value', 'error_class', 'options_html', 'inner_html', 'outer_html');
        foreach ($variables as $key) {
            if (isset($placeholders[$key])) unset($placeholders[$key]);
        }
        return $placeholders;
    }
    public function initializeHtml() {
        $html = array();
        $html['options'] = $this->config['options_html'];
        $html['inner'] = $this->config['inner_html'];

        // Set overrides for options and inner_html
        $html['options'] = $this->processElementOverrides('options', $html['options']);
        $html['inner'] = $this->processElementOverrides('inner', $html['inner']);

        // process inner and outer html template chunks
        if (empty($html['inner'])) $html['inner'] = $this->ffp->getChunkContent($this->config['tpl'], $this->config['delimiter'], $this->config['default_delimiter']);
        $html['outer'] = $this->ffp->getChunkContent($this->config['outer_tpl'], $this->config['outer_delimiter'], $this->config['default_delimiter']);

        // Parse options for checkboxes, radios, etc... if &options is passed
        // Note: if any provided options_html has been found, this part will be skipped
        $options = $this->config['options'];
        if ($options && empty($html['options'])) {
            $html['options'] = $this->processOptions($options, $this->placeholders);
        }

        // If outer template is set, process it. Otherwise just use the $html['inner']
        $html['outer'] = empty($html['outer']) ? $html['inner'] : $html['outer'];
        $inner_no_replace = '[[+inner_html:';
        $inner_replace = '[[+inner_html]]';
        $this->double_processing_needed = false;
        if (strpos($html['outer'], $inner_no_replace) !== false) {
            $this->double_processing_needed = true;
        } else {
            $html['outer'] = str_replace($inner_replace, $html['inner'], $html['outer']);
        }
        return $html;
    }

    public function processElementOverrides($level, $default) {
        $output = $default;
        $element = $this->config[$level . '_element'];
        $element_class = $this->config[$level . '_element_class'];
        $element_properties = $this->modx->fromJSON($this->config[$level . '_element_properties']);
        $properties = array_merge($this->placeholders, $element_properties);
        if ($element && $element_class) {
            if ($element_class === 'modChunk') {
                // Shortcut - use the cachable chunk method of FFP. Allows file-based chunks.
                $output = $this->ffp->getChunk($element, $properties);
            } else {
                // Full route for snippets & others
                /** @var $elementObj modElement */
                $elementObj = $this->modx->getObject($element_class, array('name' => $element));
                if ($elementObj) {
                    $output = $elementObj->process($properties);
                }
            }
        }
        return $output;
    }

    public function processOptions($options, $placeholders) {
        $inner_delimiter = '<!-- ' . $this->config['option_tpl'] . ' -->';
        $output = '';
        $options = explode($this->config['options_delimiter_outer'], $options);
        foreach ($options as $option) {
            $option_array = explode($this->config['options_delimiter_inner'], $option);
            foreach ($option_array as $key => $value) {
                $option_array[$key] = trim($value);
            }
            $inner_array = $placeholders;
            $inner_array['label'] = $option_array[0];
            $inner_array['value'] = isset($option_array[1]) ? $option_array[1] : $option_array[0];
            $inner_array['key'] = $this->config['key'] . '-' . preg_replace('/[^a-zA-Z0-9-_]/', '', $inner_array['value']);
            $output .= $this->ffp->getChunk($this->config['tpl'], $inner_array, $inner_delimiter);
        }
        return $output;
    }

    public function getError() {
        $error = $this->modx->getPlaceholder($this->config['error_prefix'] . $this->config['name']);
        return $error;
    }

    /**
     * Retreives the current value of the field.
     *
     * Depending on config, checks MODX Placeholders set by FormIt and several global variables.
     *
     * @return array|string An array of values or the string value.
     */
    public function getCurrentValue() {
        $current_value = null;
        // try to get the value from formitFastPack using modx placeholders
        // todo: if array, possibly take the FIRST or selected value and mark it as "set" so other field snippets won't use it
        if ($this->config['use_formit']) {
            $placeholder_name = $this->config['prefix'] . $this->config['name'];
            $array_values = $this->getCurrentArrayValues();
            $current_value = $array_values ? $array_values : $this->modx->getPlaceholder($placeholder_name);
        }
        // if no value is found, try alternative sources until a value is found
        if (is_null($current_value) && $this->config['use_get']) {
            $current_value = isset($_GET[$this->config['name']]) ? $_REQUEST[$this->config['name']] : null;
        }
        if (is_null($current_value) && $this->config['use_request']) {
            $current_value = isset($_REQUEST[$this->config['name']]) ? $_REQUEST[$this->config['name']] : null;
        }
        $session_key = $this->config['use_session_prefix'] . $this->config['prefix'] . $this->config['name'];
        if (is_null($current_value) && $this->config['use_session']) {
            $current_value = $this->modx->getOption($session_key, $_SESSION, null);
        }
        $cookies_key = $this->config['use_cookies_prefix'] . $this->config['prefix'] . $this->config['name'];
        if (is_null($current_value) && $this->config['use_cookies']) {
            $current_value = $this->modx->getOption($cookies_key, $_COOKIE, null);
        }
        // use default value if not already set
        $current_value = is_null($current_value) ? $this->config['default_value'] : $current_value;
	// support multiple default values on array fields (checkboxes) using same delimiter (format would be
	// &default_value=`option1||option2`. don't include labels in default specification.
	if($this->config['array'] && !is_array($current_value)) {
            $current_value = explode($this->config['options_delimiter_outer'], $current_value);
        }
        // if configured, save current value in session/ cookies for later use
        if ($this->config['use_session']) {
            $_SESSION[$session_key] = $current_value;
        }
        if ($this->config['use_cookies']) {
            $_COOKIE[$cookies_key] = $current_value;
        }
        return $current_value;
    }

    /**
     * Looks for the array-indicating placeholders in the MODX placeholder list and returns an array of values.
     *
     * @return array List of values.
     */
    public function getCurrentArrayValues() {
        $output = array();
        $placeholder_start = $this->config['prefix'] .$this->config['name'] . '.';
        if (!isset($this->modx->placeholders[$placeholder_start.'0'])) {
            return $output;
        }
        foreach($this->modx->placeholders as $k => $v) {
            if (strpos($k,$placeholder_start) === 0) {
                $ph_key_end = substr($k, strlen($placeholder_start));
                // check if the placeholder key matches the pattern "prefix.name.#" where # is an integer
                if (strlen($ph_key_end) > 0 && is_numeric($ph_key_end) && intval($ph_key_end) >= 0) {
                    $output[] = $v;
                }
            }
        }
        return $output;
    }

    /**
     * Adds a marker (such as selected="selected") after a search string such as value="1" if it is found
     *
     * @access public
     * @param string $input_text The text to process. Should have values in the form of value="$current_value".
     * @param string $current_value The value to add the marker afterwards.
     * @param string $selected_marker The marker to add after the value attribute.
     * @return string The processed output.
     */
    public function markSelected($input_text,$current_value = '',$selected_marker = 'selected="selected"') {
        // Run search and replace to add selected or checked attributes
        $options_selected_search = 'value="'.$current_value.'"';
        $options_selected_replace = $options_selected_search .' '.$selected_marker;
        $output = str_replace($options_selected_search, $options_selected_replace,$input_text);
        return $output;
    }

}
