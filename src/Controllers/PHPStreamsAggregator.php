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
 * PHPStreamsAggregator
 * The main class of the program, responsible of the functioning of the whole
 * process:
 * - Check configuration, options, data
 * - Analyze stream list
 * - Load all plugins/classes defined in stream list
 * - Check if all streams and potential output file are up to date
 * - Do nothing if all is up to date, else
 * - Update outdated streams
 * - Optionally: (
 *     - Parse all streams
 *     - Process/Mix/Aggregate parsed elements/data
 *     - Export output file (if defined)
 *     - Validate output file (if defined)
 *   )
 * - Transmit events and errors through the program / plugins during the process
 * - Display details in "verbose" mode
 * - Display a single line message in classic mode
 *
 * THIS PROGRAM IS INTENDED TO RUN AS A STANDALONE PHP SCRIPT.
 *
 * This program is intended to run as a standalone PHP script executed from a
 * terminal or a command prompt. Please run the program using the script "run.php".
 *
 * Example:
 * php [program_path]/run.php [arguments]
 *
 * The list of streams to download and keep up to date must be defined in streams
 * list file. All options must been defined in configuration and/or streams list
 * file. Specific data processing functions and algorythms can be defined as
 * plugins. Streams list file is loaded from configuration file or from command
 * line arguments.
 *
 * Please read documentation for more informations.
 *
 * LAST UPDATES:
 * - 2020-11-12 : - Improvements on code documentation and readability
 *                - Creation of a demonstration context (streams + plugins)
 *
 * TODO
 * - Big improvements on code documentation and readability.
 * - Add the possibility to process multiple stream lists at same time?
 * - Add "sources" historic/logs to allow users/admins having more infos about
 *   updates?
 *
 */

namespace PHPStreamsAggregator;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\Controllers\ConfigLoader;
use PHPStreamsAggregator\Controllers\ContextFactory;
use PHPStreamsAggregator\Controllers\DataChecker;
use PHPStreamsAggregator\Controllers\StreamsListStateComparator;
use PHPStreamsAggregator\Controllers\LogManagerCompletion as CompletionLog;
use PHPStreamsAggregator\Controllers\LogManagerError as ErrorLog;
use PHPStreamsAggregator\Controllers\Options;
use PHPStreamsAggregator\Controllers\OutputFileFactory;
use PHPStreamsAggregator\Controllers\ProcessGetGroupsToUpdate;
use PHPStreamsAggregator\Controllers\ProcessInitRunners;
use PHPStreamsAggregator\Controllers\ProcessLoadProgramData;
use PHPStreamsAggregator\Controllers\ProcessLoadStreamsListData;
use PHPStreamsAggregator\Controllers\ProcessMaker;
use PHPStreamsAggregator\Controllers\ProcessMix;
use PHPStreamsAggregator\Controllers\ProcessParseAll;
use PHPStreamsAggregator\Controllers\ProcessUpdate;
use PHPStreamsAggregator\Controllers\ProcessValidator;
use PHPStreamsAggregator\Models\DateTimeNumeric;
use PHPStreamsAggregator\Models\StreamStates;
use PHPStreamsAggregator\Models\TestReport;

/**
 * PHPStreamsAggregator
 */
class PHPStreamsAggregator{

    /**
     * Define if the class has been instanciated (mostly used to secure several
     * static functions).
     * @var boolean
     */
    static private $instanciated = false;

    /**
     * The start time (Microtime used to calculate execution time).
     * @var Number
     */
    private $startTime;

    /** Options - Instance of Options containing command line options */
    private $options;

    /** @var ContextFactory The current context */
    private $contextFactory;

