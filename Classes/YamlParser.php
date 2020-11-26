<?php

namespace nlib\Yaml\Classes;

use nlib\Log\Traits\LogTrait;
use nlib\Instance\Traits\InstanceTrait;
use nlib\Path\Classes\Path;
use nlib\Yaml\Interfaces\YamlParserInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlParser implements YamlParserInterface {

    use LogTrait;
    use InstanceTrait;

    public function get(string $file, int $flag = 0) {

        try {
            return $this->include(Yaml::parseFile($file . '.yaml', $flag), $flag);
        } catch (ParseException $e) {
            $this->dlog([__CLASS__ . '::' . __FUNCTION__ => 'Unable to parse the YAML string : ' . $e->getMessage()]);
        }
    }

    public function include($mixed, int $flag = 0) {

        if(is_array($mixed) && array_key_exists($i = 'imports', $mixed) && is_array($imports = $mixed[$i])) :
            foreach($imports as $import) :
                if(!array_key_exists($r = 'resource', $import)) continue;
                $mixed = array_merge($this->get($this->getResource($import[$r]), $flag), $mixed);
            endforeach;
        endif;

        if(is_object($mixed) && property_exists($mixed, $i = 'imports') && is_array($imports = $mixed->$i)) :
            foreach($imports as $import) :
                if(!property_exists($import, $r = 'resource')) continue;
                foreach($Obj = $this->get($this->getResource($import->$r), $flag) as $key => $value) :
                    $mixed->$key = $value;
                endforeach;
            endforeach;
        endif;

        return $mixed;
    }

    protected function getResource(string $resource) : string {
        return str_replace(
            ['\\', '/'],
            DIRECTORY_SEPARATOR,
            str_replace('%ROOT_CONFIG%', Path::i($this->_i())->getConfig(), $resource)
        );
    }
    
}