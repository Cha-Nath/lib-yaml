<?php

namespace nlib\Yaml\Classes;

use nlib\Log\Traits\LogTrait;
use nlib\Yaml\Interfaces\YamlParserInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlParser implements YamlParserInterface {

    use LogTrait;

    public function get(string $file) {

        try {
            return Yaml::parseFile($file . '.yaml');
        } catch (ParseException $e) {
            $error = 'Unable to parse the YAML string : ' . $e->getMessage();
            $this->log(['Yaml' => $error]);
            echo($error);
        }
    }
    
}