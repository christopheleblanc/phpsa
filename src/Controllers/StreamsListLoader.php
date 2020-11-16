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
 * StreamsListLoader
 * Class responsible of loading a streams list file and create an instance
 * of StreamsList.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\Texts;
use PHPStreamsAggregator\Controllers\FileStringParser;
use PHPStreamsAggregator\Controllers\MaximumDelayParser;
use PHPStreamsAggregator\Controllers\MultiClassNamesParser;
use PHPStreamsAggregator\Controllers\UpdateOptionsParser;
use PHPStreamsAggregator\Library\Xml;
use PHPStreamsAggregator\Models\StreamsList;
use PHPStreamsAggregator\Models\StreamsGroup;
use PHPStreamsAggregator\Models\StreamFTP;
use PHPStreamsAggregator\Models\StreamPath;
use PHPStreamsAggregator\Models\StreamURL;
use PHPStreamsAggregator\Models\StreamTypes;
use PHPStreamsAggregator\Models\FileData;
use PHPStreamsAggregator\Models\UpdateOptionEach;

/**
 * StreamsListLoader
 */
class StreamsListLoader{

    /** @var string - Error text **/
    private $errorText;

    /** @var string[] - Loaded node ids. Used to verify that each id is unique. **/
    private $nodeIds;

    /**
	 * Constructor
	 */
	public function __construct()
    {
        $this->errorText = null;
        $this->nodeIds = [];
	}

