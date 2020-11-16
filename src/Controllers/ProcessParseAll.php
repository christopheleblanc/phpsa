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
 * ProcessParseAll
 * Top level class used by the program to parse data from all streams using
 * plugins classes of type "Parser".
 *
 * A plugin of type "Parser" is intended to process parse data according
 * to specific algorythm, defined in class method "parse()".
 *
 * Data flow and plugins representation:
 *
 * Parse data from all streams  > Process/Mix/Aggregate all data > Make
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * [PARSE]        |
 * [PARSE]        |------------ > [MIX] ------------------------ > [MAKE]
 * [PARSE]        |
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 *
 * This class is responsible for the execution of the following process, while
 * reporting any errors/Exception:
 * - Parse all existing data
 * - Create an instance of ParsedList, which contains parsed elements.
 *
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\ParserTypes;
use PHPStreamsAggregator\Texts;
use PHPStreamsAggregator\Controllers\AstractProcessEvents;
use PHPStreamsAggregator\Controllers\LogManagerError as ErrorLog;
use PHPStreamsAggregator\Controllers\ProcessParseStream;
use PHPStreamsAggregator\Controllers\Update;
use PHPStreamsAggregator\Models\DateTimeNumeric;
use PHPStreamsAggregator\Models\ParsedGroup;
use PHPStreamsAggregator\Models\ParsedList;
use PHPStreamsAggregator\Models\ParsedStream;
use PHPStreamsAggregator\Models\StreamStates;

/**
 * ProcessParseAll
 */
class ProcessParseAll extends AstractProcessEvents{

    /** @var integer - The total number of parsed entities (on all streams). */
    private $totalObjects;

    /** @var integer - The total number of parsed streams. */
    private $totalParsed;

