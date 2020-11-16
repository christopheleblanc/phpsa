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
 * Namespace containing standard functions to manipulate Xml data/values.
 */

namespace PHPStreamsAggregator\Library\Xml;

/**
 * Check if a XML attribute is a string equivalent to boolean True or FALSE.
 * Will return the corresponding boolean as result, or null if the attribute 
 * does not correspond to a boolean.
 * Note: Please note that this function does not check if the value is a string
 * or an integer. It only check if the value is one of those can be considered
 * as boolean (string or integer).
 *
 * Examples:
 * "1"           : True
 * "true"        : True
 * "TRUE"        : True
 * "0"           : False
 * "false"       : False
 * "FALSE"       : False
 * ""            : null
 * "test"        : null
 *
 * @param    string        - The attribute values
 * @returns  boolean|null  - True if the value corresponds to boolean True, False
 *                           if it corresponds to boolean False, or Null.
 */
function attrIsTrue(&$attr)
{

    $attrStr = strtolower((string)$attr);
    $attrInt = (int)$attr;

    if(strcmp($attrStr, "true") == 0 || $attrInt == 1){
        return true;
    }
    else if(strcmp($attrStr, "false") == 0 || $attrInt == 0){
        return false;
    }
    else{
        return null;
    }

}