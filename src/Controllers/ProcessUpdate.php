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
 * ProcessUpdate
 * Top level class used by the program to update/download streams.
 *
 * This class is responsible for the execution of the following process, while
 * reporting any errors/Exception:
 * - Find all outdated streams
 * - Download/update all outdated streams
 * - Update state data
 *
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\Texts;
use PHPStreamsAggregator\Controllers\AstractProcess;
use PHPStreamsAggregator\Controllers\FileDownloadFTP;
use PHPStreamsAggregator\Controllers\FileDownloadPath;
use PHPStreamsAggregator\Controllers\FileDownloadURL;
use PHPStreamsAggregator\Controllers\LogManagerError as ErrorLog;
use PHPStreamsAggregator\Models\DateTimeNumeric;
use PHPStreamsAggregator\Models\StreamStates;
use PHPStreamsAggregator\Models\StreamTypes;
use PHPStreamsAggregator\Models\UpdateOptionTypes;
use PHPStreamsAggregator\Models\UpdatingGroup;

/**
 * ProcessUpdate
 */
class ProcessUpdate extends AstractProcess{

    /** @var boolean - Defines if at least one stream has been updated */
    private $updated;

    /** @var integer - The total number of downloaded streams */
    private $totalDownloadedStreams;

    /** @var integer - The total number of failed downloads */
    private $totalFailed;

