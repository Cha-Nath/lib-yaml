<?php

namespace nlib\Yaml\Classes;

use nlib\Log\Traits\LogTrait;
use nlib\Instance\Traits\InstanceTrait;
use nlib\Yaml\Interfaces\YamlParserInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlParser implements YamlParserInterface {

    use LogTrait;
    use InstanceTrait;

    public function get(string $file) {

        try {
            return Yaml::parseFile($file . '.yaml');
        } catch (ParseException $e) {
            $this->dlog([__CLASS__ . '::' . __FUNCTION__ => 'Unable to parse the YAML string : ' . $e->getMessage()]);
        }
    }
    
}