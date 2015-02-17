<?php

namespace Zerg\Stream;

class Factory 
{
    public static function get($type, $data)
    {
        $className = $type . 'Stream';
        if (!class_exists($className))
            throw new \Exception('Unknown stream type: ' . $type);

        return new $className($data);
    }
} 