	/**
	 * Constructor
	 */
	public function __construct()
    {

        // Pre initialization
        //

        // Store microtime to calculate execution time.
        $this->startTime = microtime(true);

        // Create static "instances"
        Data::instanciate();
        CompletionLog::instanciate();
        ErrorLog::instanciate();

        // This class is now declared as instanciated
        self::$instanciated = true;

        // Initialize Options singleton (used to detect/store command line options)
        $this->options = Options::create();

        if($this->options->getIsVerbose()){
            echo trim(Con::APP_NAME) . " " . "v" . trim(Con::APP_VERSION) . " - Copyright (C) " .
            trim(Con::APP_COPYRIGHT_DATE) . " " . trim(Con::APP_AUTHOR_NAME) . PHP_EOL;
        }

        // Check program "modes" from command line options
        //

        // Display program informations?
        if($this->options->getIsModeInfos()){
            DisplayModes::displayInfos();
            $this->die();
        }

        // Display program help?
        if($this->options->getIsModeHelp()){
            DisplayModes::displayHelp();
            $this->die();
        }


        // Initialization
        //

        // Check program directories
        $dataChecker = new DataChecker();
        if(!$dataChecker->check()){
            echo "Error while trying to check program directories:" . PHP_EOL;
            foreach($dataChecker->getMissingDirectories() as $dir){
                $errStr = "Directory " . $dir . " does not exist!";
                ErrorLog::addLog($errStr, ErrorLog::WARNING);
                echo $errStr . PHP_EOL;
            }
            echo "Program stopped..." . PHP_EOL;
            $this->die();
        }

        // Initialize class variables
		$currentDate = new DateTimeNumeric();


        $testMode = $this->options->getIsModeTest();
        $testReport = null;
        if($testMode){
            $testReport = new TestReport();
        }


        // Load data
        //

        // Load program related data (config)
        $programData = new ProcessLoadProgramData();
        $programData->load();
        if(!$programData->getIsComplete()){
            $this->dldie($programData->getErrorText());
        }

        // Load streams list related data
        $listData = new ProcessLoadStreamsListData();
        $listData->load($this, $this->options, $programData->getConfig(), $programData->getTempDirectory(),
        $testMode, $testReport);


        $plugins = $listData->getPlugins();

        // Create a context factory
        $contextFactory = new ContextFactory();
        $contextFactory->setConfig($programData->getConfig());
        $contextFactory->setTempDirectory($programData->getTempDirectory());
        $contextFactory->setOptions($this->options);
        $contextFactory->setCurrentDate($currentDate);
        $contextFactory->setStreamsList($listData->getStreamsList());
        $contextFactory->setStreamsState($listData->getStreamsListState());
        $contextFactory->setPlugins($plugins);

        $this->contextFactory = &$contextFactory;


        // Test mode???
        if($testMode){
            $testReport->setUpdateNow($this->testUpdate($contextFactory));
            DisplayModes::displayTestResults($testReport);
            $this->die();
        }

        // Run "clear" mode?
        if($this->options->getIsClear() && $this->options->getDownloadListFile() === null){
            Data::clearAllTmp($contextFactory);
            $this->die();
        }

        // Clear streams list temporary files??
        if($this->options->getIsClear() && $this->options->getDownloadListFile() !== null){
            Data::clearAllDownloadListTemp($contextFactory);
            $this->die();
        }


        // Initialize runners
        $processInit = new ProcessInitRunners();
        $processInit->init($contextFactory->getContext(), $plugins);
        if(!$processInit->getIsComplete()){
            $this->dldie($processInit->getErrorText());
        }

        // If no "special" mode has been asked,
        // Run standard mode.
        $this->run($contextFactory);

	}

