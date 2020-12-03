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
            // var_dump(file_get_contents($file . '.yaml'));die;
            if(!file_exists($file .= '.yaml')) die;
            $yaml = file_get_contents($file);
            // var_dump($yaml, preg_match('/\@include\([a-zA-Z0-9_%\\\/]+\)/', $yaml, $matches));
            // '/\@include\((\w+)\)/'
            if(!empty(preg_match('/\@include\(([a-zA-Z0-9_%\\/,]+)\)/', $yaml, $matches))) :
                // var_dump($matches[1]);
                $tmp = '';
                $includes = explode(',', $matches[1]);
                foreach($includes as $include) if(file_exists($f = $this->getResource($include) . '.yaml')) $tmp .= file_get_contents($f);
                $yaml = str_replace('@include('. $matches[1] .')', '', $yaml);
                $tmp .= $yaml;
                $yaml = $tmp;
            endif;

            return $this->include(Yaml::parse($yaml, $flag), $flag);
            // return $this->include(Yaml::parseFile($file . '.yaml', $flag), $flag);
        } catch (ParseException $e) {
            $this->dlog([__CLASS__ . '::' . __FUNCTION__ => 'Unable to parse the YAML string : ' . $e->getMessage()]);
        }
    }

    public function include($mixed, int $flag = 0) {

        if(is_array($mixed) && array_key_exists($i = 'imports', $mixed) && is_array($imports = $mixed[$i])) :
            foreach($imports as $import) :
                if(!array_key_exists($r = 'resource', $import)) continue;
                // $mixed = array_replace_recursive($this->get($this->getResource($import[$r]), $flag), $mixed);
                $mixed = $this->merge($this->get($this->getResource($import[$r]), $flag), $mixed);
                
            endforeach;
        endif;

        if(is_object($mixed) && property_exists($mixed, $i = 'imports') && is_array($imports = $mixed->$i)) :
            foreach($imports as $import) :
                if(!property_exists($import, $r = 'resource')) continue;
                // $mixed = (object) array_merge_recursive((array) $this->get($this->getResource($import->$r), $flag), (array) $mixed);
                $mixed = (object) $this->merge((array) $this->get($this->getResource($import->$r), $flag), (array) $mixed);
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

    protected function merge(array $a, array $b) {

        $tmps = $a;

        foreach($b as $key => $value) :
            
            if (is_string($key))
                if(is_array($value) && array_key_exists($key, $tmps) && is_array($tmps[$key]))
                    $tmps[$key] = $this->merge($tmps[$key], $value);
                else $tmps[$key] = $value;
            else $tmps[] = $value;

        endforeach;

        return $tmps;
    }
    
}