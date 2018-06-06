<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Fenix
 */
abstract class TemplateMaster extends Master {
    protected $_simpleTags;
    protected $_simpleBlocks;
    protected $_complexBlocks;    
    
    public function __construct($class = __CLASS__) {
        parent::__construct($class);
        $this->_complexBlocks = array();
        $this->_simpleBlocks = array();
        $this->_complexBlocks = array();
    }
}

?>
