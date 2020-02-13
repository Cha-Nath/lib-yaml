<?php

namespace nlib\Yaml\Interfaces;

use nlib\Yaml\Classes\YamlParser;

interface ParserTraitInterface {

    /**
     *
     * @return YamlParser
     */
    public function Parser() : YamlParser;

    /**
     *
     * @param YamlParser $parser
     * @return self
     */
    public function setParser(YamlParser $parser);
}