	/**
	 * Run - Main function of the program
     * @param &ContextFactory - The context
	 */
	private function run(&$contextFactory)
    {

        $streamsList = $contextFactory->getStreamsList();
        $streamsState = $contextFactory->getStreamsState();
        $verbose = $contextFactory->getOptions()->getIsVerbose();

        $totalDownloadedStreams = 0;
        $totalOutdatedStreams = 0;
        $totalFailed = 0;

        $updated = false;
        $saved = 0; // 0 = False, 1 = Started, 2 = Complete
        $validated = 0; // 0 = False, 1 = Started, 2 = Complete
        $mixed = 0; // 0 = False, 1 = Started, 2 = Complete
        $parsed = 0; // 0 = False, 1 = Started, 2 = Complete

        // If the program is "Active" and StreamsList contains at leat one group/stream.
		if($streamsList->getIsActive() && $streamsList->size() > 0){

            // Check if the state data does not correspond to the StreamsList or contains any error
            // and update it if a difference or error was found.
            $forceUpdateNow;
            $comparator = new StreamsListStateComparator($streamsList, $streamsState);
            if(!$comparator->equal()){

                if($verbose){
                    if($streamsState->getIsNew()){
                        echo "State file does not exists... " . PHP_EOL;
                        echo "Creating state data... ";
                    }
                    else{
                        echo "State file does not correspond to streams list... " . PHP_EOL;
                        echo "Modifying state data... ";
                    }
                    
                }

                Data::deleteDownloadListTempFiles($contextFactory);
                $streamsState->clear();
                $streamsState->updateStructure($streamsList);
                $forceUpdateNow = true;

                if($verbose){
                    echo "Done!" . PHP_EOL;
                }

            }
            else{
                $forceUpdateNow = false;
            }

            // Force update option??
            if($contextFactory->getOptions()->getForceUpdate()){
                $forceUpdateNow = true;
            }

            $lastUpdateTime = $streamsState->getLastUpdateTime();
            $lastDownloadTime = $streamsState->getLastDownloadTime();

            // Get groups to update
            //

            $groupsToUpdate = null;
            $processGet = new ProcessGetGroupsToUpdate();
            try{
                $processGet->execute($contextFactory, $forceUpdateNow);
                if($processGet->getIsComplete()){
                    $groupsToUpdate = $processGet->getGroupsToUpdate();
                }
                else{
                    $str = "Error while searching outdated streams";
                    if($processGet->getErrorText() !== null){
                        $str .= ": " . $processGet->getErrorText();
                    }
                    else{
                        $str .= ".";
                    }
                    $str .= " Program stopped...";
                    $this->dldie($str, ErrorLog::WARNING);
                }
            }
            catch(\Exception $ex){
                $str = "Error while search outdated streams";
                if(strlen($ex->getMessage()) > 0){
                    $str .= ": " . $ex->getMessage();
                }
                else{
                    $str .= ".";
                }
                $str .= " Program stopped...";
                $this->dldie($str, ErrorLog::WARNING);
            }

            $totalGroupsToUpdate = count($groupsToUpdate);
            unset($processGet);



            // Update streams/feeds
            //

            $updateDone = false;
            if($totalGroupsToUpdate > 0){

                $processUpdate = new ProcessUpdate();
                $return = $processUpdate->execute($this, $contextFactory, $groupsToUpdate, $totalGroupsToUpdate);
                if($return === true || $processUpdate->getIsComplete()){
                    $updateDone = true;
                    $updated = $processUpdate->getIsUpdated();
                    $totalDownloadedStreams = $processUpdate->getTotalDownloadedStreams();
                    $totalFailed = $processUpdate->getTotalFailed();
                    $totalOutdatedStreams = $processUpdate->getTotalOutdatedStreams();
                }
                else{
                    $errStr = "Error: ";
                    if($processUpdate->getErrorText() !== null){
                        $errStr .= $processUpdate->getErrorText();
                    }
                    else{
                        $errStr .= Texts\errorUnknow();
                    }
                    echo $errStr . PHP_EOL;

                }
                $streamsState->saveIfChanged();

            }

            // Dispatch event
            $eventArgs = [
                "total" => $totalGroupsToUpdate,
                "done" => $updateDone,
                "groups" => $groupsToUpdate,
                "total_outdated" => $totalOutdatedStreams,
                "total_loaded" => $totalDownloadedStreams,
                "total_failed" => $totalFailed
            ];
            $contextFactory->getPlugins()->dispatch($contextFactory->getContext(), "update", "onUpdate", $eventArgs);

            unset($groupsToUpdate);

            // Parse streams/feeds and save output file
            //

            $doMixAndSave = false;

            $outputFilepath = null;
            if($streamsList->getOutputFile() !== null){
                $outputFile = $streamsList->getOutputFile();
                $outputFilepath = $outputFile->getFilePath() . DIRECTORY_SEPARATOR . $outputFile->getFileName();
            }

            $makerLoaded = false;
            $makerClassName = $streamsList->getMakerClassName();
            if($makerClassName !== null && $contextFactory->getPlugins()->makerIsLoaded($makerClassName)){
                $makerLoaded = true;
            }

            // Check if the plugins has loaded at least one Parser from download list
            if($contextFactory->getPlugins()->hasParsers()){
                if($totalDownloadedStreams == 0){
                    if($makerLoaded && $outputFilepath !== null){
                        if($streamsState->getLastCompletionTime() < $lastDownloadTime){
                            $doMixAndSave = true;
                        }
                    }
                }
                else{
                    $doMixAndSave = true;
                }
                if($makerLoaded && $outputFilepath !== null){
                    if(!file_exists($outputFilepath)){
                        $doMixAndSave = true;
                    }
                }

            }

            $validatorClassName = $streamsList->getValidatorClassName();
            $validatorLoaded = false;
            if($validatorClassName !== null && $contextFactory->getPlugins()->validatorIsLoaded($validatorClassName)){
                $validatorLoaded = true;
            }

            if($makerLoaded && $outputFilepath !== null){
                // Check file validation if it has not been validated new
                $uptodate = ($totalGroupsToUpdate == 0);
                $outputFileFactory = new OutputFileFactory();
                $path = $contextFactory->getOutputFilePath();
                if(file_exists($path)){
                    $outputFileFactory->setFilePath($path);
                    $outputFileFactory->setFileExists(true);
                }
                $contextFactory->setOutputFile($outputFileFactory);
            }


            // Check if the program has updated something.
            // If something has been updated, we consider that we must update the output file.
            if($doMixAndSave){

                if($verbose){
                    echo 'Parse streams... ' . PHP_EOL;
                }

                $parsed = 1;

                $processParse = new ProcessParseAll();
                $catched = false;
                try{
                    $processParse->parseAll($contextFactory);
                    $catched = false;
                }
                catch(\Exception $ex){
                    $catched = $ex;
                }

                if(count($processParse->getEvents()) > 0){
                    $contextFactory->dispatchEvents($processParse->getEvents());
                }

                if(count($processParse->getAlerts()) > 0){
                    foreach($processParse->getAlerts() as $alertKey => $alert){
                        $str = "[ALERT] An alert was send during parsing process: " . $alertKey . ".";
                        echo $str . PHP_EOL;
                        ErrorLog::addLog($str);
                    }
                    $contextFactory->dispatchAlerts($processParse->getAlerts());
                }

                // Dispatch event
                $arguments = [
                    "done" => $processParse->getIsComplete(),
                    "total_parsed" => $processParse->getTotalParsed(),
                    "total_objects" => $processParse->getTotalObjects(),
                    "list" => $processParse->getParsedList()
                ];
                $contextFactory->getPlugins()->dispatch($contextFactory->getContext(), "parse_all", "onParseAll", $arguments);

                if($catched !== false){
                    $errStr = "An Exception was sent during parsing.";
                    if(strlen($catched) > 0){
                        $errStr .= ' Message : ' . $catched->getMessage();
                    }
                    else{
                        $errStr .= ' No message.';
                    }
                    $this->dldie($errStr);
                }
                else if(!$processParse->getIsComplete()){
                    $errStr = "Error while parsing. Process stopped.";
                    $this->dldie($errStr);
                }
                else if($processParse->getTotalObjects() == 0){
                    $errStr = "Error while parsing: Zero object was found.";
                    $this->dldie($errStr);
                }
                else{

                    $parsed = 2;

                    // Mix parsed objects
                    //

                    $processMix = new ProcessMix();

                    if($verbose){
                        echo 'Mix parsed objects... ';
                    }

                    $mixed = 1;

                    $mixedObjects;
                    $totalMixedObjects;
                    $mixedDone = false;
                    $catched = false;
                    try{
                        $processMix->mix(
                            $contextFactory,
                            $processParse->getParsedList(),
                        );
                        $catched = false;
                    }
                    catch(\Exception $ex){
                        $catched = $ex;
                    }

                    if(count($processMix->getEvents()) > 0){
                        $contextFactory->dispatchEvents($processMix->getEvents());
                    }
                    if($catched === false && $processMix->getIsComplete()){

                        $totalMixedObjects = count($processMix->getResults());
                        $mixedObjects = $processMix->getResults();

                        // Set state "PARSED"
                        $streamsState->setState(StreamStates::PARSED);
                        $mixed = 2;
                        $mixedDone = true;

                        if($verbose){
                            echo 'Done!' . PHP_EOL;
                        }

                    }
                    else{

                        $totalMixedObjects = 0;
                        $mixedObjects = [];

                        $errStr;
                        if($catched !== false){
                            $errStr = "An Exception as been thrown during the process.";
                            if(strlen($catched->getMessage()) > 0){
                                $errStr .= "Message: " . $catched->getMessage();
                            }
                            else{
                                $errStr .= "No message.";
                            }
                        }
                        else{
                            if($processMix->getErrorText() !== null){
                                $errStr = "Error: " . $processMix->getErrorText();
                            }
                            else{
                                $errStr = "The process returned an error without given any details.";
                            }

                        }
                        ErrorLog::addLog($errStr, ErrorLog::WARNING);
                        if($verbose){
                            echo 'Failed.' . PHP_EOL;
                            echo $errStr . "." . PHP_EOL;
                        }

                    }

                    if(count($processMix->getAlerts()) > 0){
                        foreach($processMix->getAlerts() as $alertKey => $alert){
                            $str = "[ALERT] An alert was send during mix process: " . $alertKey . ".";
                            echo $str . PHP_EOL;
                            ErrorLog::addLog($str);
                        }
                        $contextFactory->dispatchAlerts($processMix->getAlerts());
                    }

                    // Dispatch event
                    $arguments = [
                        "done" => $mixedDone,
                        "objects" => $mixedObjects,
                        "total_objects" => $totalMixedObjects
                    ];
                    $contextFactory->getPlugins()->dispatch($contextFactory->getContext(), "mix", "onMix", $arguments);

                    unset($processParse);

                    // Save output file??

                    if($processMix->getIsComplete()){

                        if($makerLoaded){

                            $saved = 1;

                            if($verbose){
                                echo 'Save parsed objects into output file... ';
                            }

                            $processMake = new ProcessMaker();
                            $catched = false;
                            $makeDone = false;
                            try{
                                $processMake->make($contextFactory, $streamsList->getOutputFile(), $mixedObjects);
                                $catched = false;
                            }
                            catch(\Exception $ex){
                                $catched = $ex;
                            }

                            if(count($processMake->getEvents()) > 0){
                                $contextFactory->dispatchEvents($processMake->getEvents());
                            }

                            if($catched === false && $processMake->getIsComplete()){
                                $streamsState->setLastCompletionTime($contextFactory->getCurrentDate()->getTimestamp());
                                $saved = 2;
                                $contextFactory->getOutputFile()->setIsUptodate(true);

                                // If there is no validator / No file validation required
                                // Increase the total count in state data
                                if(!$validatorLoaded){

                                    if($streamsState->getCurrentOutdateTime() !== null){
                                        $streamsState->setCurrentOutdateTime(null);

                                    }
                                    $streamsState->increaseTotalCount();
                                }
                                $makeDone = true;

                                if($verbose){
                                    echo 'Done!' . PHP_EOL;
                                }
                            }
                            else{
                                $errStr;
                                if($catched !== false){
                                    $errStr = 'An Exception has been thrown while saving output file. ';
                                    if(strlen($catched) > 0){
                                        $errStr .= 'Message: ' . $catched->getMessage();
                                    }
                                    else{
                                        $errStr .= 'No message.';
                                    }
                                }
                                else{
                                    $errStr = 'An error occured while saving output file. ';
                                    if($processMake->getErrorText() !== null){
                                        $errStr .= 'Error: ' . $processMake->getErrorText();
                                    }
                                    else{
                                        $errStr .= 'No details given.';
                                    }
                                }
                                if($verbose){
                                    echo 'Failed.' . PHP_EOL;
                                    echo $errStr . PHP_EOL;
                                }
                                ErrorLog::addLog($errStr, ErrorLog::WARNING);
                            }

                            if(count($processMake->getAlerts()) > 0){
                                foreach($processMake->getAlerts() as $alertKey => $alert){
                                    $str = "[ALERT] An alert was send during saving output file process: " . $alertKey . ".";
                                    echo $str . PHP_EOL;
                                    ErrorLog::addLog($str);
                                }
                                $contextFactory->dispatchAlerts($processMake->getAlerts());
                            }

                            $arguments = [
                                "done" => $makeDone,
                                "objects" => $mixedObjects,
                                "total_objects" => $totalMixedObjects,
                                "file" => $streamsList->getOutputFile()
                            ];
                            $contextFactory->getPlugins()->dispatch($contextFactory->getContext(), "make", "onMake", $arguments);

                            unset($processMake);

                        }

                    }

                    unset($processMix);

                }

            }

            $streamsState->saveIfChanged();
            $streamsState->setChanged(false);


            // Validate output file??
            //

            $doValidation = false;

            if($saved == 1){
                $doValidation = false;
            }
            else{
                if($validatorLoaded){
                    if($saved == 2){
                        $doValidation = true;
                    }

                    if($totalDownloadedStreams == 0){
                        if($streamsState->getLastValidationTime() < $streamsState->getLastCompletionTime()){
                            $doValidation = true;
                        }
                    }
                }
            }


            if($doValidation){

                $validated = 1;

                if($verbose){
                    echo 'Validate output file... ';
                }

                $errStr = null;
                $processValidate = new ProcessValidator();
                $catched = false;
                try{
                    $processValidate->validate($contextFactory, $validatorClassName, $outputFilepath);
                    $catched = false;
                }
                catch(\Exception $ex){
                    $catched = $ex;
                }

                if(count($processValidate->getEvents()) > 0){
                    $contextFactory->dispatchEvents($processValidate->getEvents());
                }

                if($catched !== false){
                    $errStr = 'An Exception has been thrown during file validation. ';
                    if(strlen($catched->getMessage()) > 0){
                        $errStr .= 'Message: "' . $ex->getMessage() . '"';
                    }
                    else{
                        $errStr .= ' No message.';
                    }
                }
                elseif(!$processValidate->getIsComplete()){
                    $errStr = 'An error occured during file validation. ';
                    if($processValidate->getErrorText() !== null){
                        $errStr .= 'Error: ' . $processValidate->getErrorText();
                    }
                    else{
                        $errStr .= 'No details given.';
                    }
                }
                else{

                    if($processValidate->getIsValidated()){
                        $validated = 2;
                        $streamsState->setState(StreamStates::VALIDATED);
                        $streamsState->setLastValidationTime($contextFactory->getCurrentDate()->getTimestamp());
                        $contextFactory->getOutputFile()->setIsValidated(true);


                        if($streamsState->getCurrentOutdateTime() !== null){
                            $streamsState->setCurrentOutdateTime(null);
                        }

                        // If validator is loaded / file require validation, we consider
                        // the validation as end of the process.
                        // Increase the total count
                        $streamsState->increaseTotalCount();
                    }
                    else{
                        $errStr = 'An error occured during file validation. ';
                        if($processValidate->getErrorText() !== null){
                            $errStr .= 'Error: ' . $processValidate->getErrorText();
                        }
                        else{
                            $errStr .= 'No details given.';
                        }
                    }

                }

                if($verbose){
                    if($processValidate->getIsValidated()){
                        echo 'Done!' . PHP_EOL;
                    }
                    else{
                        echo 'Failed...' . PHP_EOL;
                        echo $errStr . "." . PHP_EOL;
                    }
                }
                if(!$processValidate->getIsValidated()){
                    ErrorLog::addLog($errStr);
                }

                if(count($processValidate->getAlerts()) > 0){
                    foreach($processValidate->getAlerts() as $alertKey => $alert){
                        $str = "[ALERT] An alert was send during saving output file process: " . $alertKey . ".";
                        echo $str . PHP_EOL;
                        ErrorLog::addLog($str);
                    }
                    $contextFactory->dispatchAlerts($processValidate->getAlerts());
                }

                $arguments = [
                    "done" => $processValidate->getIsValidated()
                ];
                if(!$processValidate->getIsValidated()){
                    $arguments["error_message"] = $errStr;
                }
                $contextFactory->getPlugins()->dispatch($contextFactory->getContext(), "validation", "onValidation", $arguments);
                unset($processValidate);

            }

            // Last check
            //

            if($makerLoaded && $outputFilepath !== null){

                $outputFileFactory = $contextFactory->getOutputFile();
                $lastDownloadTime = $streamsState->getLastDownloadTime();
                $lastCompletionTime = $streamsState->getLastCompletionTime();
                $lastValidationTime = $streamsState->getLastValidationTime();

                if($totalGroupsToUpdate == 0 || $saved < 2){
                    if($lastCompletionTime >= $lastDownloadTime){
                        $outputFileFactory->setIsUptodate(true);
                        if($validatorLoaded && $validated < 2){
                            if($lastValidationTime >= $lastCompletionTime){
                                $outputFileFactory->setIsValidated(true);
                            }
                        }
                    }
                }

            }

            // Add state "Outdate"?
            if($makerLoaded || $validatorLoaded){
                if($validatorLoaded){
                    if(!$contextFactory->getOutputFile()->getIsValidated()){
                        if($streamsState->getCurrentOutdateTime() === null){
                            $streamsState->setCurrentOutdateTime($contextFactory->getCurrentDate()->getTimestamp());
                        }
                    }
                }
                else{
                    if(!$contextFactory->getOutputFile()->getIsUptodate()){
                        if($streamsState->getCurrentOutdateTime() === null){
                            $streamsState->setCurrentOutdateTime($contextFactory->getCurrentDate()->getTimestamp());
                        }
                    }
                }

            }

            $streamsState->saveIfChanged();


            // End of the process - Display/Add to log a validation or error text.
            //

            $totalStr;

            if($totalGroupsToUpdate > 0){
                $filesStr = ($totalDownloadedStreams > 1) ? "streams" : "stream";
                $totalStr = $totalDownloadedStreams . " " . $filesStr . " updated.";
            }
            else{
                $totalStr = "The streams are already up to date.";
            }

            if($doMixAndSave){

                $str = null;
                if($validated == 2){
                    $str = $totalStr . " " . "Output file now validated...";
                    CompletionLog::addLog($str);
                }
                else{
                    if($validated == 1){
                        $str = $totalStr . " " . "Output file not validated.";
                        ErrorLog::addLog($str);
                    }
                    else{
                        if($saved == 2){
                            $str = $totalStr . " " . "Output now saved...";
                            CompletionLog::addLog($str);
                        }
                        else if($saved == 1){
                            $str = $totalStr . " " . "Output file not saved.";
                            ErrorLog::addLog($str);
                        }
                        else{
                            $str = $totalStr . " " . "An error occured while parsing or mixing streams...";
                            ErrorLog::addLog($str);
                        }
                    }
                }
                echo $str . PHP_EOL;

            }
            else{

                $vvalidated = ($outputFilepath !== null && file_exists($outputFilepath) &&
                $contextFactory->getOutputFile()->getIsValidated());

                $str = "Nothing to do... ";

                if($validatorLoaded){
                    if($vvalidated){
                        $str .= $totalStr . " " . "Output file is valid...";
                    }
                    else{
                        $str .= $totalStr . " " . "Output file is not valid.";
                    }
                }
                else{
                    $str .= $totalStr;
                }
                echo $str . PHP_EOL;

                if($verbose){
                    if($validatorLoaded && $vvalidated){
                        echo "Last validation time: " .
                        date("Y-m-d \a\\t\ H:i:s", $streamsState->getLastValidationTime()) . PHP_EOL;
                    }
                }

            }

            // Dispatch event
            $eventArgs = [
                "updated" => $updated,
                "mixed" => ($mixed == 2),
                "made" => ($saved == 2),
                "validated" => ($validated == 2)
            ];
            $contextFactory->getPlugins()->dispatch($contextFactory->getContext(), "finish", "onFinish", $eventArgs);

		}

        // Stop the program
        $this->die();

	}

