<?php
/**
 * PHP Streams Aggregator
 * Version 1.0.0
 * Author: Christophe Leblanc
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * Copyright (C) 2018 - 2020 Christophe Leblanc
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * This file is part of "PHP Streams Aggregator".
 *
 * "PHP Streams Aggregator" is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * "PHP Streams Aggregator" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with "PHP Streams Aggregator".  If not, see
 * <https://www.gnu.org/licenses/>.
 */

/**
 * MultiClassNamesParser
 * Class used to parse multiple class names in string values.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Texts;

/**
 * MultiClassNamesParser
 */
class MultiClassNamesParser{

    /** @const string - Class names separator */
    private const OPTS_SEPARATOR = ";";

    /** @var string - The error text */
    private $errorText;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->errorText = null;
    }

    /**
     * Parse a string
     * @param string - The string to parse
     * @param &Array - The array in which the parsed class name must be added.
     */
    private function parseSingle($string, &$arr)
    {
        if(strpos($string, '\\') !== false){
            $this->errorText = Texts\errorPluginClassNameBackslashes("run");
            throw new \Exception();
        }
        else{
            $arr[] = $string;
        }
    }

    /**
     * Parse multiple classes names.
     * Note: Class names can multiple and separated by comma ";". This function is intended to parse
     * all names, while the function "parseSingle()" is intended to parse a single name.
     * @throw   Exception        - when an error occured while parsing the string.
     * @param   string           - The option string.
     * @returns string[]         - An array containing all class names.
     */
    public function &parse($string)
    {

        $options = [];

        if(strpos($string, self::OPTS_SEPARATOR) !== false){
            // Possible multiple options
            $tmpOpts = explode(self::OPTS_SEPARATOR, $string);
            foreach($tmpOpts as $tmpOpt){
                try{
                    $this->parseSingle($tmpOpt, $options);
                }
                catch(\Exception $ex){
                    throw $ex;
                }
            }
        }
        else{
            // One options
            try{
                $this->parseSingle($string, $options);
            }
            catch(\Exception $ex){
                throw $ex;
            }
        }

        return $options;

    }

    /**
     * Get the error text.
     * @returns string|null - The text, or NULL.
     */
    public function getErrorText()
    {
        return $this->errorText;
    }

}