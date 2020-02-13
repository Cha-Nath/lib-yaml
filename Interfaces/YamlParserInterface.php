<?php

namespace nlib\Yaml\Interfaces;

interface YamlParserInterface {

    /**
     *
     * @param string $file
     * @return mixed
     */
    public function get(string $file);
}