    /**
     * Test update (for mode "test")
     * &ContextFactory - The context.
     */
    private function testUpdate(&$contextFactory)
    {
        $streamsList = $contextFactory->getStreamsList();
        $streamsState = $contextFactory->getStreamsState();

        $comparator = new StreamsListStateComparator($streamsList, $streamsState);
        if(!$comparator->equal()){
            return true;
        }
        else{
            $groupsToUpdate = null;
            $process = new ProcessGetGroupsToUpdate();
            try{
                $process->execute($contextFactory, false);
                if($process->getIsComplete()){
                    $groupsToUpdate = $process->getGroupsToUpdate();
                }
                else{
                    if($process->getErrorText() !== null){
                        ErrorLog::addLog($process->getErrorText(), ErrorLog::WARNING);
                        echo $process->getErrorText() . "." . PHP_EOL;
                    }
                    return false;
                }
            }
            catch(\Exception $ex){
                ErrorLog::addLog($ex->getMessage(), ErrorLog::WARNING);
                echo $ex->getMessage() . "." . PHP_EOL;
                return false;
            }

            if(count($groupsToUpdate) > 0){
                return true;
            }
        }
        return false;
    }

    /**
     * Display, Log and Die.
     * Display a message, add a log and stop the execution of the program by 
     * calling the member method "die()".
     */
    public function dldie($str, $logCode = null)
    {
        ErrorLog::addLog($str, $logCode);
        echo $str . PHP_EOL;
        $this->die();
    }

