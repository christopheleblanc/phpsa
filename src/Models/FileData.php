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
 * FileData
 * Class representing a file path.
 */

namespace PHPStreamsAggregator\Models;

/**
 * FileData
 */
class FileData{

    /**
     * Enumeration of types
     */
    public const ABSOLUTE_PATH = 1;
    public const RELATIVE_PATH = 2;
    public const FILENAME = 3;

    /**
     * The type of the file string, in form of an integer, corresponding to
     * FileData types enumeration.
     * @var integer
     */
    private $type;

    /** @var string - The file name **/
    private $filename;

    /** @var string - The file path **/
    private $filepath;

    /**
     * Constructor
     * @param integer - The type of the FileData (absolute path, relative path, or file name),
     *                  corresponding to FileData types enumeration.
     * @param string  - The file name (with extension)
     * @param string  - The file path
     */
    public function __construct($type, $filename, $filepath)
    {
        $this->type = $type;
        $this->filename = $filename;
        $this->filepath = $filepath;
    }

    /**
     * Get the type of value (absolute path/relative path/file name), in form of an integer
     * corresponding to the FileData types enumeration.
     * @returns integer - The type
     */
    public function &getValueType()
    {
        return $this->type;
    }

    /**
     * Get the file name.
     * @returns string - The file name
     */
    public function &getFileName()
    {
        return $this->filename;
    }

    /**
     * Get the file path.
     * @returns string - The file path
     */
    public function &getFilePath()
    {
        return $this->filepath;
    }

    /**
     * Check if this data has been detected as a relative/absolute path.
     * @returns boolean - True if this data has been detected as a path, or False.
     */
    public function isPath()
    {
        return ($this->type == self::ABSOLUTE_PATH || $this->type == self::RELATIVE_PATH);
    }

}