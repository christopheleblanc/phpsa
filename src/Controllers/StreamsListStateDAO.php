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
 * StreamsListStateDAO
 * Class representing the state of the downloads defined in the streams list.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Models\StreamsListState;
use PHPStreamsAggregator\Models\StreamsGroupState;
use PHPStreamsAggregator\Models\StreamState;

/**
 * StreamsListStateDAO
 */
class StreamsListStateDAO{

    /** @var string - The temp directory **/
    private $tempPath;

    /** @var string - The file path **/
    private $filepath;

    /** @var integer - The number of errors occured while loading **/
    private $loadingErrors;

    /** @var boolean - Defines if the file exists or not. **/
    private $isFileExisting;

    /**
	 * Constructor
     * @param string  - The program temporary directory
	 * @param string  - The file name
	 */
	public function __construct(&$tempPath, &$fileName)
    {
        $this->tempPath = $tempPath;
        $this->filepath = $tempPath . DIRECTORY_SEPARATOR . Con::TEMP_STATE_DIR_NAME .
        DIRECTORY_SEPARATOR . $fileName;
        $this->loadingErrors = 0;
        $this->isFileExisting = false;
    }

	/**
	 * Constructor
	 * @param string $stateFilename - The name of the file storing states
	 */
	public function &load()
    {

        $fileLoaded;
        $simpleXml;

        if(file_exists($this->filepath)){
            $simpleXml = @simplexml_load_file($this->filepath);
            if($simpleXml === false){
                $fileLoaded = false;
            }
            else{
                $fileLoaded = true;
            }
            $this->isFileExisting = true;
        }
        else{
            $fileLoaded = false;
        }


        $count = ($fileLoaded && isset($simpleXml["count"])) ? intval($simpleXml["count"]) : 0;
        $state = ($fileLoaded && isset($simpleXml["state"])) ? intval($simpleXml["state"]) : 0;
        $lastUpdateTime = ($fileLoaded && isset($simpleXml["utime"])) ? intval($simpleXml["utime"]) : 0;
		$lastCompletionTime = ($fileLoaded && isset($simpleXml["cltime"])) ? intval($simpleXml["cltime"]) : 0;
        $lastParseTime = ($fileLoaded && isset($simpleXml["pltime"])) ? intval($simpleXml["pltime"]) : 0;
        $lastDownloadTime = ($fileLoaded && isset($simpleXml["dltime"])) ? intval($simpleXml["dltime"]) : 0;
        $lastValidatedTime = ($fileLoaded && isset($simpleXml["vtime"])) ? intval($simpleXml["vtime"]) : 0;
        $lastValidationMissing = ($fileLoaded && isset($simpleXml["vmiss"])) ? intval($simpleXml["vmiss"]) : 0;

        $currentOutdateTime = ($fileLoaded && isset($simpleXml["outime"])) ? intval($simpleXml["outime"]) : null;

        // Create the instance of StreamsListState
        // Please note that we must pass the children AFTER the instanciation
        // because each group and node must contain a reference to this instance!
        $streamsState = new StreamsListState(
            $this, // DAO
            !$fileLoaded,
            $count,
            $state,
            $lastUpdateTime,
            $lastCompletionTime,
            $lastDownloadTime,
            $lastParseTime,
            $lastValidatedTime,
            $lastValidationMissing,
            $currentOutdateTime
        );

        // Parse children
		$children = [];
        if($fileLoaded){
            if(isset($simpleXml->group)){

                foreach($simpleXml->group as $group){

                    $groupHasId = (isset($group["id"]));
                    $groupHasState = (isset($group["state"]));
                    $groupHasCount = (isset($group["count"]));
                    $groupHasLastCompletionTime = (isset($group["cltime"]));
                    $groupHasLastDownloadTime = (isset($group["dltime"]));

                    $nodesArray = [];

                    foreach($group->children() as $node){
                        $added = $this->addNodeTo($streamsState, $node, $nodesArray);
                        if(!$added){
                            $this->loadingErrors++;
                        }
                    }

                    if($groupHasId && $groupHasState && $groupHasCount && $groupHasLastCompletionTime &&
                    $groupHasLastDownloadTime){

                        $groupId = (string)$group["id"];
                        $children[$groupId] = new StreamsGroupState(
                            $streamsState,
                            $groupId,
                            $nodesArray,
                            count($nodesArray),
                            intval($group["count"]),
                            intval($group["state"]),
                            intval($group["cltime"]),
                            intval($group["dltime"])
                        );

                    }
                    else{
                        $this->loadingErrors++;
                    }

                }

            }
            unset($simpleXml);
        }

        // Pass children to the instance of StreamsListState
		$streamsState->setChildren($children);

        // Return a reference of the instance of StreamsListState
        return $streamsState;

	}

