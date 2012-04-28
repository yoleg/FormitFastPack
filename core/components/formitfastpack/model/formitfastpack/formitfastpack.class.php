<?php
/**
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
class FormitFastPack {
    /**
     * @access protected
     * @var array A collection of preprocessed chunk values.
     */
    protected $chunks = array();
    /**
     * @access public
     * @var modX A reference to the modX object.
     */
    public $modx = null;
    /**
     * @access public
     * @var array FormitFastPack config array
     */
    public $config = array();

    /**
     * @access public
     * @var array A collection of properties to adjust FormitFastPack behaviour.
     */
    public $defaults = array();

    /**
     * The FormitFastPack Constructor.
     *
     * This method is used to create a new FormitFastPack object.
     *
     * @param modX &$modx A reference to the modX object.
     * @param array $config A collection of properties that modify FormitFastPack
     * behaviour.
     * @return FormitFastPack A unique FormitFastPack instance.
     */
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;
        
        $corePath = $this->modx->getOption('ffp.core_path',null,$modx->getOption('core_path').'components/formitfastpack/');
        $assetsPath = $this->modx->getOption('ffp.assets_path',null,$modx->getOption('assets_path').'components/formitfastpack/');
        $assetsUrl = $this->modx->getOption('ffp.assets_url',null,$modx->getOption('assets_url').'components/formitfastpack/');

        $this->config = array_merge(array(
            'core_path' => $corePath,
            'model_path' => $corePath.'model/',
            'processors_path' => $corePath.'processors/',
            'controllers_path' => $corePath.'controllers/',
            'chunks_path' => $corePath.'elements/chunks/',
            'snippets_path' => $corePath.'elements/snippets/'
        ),$config);

        /* load debugging settings */
        $debug = $this->modx->getOption('debug',$this->config,false);
        if ($debug) {
            error_reporting(E_ALL); ini_set('display_errors',true);
            $this->modx->setLogTarget('HTML');
            $this->modx->setLogLevel(modX::LOG_LEVEL_ERROR);

            $debugUser = $this->config['debugUser'] == '' ? $this->modx->user->get('username') : 'anonymous';
            $user = $this->modx->getObject('modUser',array('username' => $debugUser));
            if ($user == null) {
                $this->modx->user->set('id',$this->modx->getOption('debugUserId',$this->config,1));
                $this->modx->user->set('username',$debugUser);
            } else {
                $this->modx->user = $user;
            }
        } else {
            ini_set('display_errors',false);
            // error_reporting(E_ALL);
            $this->modx->setLogTarget('FILE');
        }
    }
    
    /**
     * Sets a configuration array
     * For fieldSetDefaults snippet.
     *
     * @access public
     * @param array $config The configuration array.
     * @return bool Success.
     */
    public function setConfig(array $defaults = array()) {
        $this->defaults = array_merge($this->defaults,$defaults);
        return true;
    }
    
    /**
     * Loads a configuration array
     * For fieldSetDefaults snippet.
     *
     * @access public
     * @return array The configuration array.
     */
    public function getConfig() {
        return $this->defaults;
    }

    /**
     * Adds a marker (such as selected="selected") after a search string such as value="1" if it is found.
     * 
     *
     * @access public
     * @param string $input_text The text to process. Should have values in the form of value="$current_value".
     * @param string $current_value The value to add the marker afterwards.
     * @param string $selected_marker The marker to add after the value attribute.
     * @return string The processed output.
     */
    public function markSelected($input_text,$current_value = '',$selected_marker = 'selected="selected"') {
        $input_text = $this->_markSearchReplace($input_text, $current_value,$selected_marker);
        if (strpos($current_value,',') !== false) {
            $current_value_array = explode(',',$current_value);
            foreach($current_value_array as $value) {
                $input_text = $this->_markSearchReplace($input_text,$value,$selected_marker);
            }
        }
        return $input_text;
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
    protected function _markSearchReplace($input_text,$current_value = '',$selected_marker = 'selected="selected"') {
        // Run search and replace to add selected or checked attributes
        $options_selected_search = 'value="'.$current_value.'"';
        $options_selected_replace = $options_selected_search .' '.$selected_marker;
        $output = str_replace($options_selected_search, $options_selected_replace,$input_text);
        return $output;
    }

/*********************************************************************/
/*********************************************************************/
/*********************************************************************/
/*                                                                   */
/*                            Chunks                                 */
/*                                                                   */
/*********************************************************************/
/*********************************************************************/
/*********************************************************************/
    public function _getCacheChunk($name,$properties = array()) {
        $cache_id = $name;
        $output = $this->config['enable_chunk_cache'] ? $this->_cacheGet('chunk',$cache_id,$properties) : null;
        if (is_null($output)) {
            $content = $this->getChunkContent($name);
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($content);
            $chunk->setCacheable(false);
            $output = $chunk->process($properties,null);
            if ($this->config['enable_chunk_cache']) {
                $this->_cacheSet('chunk',$name,$properties,$output);
            }
        }
        return $output;
    }

    /**
     * Gets a Chunk and caches it; also falls back to file-based templates for easier debugging.
     *
     * @access public
     * @param string $name The name of the Chunk (or the content of the chunk).
     * @param array $properties The properties for the Chunk
     * @param string $delimiter The delimiter to split the chunk by. Use 'none' to use the entire chunk.
     * @return string The processed content of the Chunk
     */
    public function getChunk($name,$properties = array(),$delimiter = 'none') {
        $chunk = null;
        $o = $this->getChunkContent($name,$delimiter);
        $output = $this->processContent($o,$properties);
        return $output;
    }
    /**
     * Processes a string as if it were the content of a chunk.
     * @param string $content The unprocessed content of the chunk
     * @param array $properties The properties for the Chunk
     * @return string Processed output
     */
    public function processContent($content,$properties=array()) {
        $ph = array();
        foreach($properties as $key => $value) {
            $ph[$key] = (string) $value;
        }
        /** @var $chunk modChunk */
        $chunk = $this->modx->newObject('modChunk');
        $chunk->setContent($content);
        $chunk->setCacheable(false);
        $output = $chunk->process($ph);
        return $output;
    }
    /**
     * Gets the content of a chunk.
     * Uses file-based chunks if database chunks not available.
     * Will always use the file-based chunk if $debug is set to true.
     * @param string $name The name of the Chunk
     * @param string $delimiter The delimiter to split the chunk by. Use 'none' to use the entire chunk.
     * @param string $default_delimiter The delimiter to use if the given delimiter does not exist in the chunk.
     * @return string The unprocessed content of the Chunk
     */
    public function getChunkContent($name,$delimiter = 'none',$default_delimiter= '<!-- default -->') {
        /** @var $chunk modChunk */
        $chunk = null;
        if (!isset($this->chunks[$name][$delimiter])) {
            // first, try getting chunk from database
            if (!$this->modx->getOption('FormitFastPack.debug',null,false)) {
                $chunk = $this->modx->getObject('modChunk',array('name' => $name));
            }
            // get chunk content if exists, or try from file if not
            if (!empty($chunk)) {
                $content = $chunk->getContent();
            } else {
                $content = $this->_getTplChunkContent($name);
                if ($content == $name) return $name;
            }
            // explode by delimiter unless delimiter is 'none'
            if (empty($delimiter)) return 'Type not found.';
            if ($delimiter != 'none') {
                if (strpos($content,$delimiter) === false) {
                    // if the default delimiter is not present, return the entire content
                    if (strpos($content,$default_delimiter) === false) {
                        return $content;
                    }
                    // else just use the default delimiter
                    $delimiter = $default_delimiter;
                }
                $contentArray = explode($delimiter,' '.$content);
                $content = $contentArray[1];
            }
            // cache for the lifetime of the request
            $this->chunks[$name][$delimiter] = $content;
        } else {
            // retreive the cached content of the chunk
            $content = $this->chunks[$name][$delimiter];
        }
        return $content;
    }
    /**
     * Returns a modChunk object from a template file.
     *
     * @access private
     * @param string $name The name of the Chunk. Will parse to name.chunk.tpl
     * @param string $suffix The suffix to postfix the chunk with
     * @return modChunk/boolean Returns the modChunk object if found, otherwise
     * false.
     */
    private function _getTplChunkContent($name,$suffix = '.chunk.tpl') {
        $o = $name;
        $suffix = $this->modx->getOption('suffix',$this->config,$suffix);
        $f = $this->config['chunks_path'].strtolower($name).$suffix;
        if (file_exists($f)) {
            $o = file_get_contents($f);
        }
        return $o;
    }
/*********************************************************************/
/*********************************************************************/
/*********************************************************************/
/*                                                                   */
/*                                Cache                              */
/*                                                                   */
/*********************************************************************/
/*********************************************************************/
/*********************************************************************/
    // todo: use for getChunk w/ auto clearing after chunk edited
    /**
     * Get a unique cache key value
     *
     * @param mixed $id The unique ID of the resource.
     * @param array $params An array of parameters to cache
     * @param string $type What we are caching
     * @return string The generated cache key.
     */
    protected function _getCacheKey($id,array $params = array(), $type = 'chunk') {
        $output = $type.'s/';
        $output .= $id.'/';
        $key = md5($params);
        $page = (string) $this->modx->resource->get('id');
        $output .= "page-{$page}-params-{$key}";
        return $output;
    }
    public function clearCache() {
        $this->modx->cacheManager->delete($this->config['chunk_cache_path']);
    }
    public function getCacheOptions() {
        $options = array();
        return $options;
    }
    /** Update the time limit on the cache and optionally add info
     * @param mixed $id The unique ID of the resource.
     * @param array $params An array of parameters to cache
     * @param string $value The value to cache
     * @param int $time_limit Time limit in seconds
     * @return string The cache value
     */
    protected function _cacheSet($id, $params, $value, $time_limit=null) {
        $options = $this->getCacheOptions();
        $time_limit = is_null($time_limit) ? 60*60*24 : $time_limit;
        $this->modx->cacheManager->set($this->_getCacheKey($id,$params),$value, $time_limit,$options);
        return true;
    }
    /** Retrieve info from the cache
     * @param mixed $id The unique ID of the resource.
     * @param array $params An array of parameters to cache
     * @return string The value from the cache
     */
    protected function _cacheGet($id, array $params) {
        $options = $this->getCacheOptions();
        $cacheValue = $this->modx->cacheManager->get($this->_getCacheKey($id,$params),$options);
        return $cacheValue;
    }
}