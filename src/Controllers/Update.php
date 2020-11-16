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
 * Update
 * Class containing several functions to check update state of groups/streams.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Models\DateTimeNumeric;
use PHPStreamsAggregator\Models\StreamStates;
use PHPStreamsAggregator\Models\UpdateOptionEveryTimeTypes;
use PHPStreamsAggregator\Models\UpdateOptionTypes;

/**
 * Update
 */
class Update{

    /**
     * Check if the program must update when the update option is "Hour" (which means "update at such hour").
     *
     * @param   &DateTimeNumeric  - A reference to the current date of the program.
     * @param   &DateTimeNumeric  - A reference to the date of the last update.
     * @param   &integer          - A reference to the time of the daily update. Must be a integer
     *                              number between 0 and 23.
     * @param   &integer          - A reference to the time of the daily update. Must be a integer
     *                              number between 0 and 59.
     * @param   &integer          - A reference to the time of the daily update. Must be a integer
     *                              number between 0 and 59.
     * @return  boolean           - True if it's time to update, or False.
     */
    static private function isTimeToUpdateHour(&$currentDate, &$lastUpdateDate,
    &$hourOfUpdate, &$minutesOfUpdate, &$secondsOfUpdate)
    {

        // Create an instance of Datetime corresponding to the date of the current day
        // (at the time of the update)
        $todayUpdateDt = new \Datetime();
        $todayUpdateDt->setTime($hourOfUpdate, $minutesOfUpdate, $secondsOfUpdate);

        // This condition should never happen, but we test it anyway.
        if($lastUpdateDate->getDateTime() > $currentDate->getDateTime()){
            return false;
        }
        else{

            // Check if this update has been already done!!! // VERIFIED
            // Compare the date/time of the current day update with the last update date/time.
            if($lastUpdateDate->getDateTime() >= $todayUpdateDt){
                return false;
            }
            else{
                // Time logic
                if($currentDate->getHours() > $hourOfUpdate){
                    return true;
                }
                else if($currentDate->getHours() == $hourOfUpdate){
                    // Maybe now?
                    if($currentDate->getMinutes() > $minutesOfUpdate){
                        return true;
                    }
                    else if($currentDate->getMinutes() == $minutesOfUpdate){
                        // Maybe now??
                        if($currentDate->getSeconds() >= $secondsOfUpdate){
                            return true;
                        }
                        else{
                            return false;
                        }
                    }
                    else{
                        // Update later
                        return false;
                    }
                }
                else{
                    // Update later
                    return false;
                }
            }
        }
    }

    /**
    * Check if the program must update when update option is "Hour" (which means "update at such hour").
    *
    * @param  &DateTimeNumeric  - A reference to the current date of the program.
    * @param  &DateTimeNumeric  - A reference to the date of the last update.
    * @param  &integer          - A reference to the update state of the program.
    * @param  &integer          - A reference to the time of the daily update. Must be a integer
    *                             number between 0 and 23.
    * @param  &integer          - A reference to the time of the daily update. Must be a integer
    *                             number between 0 and 59.
    * @param  &integer          - A reference to the time of the daily update. Must be a integer
    *                             number between 0 and 59.
    * @return boolean           - True if it's time to update, or False.
    */
    static public function isUpdateRequiredHour(&$currentDate, &$lastUpdateDate, &$updateState, &$hourOfUpdate,
    &$minutesOfUpdate, &$secondsOfUpdate)
    {

        if(self::isTimeToUpdateHour($currentDate, $lastUpdateDate, $hourOfUpdate, $minutesOfUpdate, $secondsOfUpdate)){
            return true;
        }
        else{
            if($updateState < StreamStates::DOWNLOADED){
                return true;
            }
        }
        return false;

    }

    /**
    * Check if the program must update when update option is "Every" (which means "update every XXX units").
    *
    * @param  &DateTimeNumeric  - A reference to the current date of the program.
    * @param  &DateTimeNumeric  - A reference to the date of the last update.
    * @param  &integer          - The number of time units.
    * @param  &integer          - The time unit "Hours", "Minutes" or "Seconds" 
    *                             (see class/enum "UpdateOptionEveryTimeTypes" to understand)
    * @return boolean           - True if it's time to update, or False.
    */
    static private function isTimeToUpdateEvery(&$currentDate, &$lastUpdateDate, &$number, &$timeType)
    {
        $maxSeconds = 0;
        switch($timeType){
            case UpdateOptionEveryTimeTypes::SECONDS:{
                $maxSeconds = $number;
            }break;
            case UpdateOptionEveryTimeTypes::MINUTES:{
                $maxSeconds = $number * 60;
            }break;
            case UpdateOptionEveryTimeTypes::HOURS:{
                $maxSeconds = $number * 3600;
            }break;
        }

        if($currentDate->getTimestamp() >= ($lastUpdateDate->getTimestamp() + $maxSeconds)){
            return true;
        }
        else{
            return false;
        }
    }