    /**
     * Stop the program. Display a message in verbose mode.
     */
    public function die()
    {
        if($this->options->getIsVerbose()){
            $executionTime = microtime(true) - $this->startTime;
            echo "Process finished in " . round($executionTime, 2) . " seconds. ";
            echo "Memory usage: " . Texts\convertMemoryUsage(memory_get_usage(true)) . PHP_EOL;
        }
        die();
    }

    /**
     * Get the program temporary directory.
     * @returns  string
     */
    public function getTempPath()
    {
        return $this->contextFactory->getTempDirectory();
    }

    /**
     * Create an instance of PHPStreamsAggregator
     * @returns PHPStreamsAggregator
     */
    static public function create()
    {
        return new PHPStreamsAggregator();
    }


    // Static functions, intended for use by plugins and external programs.
    // NOTE: The current class PHPStreamsAggregator must have been instanciated, or some of those
    // functions will return NULL (with help of the security function "staticReturn()").
    //

    /**
     * Prevent a PHP error when calling static functions without having instanciated this class.
     * @param    mixed      - A value to return.
     * @returns  mixed|null - The value passed as argument if this class has been instanciated, or NULL.
     */
    static private function staticReturn($value)
    {
        return (self::$instanciated) ? $value : null;
    }

    /**
     * Get Absolute path of the program
     * @returns String - The absolute path
     */
    static public function absolutePath()
    {
        return self::staticReturn(Data::$ABSOLUTE_PATH);
    }

