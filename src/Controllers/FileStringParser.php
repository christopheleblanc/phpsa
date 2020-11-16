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
 * FileStringParser
 * Class intended to parse file options strings in order to define if the string
 * define an absolute path, a relative path, or a file name.
 * The class stores three important values ​​that can be retrieved by accessors to make a
 * decision later, and possibly create an instance of FileData.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\Models\FileData;

/**
 * FileStringParser
 */
class FileStringParser{

    /**
     * The type of the file string, in form of an integer, corresponding to
     * FileData types enumeration.
     * @var integer
     */
    private $type;

    /** @var string|null - The file name, or NULL **/
    private $filename;

    /**
     * The file path, or NULL.
     * @var string|null
     **/
    private $filepath;

    /**
     * Constructor
     **/
    public function __construct()
    {
        $this->type = -1;
        $this->filename = null;
        $this->filepath = null;
    }

    /**
     * Replace all slashes and backslashes to PHP constant "DIRECTORY_SEPARATOR".
     * @param string - The string to modify.
     * @return string - The string obtained.
     */
    private function slashesToDirectorySeparator(&$str)
    {
        return preg_replace("/[\/\\\]/", DIRECTORY_SEPARATOR, $str);
    }

    /**
     * Parse the file string (absolute path/relative path/file name).
     * @param string - The string to parse.
     */
    public function parse($string)
    {

        $pathParts = pathinfo($string);
        $dirname = $pathParts['dirname'];
        $basename = $pathParts['basename'];

        $dirnameLength = strlen($dirname);

        if($dirnameLength > 0 && strcmp($dirname, '.') !== 0){

            // Path
            $fdirname =  $this->slashesToDirectorySeparator($dirname);

            $seppos = strpos($string, DIRECTORY_SEPARATOR);
            $drivepos = strpos($string, ":".DIRECTORY_SEPARATOR);
            if($seppos === 0 || $drivepos === 1){
                // Absolute path
                $this->filename = $basename;
                if($dirnameLength > 1){
                    $this->filepath = $fdirname;
                }
                else{
                    $this->filepath = null;
                }
                $this->type = FileData::ABSOLUTE_PATH;
            }
            else{
                // Relative path
                $this->filename = $basename;
                $this->filepath = $fdirname;
                $this->type = FileData::RELATIVE_PATH;
            }

        }
        else{
            // Filename
            $this->filename = $basename;
            $this->filepath = null;
            $this->type = FileData::FILENAME;
        }

    }

    /**
     * Get the type of value (absolute path/relative path/file name), in form of an integer
     * corresponding to the FileData types enumeration.
     * Note: Do not call this function before calling the function "parse()", otherwise it will return
     * the default value "-1".
     * @returns integer - The type
     */
    public function getValueType()
    {
        return $this->type;
    }

    /**
     * Get the file name.
     * Note: Do not call this function before calling the function "parse()", otherwise it will return
     * NULL.
     * @returns string - The file name
     */
    public function getFileName()
    {
        return $this->filename;
    }

    /**
     * Will return the file path (absolute or relative) if the parsing process has detected a file path,
     * or NULL if the parsing process has detected a file name.
     * Note: Do not call this function before calling the function "parse()", otherwise it will return
     * NULL.
     * @returns string|null - The file path, or NULL.
     */
    public function getFilePath()
    {
        return $this->filepath;
    }

}