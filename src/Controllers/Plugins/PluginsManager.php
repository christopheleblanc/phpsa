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
 * PluginsManager
 * Class used to load and manage the plugin classes loaded for a context/streams
 * list.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\ParserTypes;
use PHPStreamsAggregator\Texts;
use PHPStreamsAggregator\Controllers\LogManagerError as ErrorLog;

/**
 * PluginsManager
 */
class PluginsManager{

    public const EVENT_FUNC_NAME = 'onEvent';

    /**
     * Array containing the class names of the loaded plugins of type "Parser".
     * @var String[]
     */
    private $parsers;

    /**
     * Array containing the class names of the loaded plugins of type "Mixer".
     * @var String[]
     */
    private $mixers;

    /**
     * Array containing the class names of the loaded plugins of type "Validator".
     * @var String[]
     */
    private $validators;

    /**
     * Array containing the loaded plugins of type "Runner".
     * @var AbstractRunner[string]
     */
    private $runners;

    /**
     * Array containing the names of loaded plugins of type "Runner".
     * @var String[string]
     */
    private $runnersClassNames;

    /**
     * Array containing the loaded plugins of type "Runner".
     * @var AbstractRunner[string]
     */
    private $makers;

    /**
     * The error text defined when returning an error or throwing an exception while
     * processing the function "load()".
     * Note: If you try to get the error text before calling the function "load()",
     * or if the function "load()" did not return any error or throw any Exception,
     * you will receive a "No errors" message.
     * @var string
     */
    private $errorText;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parsers = [];
        $this->mixers = [];
        $this->validators = [];
        $this->errorText = null;
        $this->runners = [];
        $this->runnersClassNames = [];
        $this->makers = [];
    }

    /**
     * Load plugins
     * @throw Exception when an error occured while loading plugins.
     */
    public function load(&$config, &$streamsList)
    {
        // Load Parsers
		try{
			$this->loadParsers($streamsList, $this->parsers);
		}
		catch(\Exception $ex){
            throw new \Exception();
		}

        // Load Runners
		try{
			$this->loadRunners($streamsList, $this->runnersClassNames);
		}
		catch(\Exception $ex){
            throw new \Exception();
		}

        // Load sorters (unused)
		//try{
			//Plugins::loadSorters($this->streamsList, $this->sortersArray);
		//}
		//catch(\Exception $ex){
		//}

        // Load Mixer
        try{
            $this->loadMixer($streamsList, $this->mixers);
        }
        catch(\Exception $ex){
            throw new \Exception();
		}

        // Load Maker
        try{
            $this->loadMaker($streamsList, $this->makers);
        }
        catch(\Exception $ex){
            throw new \Exception();
		}

        // Load validator
        try{
            $this->loadValidator($streamsList, $this->validators);
        }
        catch(\Exception $ex){
            throw new \Exception();
		}
    }

    /**
     * Load the parsers files and classes.
     * @throw Exception if an error has occurred when trying to load a file.
     * @param StreamsList The streams list.
     * @param &array[] The array intended to store the names of loaded parsers.
     */
    private function loadParsers(&$streamsList, &$parsersArray)
    {

        $parsersLoadedGroupsArray = [];

        $errors = 0;
        $included = [];

        foreach($streamsList->getChildren() as $groupKey => $groupNode){

            $parsersLoadedNodesArray = [];

            foreach($groupNode->getChildren() as $nodeKey => $node){

                $includePath = Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con:: PLUGINS_DIR_NAME . DIRECTORY_SEPARATOR . Con::PARSERS_DIR_NAME .
                DIRECTORY_SEPARATOR . $node->getParserName() . ".php";

                if(!in_array($node->getParserName(), $included)){

                    if(file_exists($includePath)) {

                        $requireSuccess = require_once($includePath);
                        if (!$requireSuccess) {
                            $this->errorText = Texts\loadPluginIncludeFailed($node->getParserName());
                            throw new \Exception();
                        }
                        else{

                            $classIsTyped = false;

                            $className = $node->getParserName();
                            $realClassname = Con::PARSERS_NAMESPACE . $className;
                            if(isset(($realClassname)::$type)){
                                $parserType = ($realClassname)::$type;
                                if($parserType !== null){
                                    if(ParserTypes::isValid($parserType)){
                                        $classIsTyped = true;
                                    }
                                }
                            }

                            if($classIsTyped){
                                array_push($included, $node->getParserName());
                                $loaded = true;
                            }
                            else{
                                $this->errorText = Texts\loadParserUntyped($node->getParserName());
                                throw new \Exception();
                            }

                        }

                    }
                    else{
                        $this->errorText = Texts\loadPluginFileNotExists($node->getParserName(), $includePath);
                        throw new \Exception();
                    }

                }
                else{
                    $loaded = true;
                }

                if(!$loaded){
                    $this->errorText = Texts\includePluginError($node->getParserName());
                    throw new \Exception();
                }

                $parsersLoadedNodesArray[$nodeKey] = $loaded;

            }

            $parsersLoadedGroupsArray[$groupKey] = $parsersLoadedNodesArray;

        }

        if($errors > 0){
            throw new \Exception();
        }
        else{

            foreach($included as $loadedParserName){
                if(!in_array($loadedParserName, $parsersArray)){
                    $parsersArray[] = $loadedParserName;
                }
            }

        }

    }

    /**
     * Load the runners files and classes.
     * @throw Exception if an error has occurred when trying to load a file.
     * @param StreamsList The streams list.
     * @param &array[] The array intended to store the names of loaded parsers.
     */
    private function loadRunners(&$streamsList, &$runnersArray)
    {
        foreach($streamsList->getRunnersClassNames() as $className){
            if(!array_key_exists($className, $runnersArray)){
                $includePath = Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con:: PLUGINS_DIR_NAME . DIRECTORY_SEPARATOR . Con::RUNNERS_DIR_NAME .
                DIRECTORY_SEPARATOR . $className . ".php";
                if(file_exists($includePath)){
                    $requireSuccess = require_once($includePath);
                    if (!$requireSuccess) {
                        $this->errorText = Texts\loadPluginIncludeFailed($className);
                        throw new \Exception();
                    }
                    else{

                        $realClassname = Con::RUNNERS_NAMESPACE . $className;
                        if(class_exists($realClassname)){
                            $runnersArray[$className] = $className;
                        }

                    }
                }
            }
        }
    }

    /**
     * Load the mixers files and classes.
     * @param &StreamsList Instance of StreamsList
     * @param &array[] The array intended to store the names of loaded mixers.
     * @throw Exception if an error has occurred when trying to load a file.
     */
    private function loadMixer(&$streamsList, &$mixersArray)
    {
        $errors = 0;
        $included = [];

        if($streamsList->getMixerClassName() !== null){

            $classname = $streamsList->getMixerClassName();
            $includePath = Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con:: PLUGINS_DIR_NAME . DIRECTORY_SEPARATOR . Con::MIXERS_DIR_NAME .
            DIRECTORY_SEPARATOR . $classname . ".php";

            $loaded;

            if(!in_array($classname, $included)){
                if(file_exists($includePath)) {

                    $requireSuccess = require_once($includePath);
                    if (!$requireSuccess) {
                        $this->errorText = Texts\loadPluginIncludeFailed($classname);
                        throw new \Exception();
                    }
                    else{
                        array_push($included, $classname);
                        $loaded = true;
                    }

                }
                else{
                    $this->errorText = Texts\loadPluginFileNotExists($classname, $includePath);
                    throw new \Exception();
                }

                if(!$loaded){
                    $this->errorText = Texts\includePluginError($classname);
                    throw new \Exception();
                }
            }
            else{
                $loaded = true;
            }

        }

        if($errors > 0){
            throw new \Exception();
        }
        else{

            foreach($included as $loadedMixerName){
                if(!in_array($loadedMixerName, $mixersArray)){
                    $mixersArray[] = $loadedMixerName;
                }
            }

        }

    }

    /**
     * Load the mixers files and classes.
     * @param &StreamsList Instance of StreamsList
     * @param &array[] The array intended to store the names of loaded mixers.
     * @throw Exception if an error has occurred when trying to load a file.
     */
    private function loadMaker(&$streamsList, &$makersArray)
    {
        $errors = 0;
        $included = [];

        if($streamsList->getMakerClassName() !== null){

            $classname = $streamsList->getMakerClassName();
            $includePath = Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con:: PLUGINS_DIR_NAME . DIRECTORY_SEPARATOR . Con::MAKERS_DIR_NAME .
            DIRECTORY_SEPARATOR . $classname . ".php";

            $loaded;

            if(!in_array($classname, $included)){
                if(file_exists($includePath)) {

                    $requireSuccess = require_once($includePath);
                    if (!$requireSuccess) {
                        $this->errorText = Texts\loadPluginIncludeFailed($classname);
                        throw new \Exception();
                    }
                    else{
                        array_push($included, $classname);
                        $loaded = true;
                    }

                }
                else{
                    $this->errorText = Texts\loadPluginFileNotExists($classname, $includePath);
                    throw new \Exception();
                }

                if(!$loaded){
                    $this->errorText = Texts\includePluginError($classname);
                    throw new \Exception();
                }
            }
            else{
                $loaded = true;
            }

        }

        if($errors > 0){
            throw new \Exception();
        }
        else{

            foreach($included as $loadedMakerName){
                if(!in_array($loadedMakerName, $makersArray)){
                    $makersArray[] = $loadedMakerName;
                }
            }

        }

    }

    /**
     * Load the validators files and classes.
     * @param &StreamsList Instance of StreamsList
     * @param &array[] The array intended to store the names of loaded validators.
     * @throw Exception if an error has occurred when trying to load a file.
     */
    private function loadValidator(&$streamsList, &$validatorsArray)
    {
        $errors = 0;
        $included = [];

        if($streamsList->getValidatorClassName() !== null){

            $classname = $streamsList->getValidatorClassName();
            $includePath = Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con:: PLUGINS_DIR_NAME . DIRECTORY_SEPARATOR . Con::VALIDATORS_DIR_NAME .
            DIRECTORY_SEPARATOR . $classname . ".php";

            $loaded;

            if(!in_array($classname, $included)){
                if(file_exists($includePath)) {

                    $requireSuccess = require_once($includePath);
                    if (!$requireSuccess) {
                        $this->errorText = Texts\loadPluginIncludeFailed($classname);
                        throw new \Exception();
                    }
                    else{
                        array_push($included, $classname);
                        $loaded = true;
                    }

                }
                else{
                    $this->errorText = Texts\loadPluginFileNotExists($classname, $includePath);
                    throw new \Exception();
                }

                if(!$loaded){
                    $this->errorText = Texts\includePluginError($classname);
                    throw new \Exception();
                }
            }
            else{
                $loaded = true;
            }

        }

        if($errors > 0){
            throw new \Exception();
        }
        else{

            foreach($included as $loadedValidatorName){
                if(!in_array($loadedValidatorName, $validatorsArray)){
                    $validatorsArray[] = $loadedValidatorName;
                }
            }

        }

    }

    /**
     * Check if a plugin of type "Parser" is loaded
     * @param string - The class name of the plugin
     * @returns boolean - True if the class has been loaded, or False.
     */
    public function parserIsLoaded($className)
    {
        return (in_array($className, $this->parsers));
    }

    /**
     * Check if at least one plugin of type "Parser" is loaded
     * @returns boolean - True if at least one plugin of type "Parser" is loaded.
     */
    public function hasParsers()
    {
        return (count($this->parsers) > 0);
    }

    /**
     * Get runners
     * @returns Object[]
     */
    public function &getRunners()
    {
        return $this->runners;
    }

    /**
     * Check if at least one plugin of type "Parser" is loaded
     * @returns boolean - True if at least one plugin of type "Parser" is loaded.
     */
    public function getRunnersClassNames()
    {
        return $this->runnersClassNames;
    }

    /**
     * Check if a plugin of type "Mixer" is loaded
     * @param string - The class name of the plugin
     * @returns boolean - True if the class has been loaded, or False.
     */
    public function mixerIsLoaded($className)
    {
        return (in_array($className, $this->mixers));
    }

    /**
     * Check if a plugin of type "Mixer" is loaded
     * @param string - The class name of the plugin
     * @returns boolean - True if the class has been loaded, or False.
     */
    public function makerIsLoaded($className)
    {
        return (in_array($className, $this->makers));
    }

    /**
     * Check if a plugin of type "Validator" is loaded
     * @param string - The class name of the plugin
     * @returns boolean - True if the class has been loaded, or False.
     */
    public function validatorIsLoaded($className)
    {
        return (in_array($className, $this->validators));
    }

    /**
     * Check if a plugin of type "Validator" is loaded
     * @param string - The class name of the plugin
     * @returns boolean - True if the class has been loaded, or False.
     */
    public function dispatcheEvent(&$context, $event, $arguments = null)
    {
        $funcName = self::EVENT_FUNC_NAME;
        foreach($this->runners as $key => $runner){
            if(method_exists($runner, $funcName)){
                $runner->$funcName($context, $event, $arguments);
            }
        }
    }

    public function dispatchCall(&$context, $funcName, $arguments = null)
    {
        foreach($this->runners as $key => $runner){
            if(method_exists($runner, $funcName)){
                $runner->$funcName($context, $arguments);
            }
        }
    }

    public function dispatch(&$context, $eventName, $funcName, $arguments = null)
    {
        $this->dispatcheEvent($context, $eventName, $arguments);
        $this->dispatchCall($context, $funcName, $arguments);
    }

    /**
     * Get the error text.
     * Note: Please note that you must use this function AFTER catching an Exception from the
     * function "load()". Furthermore, this function will return the text "No errors" if no
     * error text has been returned when loading.
     * @returns string - The error text.
     */
    public function getErrorText()
    {
        if($this->errorText === null){
            return Texts\errorNoError();
        }
        return $this->errorText;
    }

}