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
 * Context
 * Class used to store and pass several data through the program.
 * An instance of Context is passed to all plugins, to all methods.
 */

namespace PHPStreamsAggregator\Models;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\Texts;
use PHPStreamsAggregator\Controllers\DirectoryParser;
use PHPStreamsAggregator\Controllers\FileStringParser;
use PHPStreamsAggregator\Library\Xml;
use PHPStreamsAggregator\Models\Config;
use PHPStreamsAggregator\Models\FileData;

/**
 * Context
 */
class Context{

    /** @var Options          - Options of the program. */
    private $options;

    /** @var StreamsListState - The state data. */
    private $streamsState;

    /** @var StreamsList      - The streams list. */
    private $streamsList;

    /** @var string           - The temporary directory. */
    private $tempDir;

    /** @var Config           - The configuration data (from configuration file). */
    private $config;

    /** @var DateTimeNumeric  - The current date. */
    private $currentDate;

    /** @var Plugins          - The plugins. */
    private $plugins;

    /** @var string|null      - The output file path (if defined). */
    private $outputFilePath;

    /** @var OutputFile       - The output file data (if defined). */
    private $outputFile;

    /** @var Array            - Alerts. */
    private $alerts;

    /** @var Array            - Events. */
    private $events;

	/**
	 * Constructor
	 */
    public function __construct(
        &$options,
        &$currentDate,
        &$streamsState,
        &$streamsList,
        &$config,
        &$tempDir,
        &$plugins,
        &$outputFilePath,
        &$outputFile
    )
    {
        $this->options = $options;
        $this->currentDate = $currentDate;
        $this->streamsState = $streamsState;
        $this->streamsList = $streamsList;
        $this->config = $config;
        $this->tempDir = $tempDir;
        $this->plugins = $plugins;
        $this->alerts = [];
        $this->events = [];
        $this->outputFilePath = $outputFilePath;
        $this->outputFile = $outputFile;
    }

    /**
     * Add an alert event / message
     * @param string The key/id of the alert
     * @param Array|null An associative array containing arguments
     */
    public function addAlert($key, $arguments = null)
    {
        $this->alerts[$key] = $arguments;
        return true;
    }

    /**
     * Add an alert event / message
     * @param string The key/id of the alert
     * @param Array|null An associative array containing arguments
     */
    public function addEvent($key, $arguments = null)
    {
        $this->events[$key] = $arguments;
        return true;
    }

    /**
     * Get the configuration data (from configuration file).
     * @returns &Config
     */
    public function &getConfig()
    {
        return $this->config;
    }

    /**
     * Get the current date.
     * @returns &DateTimeNumeric
     */
    public function &getCurrentDate()
    {
        return $this->currentDate;
    }

    /**
     * Get output file data if it has been defined, or Null.
     * @returns &OutputFile|null
     */
    public function &getOutputFile()
    {
        return $this->outputFile;
    }

    /**
     * Get the program options.
     * @returns &Options
     */
    public function &getOptions()
    {
        return $this->options;
    }

    /**
     * Get the plugins.
     * @returns &Plugins
     */
    public function &getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Get the streams list.
     * @returns &StreamsList
     */
    public function &getStreamsList()
    {
        return $this->streamsList;
    }

    /**
     * Get the streams state.
     * @returns &StreamsListState
     */
    public function &getStreamsState()
    {
        return $this->streamsState;
    }

    /**
     * Get the temporary directory.
     * @returns &string
     */
    public function &getTempDirectory()
    {
        return $this->tempDir;
    }

    /**
     * Get alerts
     * @returns &Array
     */
    public function &getAlerts()
    {
        return $this->alerts;
    }

    /**
     * Get events
     * @returns &Array
     */
    public function &getEvents()
    {
        return $this->events;
    }

    /**
     * Get output file path if it has been defined, or Null.
     * @returns string|null
     */
    public function getOutputFilePath()
    {
        if($this->streamsList->getOutputFile() !== null){
            $f = $this->streamsList->getOutputFile();
            return $f->getFilePath() . DIRECTORY_SEPARATOR . $f->getFileName();
        }
        else{
            return null;
        }
    }

}