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
 * ConfigLoader
 * Class intended to load the configuration file and create an instance of class
 * "Config" from it.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\Texts;
use PHPStreamsAggregator\Controllers\DirectoryParser;
use PHPStreamsAggregator\Controllers\FileStringParser;
use PHPStreamsAggregator\Library\Xml;
use PHPStreamsAggregator\Models\Config;
use PHPStreamsAggregator\Models\FileData;

/**
 * ConfigLoader
 */
class ConfigLoader{

    /** @var string - Error text **/
    private $errorText;

    /** @var boolean - Defines if the file exists. **/
    private $isFileExisting;

	/**
	 * Constructor
	 */
    public function __construct()
    {
        $this->errorText = null;
        $this->isFileExisting = false;
    }

    /**
     * Load the configuration file and create an instance of "Config".
     * @throw Exception if an error has been found.
     * @returns Config - An instance of Config.
     */
	public function load()
    {

		$path = Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con::DATA_DIR_NAME .
        DIRECTORY_SEPARATOR . Con::CONFIG_DIR_NAME . DIRECTORY_SEPARATOR . Con::CONFIG_FILENAME;

        // Default values
        $tempDir = null;
        $active = true;
        $urlsDelay = 0;
        $listFile = null;

        if(!file_exists($path)){
            /*
            // Stop the program if there is no config file
            $this->errorText = Texts\errorFileDoesNotExists();
            throw new \Exception();
            */
            // Return default values
            return new Config(
                $active,
                $tempDir,
                $listFile,
                $urlsDelay,
            );
        }

        $this->isFileExisting = true;

		$this->simpleXml = @simplexml_load_file($path);
		if($this->simpleXml === false){
            $this->errorText = Texts\errorXml();
			throw new \Exception();
		}

		// Parse the file
		//

		$errs = 0;

        // Get option "temp_dir"
        if(isset($this->simpleXml->temp_dir)){
			$tmp = (string)$this->simpleXml->temp_dir;
            if(strlen($tmp) > 0){

                $parser = new DirectoryParser();
                $parser->parse($tmp);
                if($parser->getIsRootOnly()){
                    //echo "IS ROOT ONLY" . PHP_EOL;
                    $this->errorText = Texts\errorDirRootOnly("temp_dir");
                    throw new \Exception();
                }
                else{
                    //echo "IS NOT ROOT ONLY" . PHP_EOL;
                    $absolutePath;
                    switch($parser->getValueType()){
                        case FileData::ABSOLUTE_PATH:{
                            $tempDir = $parser->getPath();
                        }break;
                        case FileData::RELATIVE_PATH:{
                            $tempDir = Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . $parser->getPath();
                        }break;
                    }
                }

            }
		}

        // Get option "active"
		if(isset($this->simpleXml->active)){

            $ref = (string)$this->simpleXml->active;
			$tmp = Xml\attrIsTrue($ref);
			if($tmp !== null){
				$active = $tmp;
			}
			else{
                $this->errorText = Texts\errorUncorrectValue("active");
                throw new \Exception();
			}

		}

        // Get option "urls_delay"
        if(isset($this->simpleXml->urls_delay)){
			$tmp = (string)$this->simpleXml->urls_delay;
            if(strlen($tmp) > 0){
                $tmpInt = intval($tmp);
                $urlsDelay = $tmpInt;
            }
		}

        // Get option "list"
        if(isset($this->simpleXml->list)){
			$tmp = (string)$this->simpleXml->list;
            if(strlen($tmp) > 0){

                $parser = new FileStringParser();
                $parser->parse(trim($tmp));

                $absolutePath;
                switch($parser->getValueType()){
                    case FileData::ABSOLUTE_PATH:{
                        $absolutePath = $parser->getFilePath();
                    }break;
                    case FileData::RELATIVE_PATH:{
                        $absolutePath = Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . $parser->getFilePath();
                    }break;
                    case FileData::FILENAME:{
                        // Default file directory
                        $absolutePath = Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con::DATA_DIR_NAME .
                        DIRECTORY_SEPARATOR . Con::CONFIG_DIR_NAME;
                    }break;
                }

                $listFile = new FileData(
                    $parser->getValueType(),
                    $parser->getFileName(),
                    $absolutePath
                );

                unset($parser);

            }
		}


        // Add optional values / unregistered values into an array of string.
        // All options (registered and unregistered) will be available by their id/node name.
        $defaultValues = [
            "active" => $active,
            "urls_delay" => $urlsDelay,
            "list" => $listFile,
            "temp_dir" => $tempDir
        ];

        $unregisteredValues = [];
        foreach($defaultValues as $key => $node){
            $unregisteredValues[$key] = $node;
        }
        foreach($this->simpleXml->children() as $key => $node){
            if(!array_key_exists($key, $defaultValues)){
                $unregisteredValues[$key] = (string)$node;
            }
        }

		if($errs > 0){
			throw new \Exception();
		}

        return new Config(
            $active,
            $tempDir,
            $listFile,
            $urlsDelay,
            $unregisteredValues
        );

	}

    /**
     * Get the error message corresponding to the exception/error catched when loading
     * the configuration file.
     * Note: This function will return the message "No error." if no error has been detected.
     * @returns string - The error message.
     */
    public function getErrorText()
    {
        if($this->errorText === null){
            return Texts\errorNoError();
        }
        else{
            return $this->errorText;
        }

    }

    /**
     * Create an instance of this class.
     * @returns ConfigLoader
     */
    static public function create()
    {
        return new ConfigLoader();
    }

}