    /**
     * Get Absolute path of the program
     * @returns String - The absolute path
     */
    static public function programPath()
    {
        return self::staticReturn(Data::$ABSOLUTE_PATH);
    }

    /**
     * Get Absolute path of the program temp files (downloaded files and state files)
     * @returns String - The absolute path
     */
    static public function tempPath()
    {
        return self::staticReturn(Data::$TEMP_ABSOLUTE_PATH);
    }

    /**
     * Get Absolute path of the program temp files (downloaded files and state files)
     * @returns String - The absolute path
     */
    static public function logsPath()
    {
        return self::staticReturn(Data::$LOGS_ABSOLUTE_PATH);
    }

    /**
     * Get Absolute path of the program plugins
     * @returns String - The absolute path
     */
    static public function pluginsPath()
    {
        return self::staticReturn(Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR .
        Con::PLUGINS_DIR_NAME);
    }

    /**
     * Get Absolute path of the plugins of type "Parser"
     * @returns String - The absolute path
     */
    static public function parsersPath()
    {
        return self::staticReturn(Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR .
        Con:: PLUGINS_DIR_NAME . DIRECTORY_SEPARATOR . Con::PARSERS_DIR_NAME);
    }

    /**
     * Get Absolute path of the plugins of type "Mixer"
     * @returns String - The absolute path
     */
    static public function mixersPath()
    {
        return self::staticReturn(Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR .
        Con:: PLUGINS_DIR_NAME . DIRECTORY_SEPARATOR . Con::MIXERS_DIR_NAME);
    }

    /**
     * Get Absolute path of the plugins of type "Maker"
     * @returns String - The absolute path
     */
    static public function makersPath()
    {
        return self::staticReturn(Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR .
        Con:: PLUGINS_DIR_NAME . DIRECTORY_SEPARATOR . Con::MAKERS_DIR_NAME);
    }

    /**
     * Get Absolute path of the plugins of type "Validator"
     * @returns String - The absolute path
     */
    static public function validatorsPath()
    {
        return self::staticReturn(Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR .
        Con:: PLUGINS_DIR_NAME . DIRECTORY_SEPARATOR . Con::VALIDATORS_DIR_NAME);
    }

}