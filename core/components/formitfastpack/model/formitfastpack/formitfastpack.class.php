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
        if ($this->modx->getOption('debug',$this->config,false)) {
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

    /**
     * Gets a Chunk and caches it; also falls back to file-based templates
     * for easier debugging.
     *
     * Will always use the file-based chunk if $debug is set to true.
     *
     * @access public
     * @param string $name The name of the Chunk
     * @param array $properties The properties for the Chunk
     * @return string The processed content of the Chunk
     */
    public function getChunk($name,$properties = array(),$delimiter = 'none') {
        $chunk = null;
        if (!isset($this->chunks[$name][$delimiter])) {
            if (!$this->modx->getOption('FormitFastPack.debug',null,false)) {
                $chunk = $this->modx->getObject('modChunk',array('name' => $name));
            }

            if (empty($chunk)) {
                $chunk = $this->_getTplChunk($name);
                if ($chunk == $name) return $name;
            }
            if ($delimiter != 'none') {
                $content_full = $chunk->getContent();
                $contentArray = explode($delimiter,$content_full);
                $content_subset = $contentArray[1];
                $chunk = $this->modx->newObject('modChunk');
                $chunk->setContent($content_subset);
            }
            $this->chunks[$name][$delimiter] = $chunk->getContent();
        } else {
            $o = $this->chunks[$name][$delimiter];
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }
        $chunk->setCacheable(false);
        return $chunk->process($properties);
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
    private function _getTplChunk($name,$suffix = '.chunk.tpl') {
        $chunk = $name;
        $suffix = $this->modx->getOption('suffix',$this->config,$suffix);
        $f = $this->config['chunks_path'].strtolower($name).$suffix;
        if ($name == 'optionscountries') {echo $f; die();}
        if (file_exists($f)) {
            $o = file_get_contents($f);
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name',$name);
            $chunk->setContent($o);
        }
        return $chunk;
    }
}