    /**
    * Check if the program must update when update option is "Every" (which means "update every XXX units").
    *
    * @param  &DateTimeNumeric  - A reference to the current date of the program.
    * @param  &DateTimeNumeric  - A reference to the date of the last update.
    * @param  &integer          - The state of the node (see class/enum "StreamStates" to understand)
    * @param  &integer          - The number of time units.
    * @param  &integer          - The time type "Hours", "Minutes" or "Seconds"
    *                             (see class/enum "UpdateOptionEveryTimeTypes" to understand)
    * @return boolean           - True if it's time to update, or False.
    */
    static public function isUpdateRequiredEvery(&$currentDate, &$lastUpdateDate, &$updateState, &$number, &$timeType)
    {
        if(self::isTimeToUpdateEvery($currentDate, $lastUpdateDate, $number, $timeType)){
            return true;
        }
        else{
            if($updateState < StreamStates::DOWNLOADED){
                return true;
            }
        }
        return false;
    }


    /**
    * Check if the node is up to date when its update option is "Each" (which means "update at each process").
    *
    * @param  &DateTimeNumeric  - A reference to the current date of the program.
    * @param  &DateTimeNumeric  - A reference to the date of the last update.
    * @param  &integer          - The state of the node (see class/enum "StreamStates" to understand)
    * @return boolean           - True if the node is up to date, or False.
    */
    static public function isUpToDateEach(&$currentDate, &$lastUpdateDate, &$updateState)
    {
        if($currentDate->getTimestamp() <= $lastUpdateDate->getTimestamp() + 600 &&
        $updateState >= StreamStates::DOWNLOADED){
            return true;
        }
        return false;
    }

    /**
    * Check if the node is up to date when its update option is "Every" (which means "update every XXX units").
    *
    * @param  &DateTimeNumeric  - A reference to the current date of the program.
    * @param  &DateTimeNumeric  - A reference to the date of the last update.
    * @param  &integer          - The state of the node (see class/enum "StreamStates" to understand)
    * @param  &integer          - The number of time units.
    * @param  &integer          - The time type "Hours", "Minutes" or "Seconds"
    *                             (see class/enum "UpdateOptionEveryTimeTypes" to understand)
    * @return boolean           - True if the node is up to date, or False.
    */
    static public function isUpToDateEvery(&$currentDate, &$lastUpdateDate, &$updateState,
    &$number, &$timeType)
    {
        $maxSeconds = 0;
        switch($timeType){
            case UpdateOptionEveryTimeTypes::SECONDS:{
                $maxSeconds = $number;
            }break;
            case UpdateOptionEveryTimeTypes::MINUTES:{
                $maxSeconds = $number * 60;
            }break;
            case UpdateOptionEveryTimeTypes::HOURS:{
                $maxSeconds = $number * 3600;
            }break;
        }

        if($currentDate->getTimestamp() <= ($lastUpdateDate->getTimestamp() + $maxSeconds) &&
        $updateState >= StreamStates::DOWNLOADED){
            return true;
        }
        else{
            return false;
        }

    }


    /**
    * Check if the node is up to date when its update option is "Hour" (which means "update at such hour").
    * @param  &DateTimeNumeric  - A reference to the current date of the program.
    * @param  &DateTimeNumeric  - A reference to the date of the last update.
    * @param  &integer          - A reference to the update state of the program.
    * @param  &integer          - A reference to the time of the daily update. Must be a integer number between 0 and 23.
    * @param  &integer          - A reference to the time of the daily update. Must be a integer number between 0 and 59.
    * @param  &integer          - A reference to the time of the daily update. Must be a integer number between 0 and 59.
    * @return boolean           - True if the node is up to date, or False.
    */
    static public function isUpToDateHour(&$currentDate, &$lastUpdateDate, &$updateState, &$hourOfUpdate,
    &$minutesOfUpdate, &$secondsOfUpdate)
    {
        if(!self::isTimeToUpdateHour($currentDate, $lastUpdateDate, $hourOfUpdate, $minutesOfUpdate, $secondsOfUpdate) &&
        $updateState >= StreamStates::DOWNLOADED){
            return true;
        }
        return false;
    }

