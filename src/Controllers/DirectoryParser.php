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
 * DirectoryParser
 * Class intended to parse directory strings in order to define if the string
 * define an absolute path or a relative path.
 * The class stores three important values ​​that can be retrieved by accessors to make a
 * decision later, and possibly create an instance of FileData.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Models\FileData;

/**
 * DirectoryParser
 */
class DirectoryParser{

    /**
     * The type of the file string, in form of an integer, corresponding to
     * FileData types enumeration.
     * @var integer
     */
    private $type;

    /**
     * The path, or NULL.
     * @var string|null
     **/
    private $path;

    /**
     * Defines if the path is root only or not
     * @var boolean
     **/
    private $isRootOnly;

    /**
     * Constructor
     **/
    public function __construct()
    {
        $this->type = -1;
        $this->path = null;
        $this->isRootOnly = true;
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
     * Remove all slashes at the end of the string
     * @param string - The string to modify.
     * @return string - The string obtained.
     */
    private function &removeLastSlash(&$str)
    {
        $tmp = (string)$str;
        while(true){
            $pos = strrpos($tmp, DIRECTORY_SEPARATOR);
            $len = strlen($tmp);
            if($pos+1 == $len){
                $tmp = substr($tmp, 0, $pos);
            }
            else{
                return $tmp;
            }
        }
    }

    /**
     * Parse the file string (absolute path/relative path/file name).
     * @param string - The string to parse.
     */
    public function parse($dirname)
    {

        $len = strlen($dirname);

        if($len > 0){

            // Path
            $fdirname =  $this->slashesToDirectorySeparator($dirname);
            $path = null;

            $seppos = strpos($fdirname, DIRECTORY_SEPARATOR);
            $drivepos = strpos($fdirname, ":".DIRECTORY_SEPARATOR);
            if($seppos === 0){
                // Absolute path
                $this->type = FileData::ABSOLUTE_PATH;
                if($len > 1){
                    $this->isRootOnly = false;
                }
                else{
                    $this->isRootOnly = true;
                }
            }
            else if($drivepos === 1){
                // Absolute path
                $this->type = FileData::ABSOLUTE_PATH;
                if($len > 3){
                    $this->isRootOnly = false;
                }
                else{
                    $this->isRootOnly = true;
                }

            }
            else{
                // Relative path
                $this->type = FileData::RELATIVE_PATH;
                if($len > 0){
                    $this->isRootOnly = false;
                }
                else{
                    $this->isRootOnly = true;
                }
            }
            $this->path = $this->removeLastSlash($fdirname);

        }
        else{
            // Filename
            $this->path = $fdirname;
            $this->isRootOnly = true;
        }

    }

    /**
     * Get the type of value (absolute path/relative path), in form of an integer corresponding
     * to the FileData types enumeration.
     * Note: Do not call this function before calling the function "parse()", otherwise it will return
     * the default value "-1".
     * @returns integer - The type
     */
    public function getValueType()
    {
        return $this->type;
    }

    /**
     * Will return the path (absolute or relative) if the parsing process has detected a path,
     * or NULL.
     * Note: Do not call this function before calling the function "parse()", otherwise it will return
     * NULL.
     * @returns string|null - The file path, or NULL.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Check if the path is an absolute path and the root of a device (ex: "\")
     * @returns boolean
     */
    public function getIsRootOnly()
    {
        return $this->isRootOnly;
    }

}