    /** @var ParsedList - An instance of ParsedList created after calling parseAll(). */
    private $parsedList;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->totalObjects = 0;
        $this->totalParsed = 0;
        $this->parsedList = null;
        parent::__construct();
    }

    /**
	 * Parse streams.
     * Will scan the StreamsList to process each stream one by one,
     * parse them using the corresponding "Parser", and produce an
     * instance of ParsedList as result.
     * @param &ContextFactory - The context
	 */
	public function parseAll(&$contextFactory)
    {

        $verbose = $contextFactory->getOptions()->getIsVerbose();

        $streamsList = $contextFactory->getStreamsList();
        $streamsState = $contextFactory->getStreamsState();
        $streamsState->setState(StreamStates::START);

        // Parse all objects
        //

        // Array containg groups of Streams who were not parsed (file not found, parsing error...)
        $notParsedGroups = [];

        $parsedObjects = [];

        $this->totalObjects = 0;
        $this->totalParsed = 0;

        $groupsParseCompleteArray = [];
        $totalOutdatedObjects = 0;

        $this->events = [];
        $this->alerts = [];

        foreach($streamsList->getChildren() as $groupKey => $downloadGroup){

            if($downloadGroup->isActive()){

                if($verbose){
                    echo 'Parse group "' . $groupKey . '"' . PHP_EOL;
                }

                $stateGroup = $streamsState->getChild($groupKey);

                $groupParsedObjects = [];
                $notParsedNodes = [];
                $nodesParseCompleteArray = [];
                $totalFilesParses = 0;

                foreach($downloadGroup->getChildren() as $downloadNodeKey => $downloadNode){

                    if($downloadNode->isActive()){

                        $parserClassname = $downloadNode->getParserName();
                        $parserLoaded = false;
                        if($parserClassname !== null && $contextFactory->getPlugins()->parserIsLoaded($parserClassname)){
                            $parserLoaded = true;
                        }

                        if($parserLoaded){

                            if($verbose){
                                echo '-Parse stream "' . $downloadNodeKey . '" ';
                            }

                            $stateNode = $stateGroup->getChild($downloadNodeKey);
                            $downloadLastTime = new DateTimeNumeric($stateNode->getLastTime());

                            $isUptodate = false;
                            $isLate = false;
                            $isOutdated = false;

                            // Check if node/stream is up to date.
                            // If it is not, we must check if the last update date is not exceeding
                            // the maximum authorized delay (defined in Downloads configuration file).
                            $targetArray;
                            $nodeIsUpToDate = Update::isNodeUpToDate($downloadGroup, $contextFactory->getCurrentDate(), $downloadLastTime, $stateNode->getState());
                            if($nodeIsUpToDate){

                                $isUptodate = true;

                                if($verbose){
                                    echo '(up to date)... ';
                                }

                            }
                            else{

                                // Check if the last update date is not exceeding the maximum authorized
                                // delay (defined in Downloads configuration file).
                                // If the last update is too old, do not parse the file
                                if($downloadLastTime->getTimestamp() + $downloadGroup->getMaxDelay() > $contextFactory->getCurrentDate()->getTimestamp()){

                                    $isLate = true;

                                    if($verbose){
                                        echo '(late)... ';
                                    }

                                }
                                else{

                                    $isOutdated = true;
                                    $totalOutdatedObjects++;

                                    if($verbose){
                                        echo '(outdated)... ';
                                    }

                                }

                            }

                            $objectsArray = [];
                            $nodeParseComplete = false;

                            // Create a process
                            $processParse = new ProcessParseStream();
                            $catched = false;
                            try{
                                $processParse->parse($contextFactory, $parserClassname, $downloadNode, $objectsArray, $notParsedNodes);
                                $catched = false;
                            }
                            catch(\Exception $ex){
                                $catched = $ex;
                            }

                            $nodeParseComplete = $processParse->getIsComplete();

                            $this->events = array_merge($this->events, $processParse->getEvents());
                            $this->alerts = array_merge($this->alerts, $processParse->getAlerts());

                            if($nodeParseComplete){

                                $mixStream = new ParsedStream(
                                    $downloadNode,
                                    $stateNode,
                                    $downloadNodeKey,
                                    $objectsArray,
                                    $isUptodate,
                                    $isLate,
                                    $isOutdated
                                );
                                $groupParsedObjects[$downloadNodeKey] = $mixStream;

                                $this->totalParsed++;
                                if($verbose){
                                    echo 'Done!' . PHP_EOL;
                                }

                            }
                            else{

                                // Display an error but do not stop the program
                                $errStr;
                                if($catched !== false){
                                    $errStr = 'Error: An Exception has been thrown during parsing process for stream "' .
                                    $downloadNodeKey . '".';
                                    if(strlen($catched->getMessage()) > 0){
                                        $errStr .= " Message: " . $catched->getMessage();
                                    }
                                    else{
                                        $errStr .= " No message.";
                                    }
                                }
                                else{
                                    if($processParse->getErrorText() !== null){
                                        $errStr = 'Parsing process for stream "' . $downloadNodeKey .
                                        '" has returned an error. Error: ' . $processParse->getErrorText();
                                    }
                                    else{
                                        $errStr = 'Parsing process for stream "' . $downloadNodeKey .
                                        '" has returned an error without giving details...';
                                    }
                                }

                                if($verbose){
                                    echo 'Failed.' . PHP_EOL;
                                    echo $errStr . PHP_EOL;
                                }

                                ErrorLog::addLog($errStr);


                            }

                            unset($processParse);


                            // Dispatch event
                            $arguments = [
                                "done" => $nodeParseComplete,
                                "id" => $downloadNode->getId()
                            ];
                            $contextFactory->getPlugins()->dispatch($contextFactory->getContext(), "parse", "onParse", $arguments);


                            $nodesParseCompleteArray[] = $nodeParseComplete;
                            $totalFilesParses++;

                        }

                    }

                }

                $notParsedGroups[$groupKey] = $notParsedNodes;
                $nodesParseCompletionSum = array_sum($nodesParseCompleteArray);
                $nodesMultiParseComplete = ($nodesParseCompletionSum == $totalFilesParses);
                $groupsParseCompleteArray[] = $nodesMultiParseComplete;

                if(count($groupParsedObjects) > 0){
                    $parsedObjects[$groupKey] = new ParsedGroup(
                        $downloadGroup,
                        $stateGroup,
                        $groupKey,
                        $groupParsedObjects
                    );
                }

            }

        }

        $this->parsedList = new ParsedList($parsedObjects);
        $this->totalObjects = count($parsedObjects);

        $this->setIsComplete();
        return true;

    }

    /**
     * Get the total number of parsed streams.
     * @returns integer
     */
    public function getTotalParsed()
    {
        return $this->totalParsed;
    }

    /**
     * Get the total number of objects found.
     * @returns integer
     */
    public function getTotalObjects()
    {
        return $this->totalObjects;
    }

    /**
     * Get the instance of ParsedList containing all parsed entities.
     * The instance is created on calling function parseAll().
     * @returns &ParsedList|null
     */
    public function &getParsedList()
    {
        return $this->parsedList;
    }

}