    /*
     * Check if a node is up to date.
     * @param   &StreamsGroup     - An instance of StreamsGroup (the group itself, or a group
     *                              which is the parent of a node)
     * @param   &DateTimeNumeric  - The current date
     * @param   &DateTimeNumeric  - The node comparision date
     * @param   integer           - The node state
     * @returns boolean           - True if the node is up to date, or false.
     */
    static public function isNodeUpToDate(&$downloadGroup, &$currentDate, &$refDate, $nodeState)
    {

        $nodeIsUpToDate = 0;

        foreach($downloadGroup->getUpdateOptions() as $updateOption){
            switch($updateOption->getType()){
                case UpdateOptionTypes::EACH:{
                    if(self::isUpToDateEach(
                        $currentDate,
                        $refDate,
                        $nodeState
                    )){
                        $nodeIsUpToDate++;
                    }
                }break;
                case UpdateOptionTypes::EVERY:{
                    if(self::isUpToDateEvery(
                        $currentDate,
                        $refDate,
                        $nodeState,
                        $updateOption->getNumber(),
                        $updateOption->getTimeType()
                    )){
                        $nodeIsUpToDate++;
                    }
                }break;
                case UpdateOptionTypes::HOUR:{
                    if(self::isUpToDateHour(
                        $currentDate,
                        $refDate,
                        $nodeState,
                        $updateOption->getHours(),
                        $updateOption->getMinutes(),
                        $updateOption->getSeconds()
                    )){
                        $nodeIsUpToDate++;
                    }
                }break;
            }

        }

        if($nodeIsUpToDate == count($downloadGroup->getUpdateOptions())){
            return true;
        }
        else{
            return false;
        }

    }

    /**
	 * Check if streams list nodes are up to date.
     * @param   &DateTimeNumeric   - The current date
     * @param   &StreamsList       - An instance of streamsList
     * @param   &StreamsListState  - An instance of StreamsListState
     * @returns boolean            - True if list is up to date, or false.
	 */
    static public function areStreamsUpToDate(&$currentDate, &$streamsList, &$streamsState)
    {

        $totalNodes = 0;
        $nodesUpToDate = 0;

        // Check which group needs to be updated
        foreach($streamsList->getChildren() as $groupKey => $groupNode){

            if($groupNode->isActive()){

                $groupNeedUpdate = false;
                $stateGroup = $streamsState->getChild($groupKey);

                if($stateGroup !== null){

                    $updateOptions = $groupNode->getUpdateOptions();

                    if(count($updateOptions) > 0){

                        foreach($groupNode->getChildren() as $downloadNodeKey => $downloadNode){

                            if($downloadNode->isActive()){

                                $stateNode = $stateGroup->getChild($downloadNodeKey);

                                if($stateNode !== null){

                                    $lastDownloadDate = new DateTimeNumeric($stateNode->getLastTime());
                                    $nodeIsUpToDate = self::isNodeUpToDate($groupNode, $currentDate, $lastDownloadDate, $stateNode->getState());

                                    if($nodeIsUpToDate){
                                        $nodesUpToDate++;
                                    }

                                    $totalNodes++;

                                }
                            }
                        }
                    }
                }
            }
        }

        return ($totalNodes == $nodesUpToDate);

    }

    /**
	 * Check if streams list nodes are up to date.
     * @param   &DateTimeNumeric   - The current date
     * @param   &StreamsList       - An instance of streamsList
     * @param   &StreamsListState  - An instance of StreamsListState
     * @returns boolean            - True if list is up to date, or false.
	 */
    static public function areGroupsUpToDate(&$currentDate, &$streamsList, &$streamsState)
    {

        $totalGroups = 0;
        $groupsUpToDate = 0;

        // Check which group needs to be updated
        foreach($streamsList->getChildren() as $groupKey => $groupNode){

            if($groupNode->isActive()){

                $groupNeedUpdate = false;
                $stateGroup = $streamsState->getChild($groupKey);

                if($stateGroup !== null){

                    $lastDownloadDate = new DateTimeNumeric($stateGroup->getLastDownloadTime());
                    $groupIsUpToDate = self::isNodeUpToDate($groupNode, $currentDate, $lastDownloadDate, $stateGroup->getState());

                    if($groupIsUpToDate){
                        $groupsUpToDate++;
                    }
                    $totalGroups++;

                }
            }
        }

        return ($totalGroups == $groupsUpToDate);

    }

}