	/**
	 * Load File
     * @param &Config - An instance of Config
     * @param String  - The name of the file
     * @param String  - The absolute path of the file
	 */
	public function load(&$config, &$filename, &$filepath)
    {

		$nodeList = [];
        $groupsArray = [];

        if(!file_exists($filepath) || is_dir($filepath)){
            $this->errorText = Texts\errorFileDoesNotExists();
			throw new \Exception();
        }

        // Notes: If you got errors when loading the XML file,
        // Be sure that the file is formatted correctly and URLs are escaped!
        // "&" caracters in URLs must be converted to "&amp;"
		$simpleXml = @simplexml_load_file($filepath);
		if($simpleXml === false){
            $this->errorText = Texts\errorXml();
			throw new \Exception();
		}

        $errs = 0;

        // Load configuration data
        //

        $isActive = true;
        $activeDefined = false;
        $urlsDelay = 0;
        $makerClassName = null;
        $validatorClassName = null;
        $outputFile = null;
        $mixerClassName = null;
        $runnersClassNames = [];

		if(isset($simpleXml["active"])){

            $ref = (string)$simpleXml["active"];
			$tmp = Xml\attrIsTrue($ref);
			if($tmp !== null){
				$isActive = $tmp;
                $activeDefined = true;
			}
			else{
                $this->errorText = Texts\errorUncorrectValue("active");
                throw new \Exception();
			}

		}

		if(isset($simpleXml["run"])){

            $tmp = (string)$simpleXml["run"];
			if(strlen($tmp) > 0){
				$parser = new MultiClassNamesParser();
                try{
                    $runnersClassNames = $parser->parse($tmp);
                }
                catch(\Exception $ex){
                    $this->errorText = $parser->getErrorText();
                    throw new \Exception();
                }
			}
			else{
			}

		}

        if(isset($simpleXml["maker"])){
			$tmp = (string)$simpleXml["maker"];
            if(strlen($tmp) > 0){
                if(strpos($tmp, '\\') !== false){
                    $this->errorText = Texts\errorPluginClassNameBackslashes("maker");
                    throw new \Exception();
                }
                else{
                    $makerClassName = $tmp;
                }
            }
		}

        if(isset($simpleXml["output"])){
			$tmp = (string)$simpleXml["output"];
            if(strlen($tmp) > 0){

                // Parse the value to check if value "output" is a filename, an absolute path or a relative path

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
                        $absolutePath = Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con::DATA_DIR_NAME .
                        DIRECTORY_SEPARATOR . Con::OUTPUT_DIR_NAME;
                    }break;
                }
                $outputFile = new FileData(
                    $parser->getValueType(),
                    $parser->getFileName(),
                    $absolutePath
                );

                unset($parser);

            }
		}

        if(isset($simpleXml["urls_delay"])){
			$tmp = (string)$simpleXml["urls_delay"];
            if(strlen($tmp) > 0){
                $tmpInt = intval($tmp);
                $urlsDelay = $tmpInt;
            }
		}

        if(isset($simpleXml["validator"])){
			$tmp = (string)$simpleXml["validator"];
            if(strlen($tmp) > 0){
                if(strpos($tmp, '\\') !== false){
                    $this->errorText = Texts\errorPluginClassNameBackslashes("validator");
                    throw new \Exception();
                }
                else{
                    $validatorClassName = $tmp;
                }
            }
		}

        if(isset($simpleXml["mixer"])){
			$tmp = (string)$simpleXml["mixer"];
            if(strlen($tmp) > 0){
                if(strpos($tmp, '\\') !== false){
                    $this->errorText = Texts\errorPluginClassNameBackslashes("mixer");
                    throw new \Exception();
                }
                else{
                    $mixerClassName = $tmp;
                }
            }
		}


        // Compare to Config and use Config values if the StreamsList File does not define
        // values that have been defined in configuration.
        //

        if(!$activeDefined && $config->isActive() !== null){
            $isActive = $config->isActive();
        }

        if($urlsDelay === 0 && $config->getUrlsDelay() !== 0){
            $urlsDelay = $config->getUrlsDelay();
        }


        // Load list
        //

        // Check if the program is configured to load all nodes or only one group.
        $nodeGroupId = null;
        $listId = null;


		// Parse the file and build the list
		//

        // Load groups if they exists
        if(isset($simpleXml->group)){
            foreach($simpleXml->group as $group){

                // Check if the group has an ID
                $nodeGroupExists;
                $groupNodeId;
                $groupNodesArray;
                if(isset($group["id"])){
                    $nodeGroupExists = true;
                    $groupNodeId = (string)$group["id"];
                    $groupNodesArray = [];
                }
                else{
                    $nodeGroupExists = false;
                    $groupNodeId = null;
                    $groupNodesArray = &$nodeList;
                }

                // Check if Group is "Active"
                // TRUE by default
                $groupIsActive = null;
                if(isset($node["active"])){
                    $tmp = Xml\attrIsTrue($node["active"]);
                    if($tmp !== null){
                        $groupIsActive = $tmp;
                    }
                    else{
                        $groupIsActive = true;
                    }
                }
                else{
                    $groupIsActive = true;
                }

                $updateOptions = null;
                try{
                    $updateOptions = $this->getUpdateOptions($group);
                }
                catch(\Exception $ex){
                    throw $ex;
                }

                $maxDelay = null;
                try{
                    $maxDelay = $this->getMaxDelay($group);
                }
                catch(\Exception $ex){
                    throw $ex;
                }

                foreach($group->stream as $node){
                    $this->loadNode($node, $groupNodesArray);
                }

                if($nodeGroupExists){

                    $groupsArray[$groupNodeId] = new StreamsGroup(
                        $groupIsActive,
                        $updateOptions,
                        $maxDelay,
                        $groupNodesArray,
                        count($groupNodesArray),
                        $groupNodeId
                    );

                }

            }

        }

        // Load nodes which are not in groups
        if(isset($simpleXml->stream)){
            foreach($simpleXml->stream as $node){
                try{
                    $this->loadNode($node, $nodeList);
                }
                catch(\Exception $ex){
                    throw new \Exception();
                }
            }
        }

        // If there are nodes which are not in groups, we create a new one.
        if(count($nodeList) > 0){

            // Check if there is a group ID
            $groupId;
            if(isset($simpleXml["id"])){
                $groupId = (string)$simpleXml["id"];
            }
            else{
                $groupId = Con::AUTO_LIST_ID; // Use default group id
            }

            // Check update options
            $updateOptions = null;
            try{
                $updateOptions = $this->getUpdateOptions($simpleXml);
            }
            catch(\Exception $ex){
                throw $ex;
            }

            // Check max delay option
            $maxDelay = null;
            try{
                $maxDelay = $this->getMaxDelay($simpleXml);
            }
            catch(\Exception $ex){
                throw $ex;
            }

            $groupsArray[$groupId] = new StreamsGroup(
                $isActive, // Active (Same as StreamsList)
                $updateOptions,
                $maxDelay,
                $nodeList,
                count($nodeList),
                $groupId // Default List ID
            );

        }

		$groupsLength = count($groupsArray);


        // Load options
        $unregisteredValues = [];
        if(isset($simpleXml->options)){
            foreach($simpleXml->options->children() as $key => $node){
                $tmp = (string)$node;
                if(strlen($tmp) > 0){
                    $unregisteredValues[$key] = $tmp;
                }
            }
        }

        // Return an instance of StreamsList
        return new StreamsList(
            $isActive,
            $filename,
            $outputFile,
            $runnersClassNames,
            $mixerClassName,
            $validatorClassName,
            $makerClassName,
            $urlsDelay,
            $groupsArray,
            $groupsLength,
            $unregisteredValues
        );

	}

	/**
	 * Create an instance of this class.
	 * @return StreamsList The new instance of this class
	 */
	static public function create()
    {
		return new StreamsListLoader();
	}

    /**
     * Get update options attribute of a SimpleXmlElement
     * @throw Exception on error
     * @param &SimpleXmlElement - The SimpleXmlElement
     * @returns UpdateOption[] - An array containing the update options
     */
    private function &getUpdateOptions(&$element)
    {
        $updateOptions = null;
        if(isset($element["update"])){
            $tmp = (string)$element["update"];
            $parser = new UpdateOptionsParser();
            try{
                $updateOptions = $parser->parse($tmp);
            }
            catch(\Exception $ex){
                $this->errorText = 'Error while parsing Update options "' . $parser->getErrorValue() .
                '". The format of the option is not correct.';
                throw new \Exception();
            }
            unset($parser);
        }
        if($updateOptions === null || count($updateOptions) == 0){
            $updateOptions = [new UpdateOptionEach()];
        };
        return $updateOptions;
    }

    /**
     * Get the maximum delay attribute of a SimpleXmlElement
     * @throw Exception on error
     * @param &SimpleXmlElement - The SimpleXmlElement
     * @returns integer - The maximum delay
     */
    private function getMaxDelay(&$element)
    {
        $maxDelay = null;
        if(isset($element["max_delay"])){
            $tmp = (string)$element["max_delay"];
            try{
                $maxDelay = MaximumDelayParser::parse($tmp);
            }
            catch(\Exception $ex){
                $this->errorText = 'Error while parsing maximum delay "' . $tmp .
                '". The format is not correct.';
                throw new \Exception();
            }
        }
        if($maxDelay == null){
            $maxDelay = 0;
        }
        return $maxDelay;
    }

    /**
	 * Load a node into an array of Stream.
     * @param &SimpleXMLElement  - A node
     * @param &Stream[][]        - An associative array intended to receive found nodes.
	 */
    private function loadNode(&$node, &$groupList)
    {
        $nodeIsValid = true;

        /* Check mandatory name and attributes */

        if($node->getName() != "stream"){
            $nodeIsValid = false;
        }

        $nodeId = null;
        if(!isset($node["id"])){
            $tmp = (string)$node["id"];
            if(in_array($tmp, $this->nodeIds)){

            }
            $nodeIsValid = false;
        }

        $nodeName;
        if(isset($node["name"])){
            $nodeName = (string)$node["name"];
        }
        else{
            $nodeName = "";
        }

        $parserClassname = null;
        if(isset($node["parser"])){
            $tmp = (string)$node["parser"];
            if(strpos($tmp, '\\') !== false){
                $this->errorText = Texts\errorPluginClassNameBackslashes("parser");
                throw new \Exception();
            }
            else{
                $parserClassname = $tmp;
            }
        }

        $nodeType = null;

        $nodeUrl = null;
        $nodePath = null;
        $ftpAddress = null;
        $ftpPort = null;
        $ftpLogin = null;
        $ftpPassword = null;
        $ftpFilepath = null;

        if(isset($node["url"])){
            $nodeUrl = (string)$node["url"];
            $nodeType = StreamTypes::URL;
        }
        else if(isset($node["path"])){
            $nodePath = (string)$node["path"];
            $nodeType = StreamTypes::PATH;
        }
        else if(isset($node["ftp_address"])){

            $ftpAddress = (string)$node["ftp_address"];

            $ftpPort = isset($node["ftp_port"]) && $node["ftp_port"] != "" ? (string)$node["ftp_port"] : null;
            $ftpLogin = isset($node["ftp_login"]) ? (string)$node["ftp_login"] : null;
            $ftpPassword = isset($node["ftp_password"]) ? (string)$node["ftp_password"] : null;
            $ftpFilepath = isset($node["ftp_filepath"]) ? (string)$node["ftp_filepath"] : false;

            if($ftpLogin === null || $ftpPassword === null){
                $this->errorText = Texts\errorXmlMissingFTPValues($ftpLogin, $ftpPassword);
                throw new \Exception();
            }
            else{
                $nodeType = StreamTypes::FTP;
            }

        }

        if(!isset($nodeType)){
            $nodeIsValid = false;
        }

        /* Check optional attributes */

        $nodeIsActive = null;
        if(isset($node["active"])){
            $tmp = Xml\attrIsTrue($node["active"]);
            if($tmp !== null){
                $nodeIsActive = $tmp;
            }
            else{
                $nodeIsActive = true;
            }
        }
        else{
            $nodeIsActive = true;
        }

        /* Create an instance of Stream */

        if($nodeIsValid){

            // Remove unwanted characters with preg_replace().
            // Note: The id is used as name for the nodes of the state XML, so th id
            // must not contain any special characters except the underscore character.
            $id = preg_replace("/[^A-Za-z0-9_]/", '', (string)$node["id"]);

            // Create the instance

            switch($nodeType){
                case StreamTypes::URL:{

                    $groupList[$id] = new StreamURL(
                        $nodeName,
                        $id,
                        $nodeUrl,
                        $nodeIsActive,
                        $parserClassname
                    );

                }break;
                case StreamTypes::PATH:{

                    $groupList[$id] = new StreamPath(
                        $nodeName,
                        $id,
                        $nodePath,
                        $nodeIsActive,
                        $parserClassname
                    );

                }break;
                case StreamTypes::FTP:{

                    $groupList[$id] = new StreamFTP(
                        $nodeName,
                        $id,
                        $ftpAddress,
                        $ftpPort,
                        $ftpLogin,
                        $ftpPassword,
                        $ftpFilepath,
                        $nodeIsActive,
                        $parserClassname
                    );

                }break;
            }

        }

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

}