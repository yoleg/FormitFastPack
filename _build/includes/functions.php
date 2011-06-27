<?php
/* package formitfastpack */
/* based on quip functions.php */
function getSnippetContent($filename) {
    $o = file_get_contents($filename);
    $o = str_replace('<?php','',$o);
    $o = str_replace('?>','',$o);
    $o = _removeBOM($o);
    $o = trim($o);
    return $o;
}
function _removeBOM($str=""){ 
    if(substr($str, 0,3) == pack("CCC",0xef,0xbb,0xbf)) {
        $str=substr($str, 3);
    }
    $str = str_replace('﻿','',$str);
    return $str;
}