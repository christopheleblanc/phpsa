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
 * DataChecker
 * Class intended to check if several important directories used by the program
 * exists. It is used at the start of the program.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;

/**
 * DataChecker
 */
class DataChecker{

    /** @var string[] - Array containing missing directories **/
    private $missingDirs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->missingDirs = [];
    }

    /**
     * Checks if the directories used by the program exists and 
     * create them if they don't.
     * @return integer The number of errors that occurred while creating missing folders.
     */
    public function check()
    {
        /*
        * Define application directories.
        */
        $appDirs = array(
            Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con::DATA_DIR_NAME,
            Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con::DATA_DIR_NAME . DIRECTORY_SEPARATOR . Con::CONFIG_DIR_NAME,
            Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con::DATA_DIR_NAME . DIRECTORY_SEPARATOR . Con::LOG_DIR_NAME
        );

        /*
        * Create directories if they not exists.
        */
        $errs = 0;
        foreach($appDirs as $appDir){

            if(!file_exists($appDir)){
                $errs++;
                $this->missingDirs[] = $appDir;
            }
        }

        return ($errs == 0);

    }

    /**
     * Get missing directories
     * @returns string[]
     */
    public function &getMissingDirectories()
    {
        return $this->missingDirs;
    }

}