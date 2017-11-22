<?php

namespace Viewflex\Ligero\Utility;

trait ArrayHelperTrait
{
    /**
     * Extracts from array of objects an array of names.
     *
     * @param array $objects
     * @return array
     */
    public function objectNames($objects)
    {
        $names = array();
        foreach ($objects as $obj)
            $names[] = $obj->name;

        return $names;
    }

    /**
     * Combines two arrays into an indexed array with unique values.
     *
     * @param array $array_1
     * @param array $array_2
     * @return array
     */
    public function uniqueIndexedArrayMerge($array_1, $array_2)
    {
        return array_values(array_unique(array_merge(
            $this->arrayNormalize($array_1),
            $this->arrayNormalize($array_2)
        )));
    }
    
    /**
     * Makes a valid string parameter into an array with one string value.
     * Filters array of parameters to strip empty or invalid values.
     * 
     * @param string|array $input
     * @return array
     */
    public function arrayNormalize($input)
    {
        $output = array();
        
        if (!is_array($input)) {
            
            if ($this->isValidStringParameter($input))
                $output[] = $input;

        } else {
            
            foreach ($input as $value) {
                
                if ($this->isValidStringParameter($value))
                    $output[] = $value;
            }
        }

        return $output;
    }
    
    /**
     * Returns true only if input is a string, has non-zero length, and is not ' '.
     * 
     * @param mixed $input
     * @return bool
     */
    function isValidStringParameter($input)
    {
        return (is_string($input) && (strlen($input) !== 0) && ($input !== ' '));
    }

    /**
     * Recursively convert array to string for logging.
     *
     * @param array $attributes
     * @param string $prefix
     * @return string
     */
    public function arrayToString($attributes = [], $prefix = '')
    {
        $string = '';
        $i = 1;
        $size = count($attributes);
        
        foreach ($attributes as $key => $value) {

            if (!is_object($value)) {

                if(is_array($value)) {
                    $string .= $this->arrayToString($value, strval(($prefix ? ($prefix.'.'.$key) : $key)));
                } else {
                    $string .= ($prefix ? ($prefix.'.'.$key) : $key).'='.strval($value).($i == $size ? '' : ', ');
                }
                $i++;
                
            }
            
        }

        return $string;
    }
    
}
