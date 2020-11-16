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
 * ProcessLoadProgramData
 * Top level class used by the program to load program data.
 * Currently, there is only one file to load: the configuration file.
 * This class may also be used to load other files in future...
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\Texts;
use PHPStreamsAggregator\Controllers\AstractProcessEvents;

/**
 * ProcessLoadProgramData
 */
class ProcessLoadProgramData extends AstractProcessEvents{

    /** @var Config - The instance of Config loaded */
    private $config;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = null;
        parent::__construct();
    }

    /**
     * Load configuration file
     */
    public function load()
    {

        // Load configuration file
        //

        $configLoader = ConfigLoader::create();
        $config = null;
		try{
			$config = $configLoader->load();
		}
		catch(\Exception $ex){
            $this->errorText = "Error while loading configuration file: " . $configLoader->getErrorText();
            return false;
		}
        unset($configLoader);

        // Define temp directory
        //

        if(!$this->checkConfigDirs($config)){
            return false;
        }

        // Set config directories
        $tempDir = null;
        if($config->getTempDir() !== null){
            $tempDir = $config->getTempDir();
        }
        else{
            $tmpTempDir = Data::$TEMP_ABSOLUTE_PATH;
            if(!file_exists($tmpTempDir) || !is_dir($tmpTempDir)){
                $this->errorText = 'Error: ' . 'Temp directory ' . $tmpTempDir . ' does not exists.';
                return false;
            }
            $tempDir = $tmpTempDir;
        }

        $this->config = $config;
        $this->tempDirectory = $tempDir;
        $this->setIsComplete();

    }

    /**
     * Check if the directories selected by user in configuration file exists.
     * @returns boolean
     */
    private function checkConfigDirs(&$config)
    {
        $errStr = "Error while checking configuration directories: ";
        $tempDir = $config->getTempDir();
        if($tempDir !== null){
            if(!file_exists($tempDir) || !is_dir($tempDir)){
                $this->errorText = ' Temp directory "' . $tempDir . '" defined as value "' .
                'temp_dir' . '" does not exists.' . PHP_EOL;
                return false;
            }

        }

        return true;

    }

    /**
     * Get the instance of ConfigLoader
     * @return &Config
     */
    public function &getConfig()
    {
        return $this->config;
    }

    /**
     * Get the instance of ConfigLoader
     * @return &Config
     */
    public function &getTempDirectory()
    {
        return $this->tempDirectory;
    }

}