    /**
     * Add a node to a group
     * @param   &StreamsListState       - The instance of StreamsListState
     * @param   &SimpleXMLElement       - The SimpleXML node to load
     * @param   &StreamState[] - The array in which the new instance of
     *                                    StreamState must being added
     * @returns boolean                 - True if a new instance has been added,
     *                                  - or False.
     */
    private function addNodeTo(&$stateManager, &$node, &$array)
    {
        $name = (string)$node->getName();

        $hasState = false;
        $state = null;
        if(isset($node["state"])){
            $state = intval($node["state"]);
            $hasState = true;
        }

        $hasLastTime = false;
        $lastParseTime = null;
        if(isset($node["dltime"])){
            $lastParseTime = intval($node["dltime"]);
            $hasLastTime = true;
        }

        $totalDownloads = (isset($node["count"])) ? intval($node["count"]) : 0;

        if($hasState && $hasLastTime){

            $array[$name] = new StreamState(
                $stateManager,
                $name,
                $state,
                $lastParseTime,
                $totalDownloads
            );
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Get the total number of errors occured while loading the file.
     * @returns integer - The number of errors.
     */
    public function getTotalLoadingErrors()
    {
        return $this->loadingErrors;
    }

	/**
	 * Save the state file.
     * @param &StreamsListState - The instance of StreamsListState.
	 */
	public function save(&$streamsState)
    {

		$simpleXml = new \SimpleXMLElement("<states></states>");

		$simpleXml->addAttribute('state', $streamsState->getState());
		$simpleXml->addAttribute('count', $streamsState->getTotalCount());
        $simpleXml->addAttribute('utime', $streamsState->getLastUpdateTime());
        $simpleXml->addAttribute('dltime', $streamsState->getLastDownloadTime());
        $simpleXml->addAttribute('cltime', $streamsState->getLastCompletionTime());
        $simpleXml->addAttribute('vtime', $streamsState->getLastValidationTime());
        $simpleXml->addAttribute('vmiss', $streamsState->getLastValidationMissing());

        if($streamsState->getCurrentOutdateTime() !== null){
            $simpleXml->addAttribute('outime', $streamsState->getCurrentOutdateTime());
        }

		foreach($streamsState->getChildren() as $nodeKey => $node1){
            if($node1 instanceof StreamsGroupState){

                $xmlGroupNode = $simpleXml->addChild("group");
                $xmlGroupNode->addAttribute('id', $node1->getId());
                $xmlGroupNode->addAttribute('state', $node1->getState());
                $xmlGroupNode->addAttribute('count', $node1->getTotalDownloads());
                $xmlGroupNode->addAttribute('cltime', $node1->getLastCompletionTime());
                $xmlGroupNode->addAttribute('dltime', $node1->getLastDownloadTime());

                foreach($node1->getChildren() as $childKey => $childNode){
                    $xmlNode = $xmlGroupNode->addChild($childNode->getName());
                    $xmlNode->addAttribute('state', $childNode->getState());
                    $xmlNode->addAttribute('dltime', $childNode->getLastTime());
                    $xmlNode->addAttribute('count', $childNode->getTotalDownloads());
                }

            }
            else{
                $xmlNode = $simpleXml->addChild($node1->getName());
                $xmlNode->addAttribute('state', $node1->getState());
                $xmlNode->addAttribute('dltime', $node1->getLastTime());
                $xmlNode->addAttribute('count', $node1->getTotalDownloads());
            }

		}
        

        // Create Temp/State dir if it does not exists.
        $tempDirState = $this->tempPath . DIRECTORY_SEPARATOR . Con::TEMP_STATE_DIR_NAME;
        if(!file_exists($tempDirState) || !is_dir($tempDirState)){
            mkdir($tempDirState);
        }


		/* Save the XML document */
		$doc = new \DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$doc->loadXML($simpleXml->asXML());

		file_put_contents($this->filepath, $doc->saveXML(), LOCK_EX);

		// Apply chmod 777 to the file (Read, Write, Execute for all)
		chmod($this->filepath, 0777);

	}

    /**
	 * Delete the file
	 */
    public function deleteFile()
    {
        if(file_exists($this->filepath)){
            unlink($this->filepath);
        }
    }

    /**
	 * Defines if the file exists or not.
     * @returns boolean
	 */
    public function getIsFileExisting()
    {
        return $this->isFileExisting;
    }

	/**
	 * Create an instance of this class.
     * @param String - The temp path
     * @param String - The name of the file
	 * @return StreamsListStateDAO The new instance of this class.
	 */
	static public function create(&$tempPath, &$filepath)
    {
		return new StreamsListStateDAO($tempPath, $filepath);
	}

}