    /** @var integer - The total number of outdated streams */
    private $totalOutdatedStreams;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->updated = false;
        $this->totalDownloadedStreams = 0;
        $this->totalFailed = 0;
        $this->totalOutdatedStreams = 0;
    }

	/**
	 * Update streams.
     * This function take an array containing a group of streams as parameter, and do the same
     * process for each group:
     * - Check if node is outdated (relating to the group update options)
     * - Download/update outdated nodes.
     * - Modify streams state
     * @param &PHPStreamsAggregator  - The instance of program
     * @param &ContextFactory        - The context factory
     * @param &StreamsGroup[]        - Groups to update
     * @param integer                - The total number of groups to update
	 */
	public function execute(&$program, &$contextFactory, &$groupsToUpdate, $totalGroupsToUpdate)
    {

        $this->updated = false;
        $this->totalDownloadedStreams = 0;
        $this->totalFailed = 0;
        $this->totalOutdatedStreams = 0;

        $verbose = $contextFactory->getOptions()->getIsVerbose();
        $forceUpdate = $contextFactory->getOptions()->getForceUpdate();
        $streamsState = $contextFactory->getStreamsState();


		// Initialize
		//

        $groupsUpdating = []; // Array containing instances of UpdatingGroup

        foreach($groupsToUpdate as $groupKey => $groupNode){

            $stateGroup = $streamsState->getChild($groupKey);
            if($stateGroup !== null){

                // Reset State of each group
                $stateGroup->setState(StreamStates::START);

                // Create instance of "UpdatingGroup" for each group.
                $groupsUpdating[$groupKey] = new UpdatingGroup($groupKey, $stateGroup);

            }
            else{

                // Normally, the state should never be null. But, in the unlikely event that this
                // happens, we stop the program and return an error.
                $errStr = 'Error while loading state group "' . $groupKey . '". Program stopped.';
                ErrorLog::addLog($errStr, ErrorLog::WARNING);
                echo $errStr . "." . PHP_EOL;
                $program->die();

            }

        }


		// Create the Stream objects
		//

        foreach($groupsToUpdate as $groupKey => $groupNode){

            $updatingGroup = $groupsUpdating[$groupKey];
            $updateOptions = $groupNode->getUpdateOptions();

            foreach($groupNode->getChildren() as $downloadNodeKey => $downloadNode){

                if($downloadNode->isActive()){

                    $stateNode = $updatingGroup->getState()->getChild($downloadNodeKey);
                    $downloadLastTime = new DateTimeNumeric($stateNode->getLastTime());

                    $nodeNeedUpdate = false;

                    if(count($updateOptions) > 0){

                        $nodeNeedUpdateI = 0;

                        foreach($updateOptions as $updateOption){
                            switch($updateOption->getType()){
                                case UpdateOptionTypes::EACH:{
                                    $nodeNeedUpdateI++;
                                }break;
                                case UpdateOptionTypes::EVERY:{
                                    if(Update::isUpdateRequiredEvery(
                                        $contextFactory->getCurrentDate(),
                                        $downloadLastTime,
                                        $stateNode->getState(),
                                        $updateOption->getNumber(),
                                        $updateOption->getTimeType()
                                    )){
                                        $nodeNeedUpdateI++;
                                    }
                                }break;
                                case UpdateOptionTypes::HOUR:{
                                    if(Update::isUpdateRequiredHour(
                                        $contextFactory->getCurrentDate(),
                                        $downloadLastTime,
                                        $stateNode->getState(),
                                        $updateOption->getHours(),
                                        $updateOption->getMinutes(),
                                        $updateOption->getSeconds()
                                    )){
                                        $nodeNeedUpdateI++;
                                    }
                                }break;
                            }
                        }

                        if($nodeNeedUpdateI > 0){
                            $nodeNeedUpdate = true;
                            $this->totalOutdatedStreams++;
                        }
                    }

                    $tempFileName = $contextFactory->getTempDirectory() . DIRECTORY_SEPARATOR . Con::TEMP_FILES_DIR_NAME .
                    DIRECTORY_SEPARATOR . $contextFactory->getStreamsList()->getFileName() . DIRECTORY_SEPARATOR . $downloadNode->getId();

                    $tempResFileName = $contextFactory->getTempDirectory() . DIRECTORY_SEPARATOR . Con::TEMP_FILES_DIR_NAME .
                    DIRECTORY_SEPARATOR . $contextFactory->getStreamsList()->getFileName() . DIRECTORY_SEPARATOR . Con::TEMP_FILES_RES_DIR_NAME .
                    DIRECTORY_SEPARATOR . $downloadNode->getId();

                    // Reset the state of the download if the state is blocked at "1" (maybe an error)
                    if(!$nodeNeedUpdate){
                        if(!file_exists($tempFileName)){
                            $nodeNeedUpdate = true;
                        }
                    }

                    if($nodeNeedUpdate || $forceUpdate){

                        $downloadInstance = null;

                        switch($downloadNode->getDownloadType()){
                            case StreamTypes::URL:{

                                $downloadInstance = new FileDownloadURL(
                                    $downloadNode->getUrl(),
                                    $tempResFileName,
                                    $downloadNode->getId()
                                );

                            }break;
                            case StreamTypes::PATH:{

                                $downloadInstance = new FileDownloadPath(
                                    $downloadNode->getPath(),
                                    $tempResFileName,
                                    $downloadNode->getId()
                                );

                            }break;
                            case StreamTypes::FTP:{

                                $downloadInstance = new FileDownloadFTP(
                                    $downloadNode->getFtpAddress(),
                                    $downloadNode->getFtpPort(),
                                    $downloadNode->getFtpLogin(),
                                    $downloadNode->getFtpPassword(),
                                    $downloadNode->getFtpFilepath(),
                                    $tempResFileName,
                                    $downloadNode->getId()
                                );

                            }break;
                        }

                        if(isset($downloadInstance)){
                            $updatingGroup->addFileToDownload($downloadNode->getId(), $downloadInstance);
                        }

                    }

                }

            }

        }


		// Start downloads
		//
        $totalGroupsFullDownload = 0;
        foreach($groupsToUpdate as $groupKey => $groupNode){

            $updatingGroup = $groupsUpdating[$groupKey];

            $totalDownloads = count($updatingGroup->getFilesToDownload());

            if($totalDownloads > 0){

                if($verbose){
                    echo 'Update group "' . $groupKey . '"' . PHP_EOL;
                }

                $this->download($contextFactory, $updatingGroup, $verbose);
                if($updatingGroup->getTotalDownloaded() > 0){
                    $this->totalDownloadedStreams += $updatingGroup->getTotalDownloaded();
                }

                // CHeck if download list is complete (all downloads complete)
                if($updatingGroup->getTotalDownloaded() > 0 &&
                $totalDownloads == $updatingGroup->getTotalDownloaded()){
                    $totalGroupsFullDownload++;
                    $stateGroup = $updatingGroup->getState();
                    $stateGroup->setState(StreamStates::DOWNLOADED);
                    $stateGroup->setLastDownloadTime($contextFactory->getCurrentDate()->getTimestamp());
                    $stateGroup->increaseTotalDownloads();
                }

            }

        }

        // Release memory
        //

        if($this->totalDownloadedStreams > 0){

            $this->updated = true;

            $streamsState->setLastUpdateTime($contextFactory->getCurrentDate()->getTimestamp());

            // Delete temp responses directory
            $tempDirFilesFileRes = $contextFactory->getTempDirectory() . DIRECTORY_SEPARATOR . Con::TEMP_FILES_DIR_NAME .
            DIRECTORY_SEPARATOR . $contextFactory->getStreamsList()->getFileName() . DIRECTORY_SEPARATOR .
            Con::TEMP_FILES_RES_DIR_NAME;
            Data::recursiveRemoveDirectory($tempDirFilesFileRes, true, false);

            if($totalGroupsFullDownload == $totalGroupsToUpdate){
                $streamsState->setLastDownloadTime($contextFactory->getCurrentDate()->getTimestamp());
                $streamsState->setState(StreamStates::DOWNLOADED);
            }

        }

        unset($groupsUpdating);

        return true;

	}

	/**
	 * Download a stream
     * @param &ContextFactory   - The context factory.
	 * @param &UpdatingGroup    - An instance of UpdatingGroup
     * @param boolean           - Defines if the program ren in "verbose" mode
	 */
	private function download(&$contextFactory, &$updatingGroup, $verbose)
    {

        $streamsList = $contextFactory->getStreamsList();
        $streamsGroup = $streamsList->getChild($updatingGroup->getId());

        // Create files directory if it not exists.
        $tempDirFiles = $contextFactory->getTempDirectory() . DIRECTORY_SEPARATOR . Con::TEMP_FILES_DIR_NAME;
        if(!file_exists($tempDirFiles)){
            mkdir($tempDirFiles);
        }
        $tempDirFilesFile = $tempDirFiles . DIRECTORY_SEPARATOR . $streamsList->getFileName();
        if(!file_exists($tempDirFilesFile)){
            mkdir($tempDirFilesFile);
        }
        $tempDirFilesFileRes = $tempDirFiles . DIRECTORY_SEPARATOR . $streamsList->getFileName() .
        DIRECTORY_SEPARATOR . Con::TEMP_FILES_RES_DIR_NAME;
        if(!file_exists($tempDirFilesFileRes)){
            mkdir($tempDirFilesFileRes);
        }


        // Download files
        //

        $downloadsUrlServers = [];

		foreach($updatingGroup->getFilesToDownload() as $downloadKey => $download){

            if($verbose){
                echo '-Update stream "' . $downloadKey . '"... ';
            }

            $streamNode = $streamsGroup->getChild($downloadKey);
			$stateNode = $updatingGroup->getState()->getChild($downloadKey);
			$stateNode->setState(StreamStates::START);

            $errorText = null;

			switch($download->getDownloadType()){
				case StreamTypes::URL:{

                    // Some servers forbid too many requests to be made in the same time frame.
                    // To prevent requests from being rejected, it may be necessary to slow down
                    // the execution of the program between two requests to the same server.

                    // This option can be configured by the user in the configuration file.

                    // Check if a delay has been configured for requests on same server
                    if($streamsList->getUrlsDelay() > 0){

                        // Get server Hostname
                        $hostName = false;
                        $parsed = parse_url($download->getSourceURL());
                        if($parsed !== null && count($parsed) > 0){
                            if($parsed["host"] !== null){
                                $hostName = $parsed["host"];
                            }
                        }

                        // If hostname has not been already requested
                        if($hostName !== false){
                            if(!in_array($hostName, $downloadsUrlServers)){

                                // Sleep
                                // Note: Config variable "sameServerDelay" is exprimed
                                // in milliseconds. We must multiply it by 1000.
                                usleep($streamsList->getUrlsDelay() * 1000);

                                // Add server Hostname in the array
                                $downloadsUrlServers[] = $hostName;

                            }
                        }
                    }

					try{
						$download->start();
					}
					catch(\Exception $ex){
                        $errorText = Texts\downloadURLException($ex, $download->getSourceURL());
						ErrorLog::addLog($errorText);
					}

				}break;
				case StreamTypes::PATH:{

					try{
						$download->start();
					}
					catch(\Exception $ex){
                        $errorText = Texts\downloadPathException($ex, $download->getSourcePath());
						ErrorLog::addLog($errorText);
					}

				}break;
				case StreamTypes::FTP:{

					try{
						$download->start();
					}
					catch(\Exception $ex){
                        $errorText = Texts\downloadFTPException($ex, $download->getFtpAddress());
						ErrorLog::addLog($errorText);
					}

				}break;
			}

            $downloadDone = false;

			if($download->getIsComplete()){

                // Parse response???
                //

                $fileNameTmp = $tempDirFilesFileRes . DIRECTORY_SEPARATOR . $downloadKey;
                $fileName = $tempDirFilesFile . DIRECTORY_SEPARATOR . $downloadKey;

                $streamFileConfirm = true;

                $parseChecker = new ProcessParseStreamCheck();

                if(file_exists($fileNameTmp)){

                    $testParsable = false;
                    $isParsable = false;

                    if($streamNode->getParserName() !== null){
                        $testParsable = true;
                        $isParsable = $parseChecker->check($contextFactory, $streamNode, $fileNameTmp);
                    }

                    if(!$testParsable || ($testParsable && $isParsable) ){

                        $downloadDone = true;
                        $stateNode->setState(StreamStates::DOWNLOADED);
                        $stateNode->setLastTime($contextFactory->getCurrentDate()->getTimestamp());
                        $stateNode->increaseTotalDownloads();
                        $updatingGroup->increaseTotalDownloaded(1);

                        // Transfert temp response to definitive tmp files directory
                        Data::transfertStream(
                            $contextFactory,
                            $fileNameTmp,
                            $fileName
                        );

                    }
                    else{

                        // Delete temp file
                        @unlink($fileNameTmp);

                        $pre = 'Error while testing temp response/data for stream "' . $streamNode->getId() . '": ';
                        $errorText;
                        if($parseChecker->getErrorText() !== null){
                            $errorText = $pre . $parseChecker->getErrorText();
                        }
                        else{
                            $errorText = $pre . 'Parse failed without giving details...';
                        }

                    }
                }
                else{

                    $pre = 'Error while testing temp response/data for stream "' . $streamNode->getId() . '": ';
                    $errorText = $pre . 'File does not exists.';
                }

			}
			else{

			}

            if($downloadDone){

                if($verbose){
                    echo 'Done!' . PHP_EOL;
                }

            }
            else{

                $this->totalFailed++;
                $updatingGroup->addFileNotDownloaded($downloadKey, $download);

                if($errorText !== null){
                    ErrorLog::addLog($errorText);
                }

                if($verbose){
                    echo 'Failed' . PHP_EOL;
                    if($errorText !== null){
                        echo $errorText . PHP_EOL;
                    }
                }

            }

            // Dispatch event
            $arguments = [
                "group_id" => $updatingGroup->getId(),
                "id" => $downloadKey,
                "done" => $downloadDone
            ];
            $contextFactory->getPlugins()->dispatch($contextFactory->getContext(), "download", "onDownload", $arguments);

		}

	}

    /**
     * Get if at least one node has been updated / downloaded.
     * @returns boolean
     */
    public function getIsUpdated()
    {
        return $this->updated;
    }

    /**
     * Get the total number of downloaded streams.
     * @returns integer
     */
    public function getTotalDownloadedStreams()
    {
        return $this->totalDownloadedStreams;
    }

    /**
     * Get the total number of downloads failed.
     * @returns integer
     */
    public function getTotalFailed()
    {
        return $this->totalFailed;
    }

    /**
     * Get the total number of outdated streams.
     * @returns integer
     */
    public function getTotalOutdatedStreams()
    {
        return $this->totalOutdatedStreams;
    }

}