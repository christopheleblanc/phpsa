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
 * StateViewer
 * Classe intended to allow user to visualize state in web browser.
 */

namespace PHPStreamsAggregator;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\Controllers\ConfigLoader;
use PHPStreamsAggregator\Controllers\DataChecker;
use PHPStreamsAggregator\Controllers\FileStringParser;
use PHPStreamsAggregator\Controllers\StreamsListLoader;
use PHPStreamsAggregator\Controllers\Update;
use PHPStreamsAggregator\Models\DateTimeNumeric;
use PHPStreamsAggregator\Models\FileData;
use PHPStreamsAggregator\Models\StreamsListCompleteState;
use PHPStreamsAggregator\Models\StreamsListState;
use PHPStreamsAggregator\Models\UpdateOptionTypes;

/**
 * StateViewer
 */
class StateViewer{

	/** @var DateTimeNumeric The current date. */
	private $currentDate;

	/** @var StreamsListCompleteState The state of the streams defined in the streams list. */
	private $streamsState;

    /**
     * Constructor
     */
    public function __construct($fileName = null)
    {

        $this->currentDate = new DateTimeNumeric();
        $this->streamsState = null;


        // Create static "instances"
        Data::instanciate();


        // Check program directories
        //

        $dataChecker = new DataChecker();
        if(!$dataChecker->check()){

            $errStr = "";
            $dirs = $dataChecker->getMissingDirectories();
            $tot = count($dirs);
            $last = $tot - 1;
            for($i = 0; $i < $tot; $i++){
                $dir = $dirs[$i];
                $errStr .= $appDir;
                if($i < $last){
                    $errStr .= ", ";
                }
            }
            $dirsStr = ($tot > 1) ? "directories are missing" : "directory is missing";
            throw new \Exception('Error while checking program data directories. ' . $tot . ' ' . $dirsStr . ': ' . $errStr . '.');
        }


        // Load configuration file
        //

        $config = null;
        $configLoader = ConfigLoader::create();
		try{
			$config = $configLoader->load();
		}
		catch(\Exception $ex){
			throw new \Exception('Error while loading configuration file: . ' . $configLoader->getErrorText() . '.');
		}
        unset($configLoader);


        // Select streams list  file from class parameter OR configuration file
        //

        $listFile = null;
        if($fileName !== null){

            // Parse the value to check if value "output" is a filename, an absolute path or a relative path

            $parser = new FileStringParser();
            $parser->parse($fileName);

            $absolutePath;
            switch($parser->getValueType()){
                case FileData::ABSOLUTE_PATH:{
                    $absolutePath = $parser->getFilePath();
                }break;
                case FileData::RELATIVE_PATH:{
                    $absolutePath = Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . $parser->getFilePath();
                }break;
                case FileData::FILENAME:{
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
        else if($config->getDownloadListFile() !== null){
            $listFile = $config->getDownloadListFile();
        }
        else{
            $errStr = 'No streams list file defined. Please define one in the configuration file or pass it as a constructor parameter.';
			throw new \Exception($errStr);
        }


		// Load streams list
        //

        $listFilename = $listFile->getFileName();
        $listFilepath = $listFile->getFilePath() . DIRECTORY_SEPARATOR . $listFile->getFileName();

        $streamsList = null;
        $streamsListLoader = StreamsListLoader::create();
		try{
            $streamsList = $streamsListLoader->load($config, $listFilename, $listFilepath);
		}
		catch(\Exception $ex){
            $errStr = $streamsListLoader->getErrorText();
			throw new \Exception($errStr);
		}
        unset($streamsListLoader);


        // Load state file
        //

        $stateFilepath = Data::$TEMP_ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con::TEMP_STATE_DIR_NAME .
        DIRECTORY_SEPARATOR . $listFile->getFileName();

        $streamsState = null;
        if(file_exists($stateFilepath)){
            $streamsState = StreamsListState::create($stateFilepath);
        }
        else{
            throw new \Exception('State file not found.');
        }


        // Create an instance of StreamsListCompleteState which can be easily used to access
        // all states of the streams list.
        //

        $isUpToDate = Update::areGroupsUpToDate($this->currentDate, $streamsList, $streamsState);
        $this->streamsState = StreamsListCompleteState::create($streamsState, $isUpToDate);

    }

    /**
     * Get the current date
     * @return &DateTimeNumeric
     */
    public function getCurrentDate()
    {
        return $this->currentDate;
    }

    /**
     * Get the current date
     * @return &DateTime
     */
    public function &getCurrentDateTime()
    {
        return $this->currentDate->getDateTime();
    }

    /**
     * Get the StreamsListCompleteState
     * @return StreamsListCompleteState
     */
    public function &getState()
    {
        return $this->streamsState;
    }

    /**
     * Create an instance of StateViewer
     * @return StateViewer
     */
    static public function create($fileName = null)
    {
        return new StateViewer($fileName);
    }

}