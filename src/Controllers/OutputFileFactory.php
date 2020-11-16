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
 * OutputFileFactory
 * Class representing the output file in program (when using an output file).
 * The OutputFileFactory is intended to create and fill an instance of
 * OutputFile which can be passed in Context.
 * OutputFileFactory is mostly a copy of OutputFile plus accessors
 * which must not be available from the Context class.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Models\OutputFile;

/**
 * OutputFileFactory
 */
class OutputFileFactory{

    /** @var string  - The file name **/
    private $filePath;

    /** @var boolean - Defines if the file exists **/
    private $fileExists;

    /** @var boolean - Defines if the file is up to date **/
    private $isUptodate;

    /** @var boolean - Defines if the file is validated **/
    private $isValidated;

    /**
     * Constructor
     * @param string   - The file path.
     * @param boolean  - Defines if the file exists
     * @param boolean  - Defines if the file is up to date.
     * @param boolean  - Defines if the file is validated.
     */
    public function __construct(&$filePath = null, &$fileExists = false, &$isUptodate = false, &$isValidated = false)
    {
        $this->filePath = $filePath;
        $this->fileExists = $fileExists;
        $this->isUptodate = $isUptodate;
        $this->isValidated = $isValidated;
    }

    /**
     * Get the file path.
     * @returns string
     */
    public function &getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Check if file exists.
     * @returns boolean
     */
    public function &exists()
    {
        return $this->fileExists;
    }

    /**
     * Check if output file is up to date
     * @returns boolean
     */
    public function &getIsUptodate()
    {
        return $this->isUptodate;
    }

    /**
     * Check if output file is validated
     * @returns boolean
     */
    public function &getIsValidated()
    {
        return $this->isValidated;
    }

    /**
     * Set the file path.
     * @param string - The file path
     */
    public function setFilePath($path)
    {
        $this->filePath = $path;
    }

    /**
     * Defines if the file exists.
     * @param boolean
     */
    public function setFileExists($v)
    {
        $this->fileExists = $v;
    }

    /**
     * Defines if the file is up to date.
     * @param boolean
     */
    public function setIsUptodate($v)
    {
        $this->isUptodate = $v;
    }

    /**
     * Defines if the file is validated.
     * @param boolean
     */
    public function setIsValidated($v)
    {
        $this->isValidated = $v;
    }

    /**
     * Return an instance of OutputFile for Context.
     * @returns &OutputFile
     */
    public function &getContextualizable()
    {
        $new = new OutputFile(
            $this->filePath,
            $this->fileExists,
            $this->isUptodate,
            $this->isValidated
        );
        return $new;
    }

}