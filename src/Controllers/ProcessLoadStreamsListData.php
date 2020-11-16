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
 * ProcessLoadStreamsListData
 * Top level class used by the program to load streams list data.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\Texts;
use PHPStreamsAggregator\Controllers\AstractProcessEvents;
use PHPStreamsAggregator\Controllers\LogManagerError as ErrorLog;
use PHPStreamsAggregator\Controllers\PluginsManager;
use PHPStreamsAggregator\Models\StreamsListState;

/**
 * ProcessLoadStreamsListData
 */
class ProcessLoadStreamsListData extends AstractProcessEvents{

    /** @var StreamsList - The instance of StreamsList loaded */
    private $streamsList;

    /** @var PluginsManager - The instance of PluginsManager */
    private $plugins;

    /** @var StreamsListState - The instance of StreamsListState */
    private $streamsState;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->streamsList = null;
        $this->plugins = null;
        parent::__construct();
    }

    /**
     * Load configuration file
     */
    public function load(&$prog, &$options, &$config, &$tempDir, $testMode, &$testReport)
    {


        // Select StreamsList file from Command line options OR configuration file
        //

        $listFile = null;
        $optsListFile = $options->getDownloadListFile();
        if($optsListFile !== null){
            $listFile = $optsListFile;
        }
        else if($config->getDownloadListFile() !== null){
            $listFile = $config->getDownloadListFile();
        }
        else{
            $errStr = 'No streams list file defined. Please define one in the configuration file or pass it as a command line argument. Program stopped.';
			ErrorLog::addLog($errStr);
            echo $errStr . "." . PHP_EOL;
            $prog->die();
        }

        $listFilename = $listFile->getFileName();
        $listFilepath = $listFile->getFilePath() . DIRECTORY_SEPARATOR . $listFile->getFileName();


		// Load streams list
        //

        $streamsListLoader = StreamsListLoader::create();
        $streamsList = null;
		try{
            $streamsList = $streamsListLoader->load($config, $listFilename, $listFilepath);
		}
		catch(\Exception $ex){
            if($testMode){
                $testReport->setStreamsListError($streamsListLoader->getErrorText());
                return; // Stop the function to prevent calling function "die()" in test mode.
            }
            $errStr = $streamsListLoader->getErrorText() . " Program stopped.";
			ErrorLog::addLog($errStr);
            echo $errStr . "." . PHP_EOL;
            $prog->die();
		}
        unset($streamsListLoader);
        if($testMode){
            $testReport->setStreamsListResult(true);
        }

        $this->streamsList = $streamsList;


        // Load plugins
        //

        $plugins = new PluginsManager();
        try{
            $plugins->load($config, $streamsList);
        }
        catch(\Exception $ex){
            if($testMode){
                $testReport->setPluginsError($plugins->getErrorText());
                return; // Stop the function to prevent "die()".
            }
            $errStr = $plugins->getErrorText() . " Program stopped.";
			ErrorLog::addLog($errStr);
            echo $errStr . PHP_EOL;
            $prog->die();
        }
        if($testMode){
            $testReport->setPluginsResult(true);
        }

        $this->plugins = $plugins;


        // Load or create state file
        //

		$streamsState = StreamsListState::create($tempDir, $listFile->getFileName());
        $this->streamsState = $streamsState;

        $this->setIsComplete();

    }

    /**
     * Get the instance of StreamsList
     * @return &StreamsList
     */
    public function &getStreamsList()
    {
        return $this->streamsList;
    }

    /**
     * Get the instance of PluginsManager
     * @return &PluginsManager
     */
    public function &getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Get the instance of StreamsListState
     * @return &StreamsListState
     */
    public function &getStreamsListState()
    {
        return $this->streamsState;
    }

}