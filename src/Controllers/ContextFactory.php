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
 * ContextFactory
 * Class used to create an instance of Context which can be passed through the 
 * program/plugins. This is mostly a copy of the class Context, plus the ability
 * to set the values and create an instance of Context.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Controllers\OutputFileFactory;
use PHPStreamsAggregator\Models\Context;

/**
 * ContextFactory
 */
class ContextFactory{

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

	/**
	 * Constructor
	 */
    public function __construct()
    {
        $this->options = null;
        $this->currentDate = null;
        $this->streamsState = null;
        $this->streamsList = null;
        $this->config = null;
        $this->tempDir = null;
        $this->plugins = null;
        $this->outputFilePath = null;
        $this->outputFile = null;
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
     * Get output file data if it has been defined, or Null.
     * @returns &OutputFile|null
     */
    public function &getOutputFile()
    {
        return $this->outputFile;
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

    public function &getAlerts()
    {
        return $this->alerts;
    }

    public function &getEvents()
    {
        return $this->events;
    }

    /**
     * Set the configuration data
     * @param &Config - The configuration data
     */
    public function setConfig(&$config)
    {
        $this->config = $config;
    }

    /**
     * Set the output file data
     * @param &OutputFile - The output file data
     */
    public function setOutputFile($o)
    {
        $this->outputFile = $o;
    }

    /**
     * Set the output file path
     * @param string - The output file path
     */
    public function setOutputFilePath($path)
    {
        $this->outputFilePath = $path;
    }

    /**
     * Set the current date
     * @param &DateTimeNumeric - The current date
     */
    public function setCurrentDate(&$currentDate)
    {
        $this->currentDate = $currentDate;
    }

    /**
     * Set the program options
     * @param &Options - The program options
     */
    public function setOptions(&$options)
    {
        $this->options = $options;
    }

    /**
     * Set the plugins
     * @param &Plugins - The plugins
     */
    public function setPlugins(&$plugins)
    {
        $this->plugins = $plugins;
    }

    /**
     * Set the streams list state data
     * @param &StreamsListState - The streams list state data
     */
    public function setStreamsState(&$streamsState)
    {
        $this->streamsState = $streamsState;
    }

    /**
     * Set the streams list
     * @param &StreamsList - The streams list
     */
    public function setStreamsList(&$streamsList)
    {
        $this->streamsList = $streamsList;
    }

    /**
     * Set the temporary directory
     * @param string - The temporary directory
     */
    public function setTempDirectory(&$tempDir)
    {
        $this->tempDir = $tempDir;
    }

    /**
     * Dispatch event and/or function to the program/plugins
     * @param string      - The Event key
     * @param mixed       - string[The function name] | Array[The event arguments]
     * @param mixed       - Array[The event/functions arguments] | null
     * @returns  boolean  - True in case of success, or False.
     */
    public function dispatch($eventKey, $mixed1 = null, $mixed2 = null)
    {
        if($mixed1 === null || ($mixed1 !== null && is_array($mixed1))){
            $this->plugins->dispatchEvent($this, $eventKey, $mixed1);
            return true;
        }
        else if($mixed1 !== null && is_string($mixed1)){
            $this->plugins->dispatch($this, $eventKey, $mixed1, $mixed2);
            return true;
        }
        return false;
    }

    /**
     * Dispatch events to the program/plugins
     * @param Array - Events
     */
    public function dispatchEvents(&$events)
    {
        foreach($events as $key => $value){
            $this->plugins->dispatchEvent($this, $key, $value);
        }
    }

    /**
     * Dispatch an alert to the program/plugins
     * @param string - Alert "key"
     * @param Array  - Arguments
     * @returns boolean True in case of success, of False
     */
    public function dispatchAlert($key, $arguments = null)
    {
        $args = ["alert" => $key];
        if($arguments !== null && is_array($arguments)){
            $args = array_merge($args, $arguments);
        }
        $this->plugins->dispatch($this, "alert", "onAlert", $args);
        return true;
    }

    /**
     * Dispatch alerts to the program/plugins
     * @param Array - Alerts
     */
    public function dispatchAlerts(&$alerts)
    {
        foreach($alerts as $key => $value){
            $this->dispatchAlert($key, $value);
        }
    }

    /**
     * Dispatch all events and alerts from a Context.
     * @param &Context - The context
     */
    public function dispatchAll(&$context)
    {
        $this->dispatchAlerts($context);
    }

    /**
     * Create an instance of Context
     * @returns &Context
     */
    public function &getContext()
    {
        $outputFile;
        if($this->outputFile !== null){
            $outputFile = $this->outputFile->getContextualizable();
        }
        else{
            $outputFile = null;
        }
        $new = new Context(
            $this->options,
            $this->currentDate,
            $this->streamsState,
            $this->streamsList,
            $this->config,
            $this->tempDir,
            $this->plugins,
            $this->outputFilePath,
            $outputFile
        );
        return $new;
    }
}