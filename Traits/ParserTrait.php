<?php

namespace nlib\Yaml\Traits;

use nlib\Yaml\Classes\YamlParser;

trait ParserTrait {

    private $_parser;

    #region Getter

    public function Parser() : YamlParser {
        if(empty($this->_parser)) $this->setParser(new YamlParser);
        return $this->_parser;
    }
    
    #endregion

    #region Setter

    public function setParser(YamlParser $parser) : self { $this->_parser = $parser; return $this; }
    
    #endregion
}