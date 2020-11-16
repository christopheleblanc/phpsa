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
 * ProcessGetGroupsToUpdate
 * Class used to find outdated groups in a StreamsList.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Controllers\AstractProcess;
use PHPStreamsAggregator\Controllers\Update;
use PHPStreamsAggregator\Models\DateTimeNumeric;
use PHPStreamsAggregator\Models\UpdateOptionTypes;

/**
 * ProcessGetGroupsToUpdate
 */
class ProcessGetGroupsToUpdate extends AstractProcess{

    /** @var Array - Array containing groups outdated */
    private $groupsToUpdate;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groupsToUpdate = [];
    }


    /**
     * Get groups to update
     * @param &ContextFactory  - The context
     * @param boolean          - Force update?
     */
    public function execute(&$contextFactory, $forceUpdateNow = false)
    {

        $this->groupsToUpdate = [];

        // If update is forced, add all groups to the array.
        if($forceUpdateNow){
            foreach($contextFactory->getStreamsList()->getChildren() as $groupKey => $groupNode){
                if($groupNode->isActive()){
                    $this->groupsToUpdate[$groupKey] = $groupNode;
                }
            }
        }
        else{

            $filesPath = $contextFactory->getTempDirectory() . DIRECTORY_SEPARATOR .
            Con::TEMP_FILES_DIR_NAME . DIRECTORY_SEPARATOR . $contextFactory->getStreamsList()->getFileName();

            // Check which group needs to be updated (depending on update option)
            foreach($contextFactory->getStreamsList()->getChildren() as $groupKey => $groupNode){

                if($groupNode->isActive()){

                    $groupNeedUpdate = false;
                    $stateGroup = $contextFactory->getStreamsState()->getChild($groupKey);

                    if($stateGroup !== null){

                        $lastUpdateDate = new DateTimeNumeric($stateGroup->getLastDownloadTime());

                        // Check if group need update by its last download time and update option
                        if(!$groupNeedUpdate){

                            $updateOptions = $groupNode->getUpdateOptions();

                            if(count($updateOptions) > 0){

                                $groupNeedUpdateI = 0;

                                foreach($updateOptions as $updateOption){

                                    switch($updateOption->getType()){
                                        case UpdateOptionTypes::EACH:{
                                            $groupNeedUpdateI++;
                                        }break;
                                        case UpdateOptionTypes::EVERY:{
                                            if(Update::isUpdateRequiredEvery(
                                                $contextFactory->getCurrentDate(),
                                                $lastUpdateDate,
                                                $stateGroup->getState(),
                                                $updateOption->getNumber(),
                                                $updateOption->getTimeType()
                                            )){
                                                $groupNeedUpdateI++;
                                            }
                                        }break;
                                        case UpdateOptionTypes::HOUR:{
                                            if(Update::isUpdateRequiredHour(
                                                $contextFactory->getCurrentDate(),
                                                $lastUpdateDate,
                                                $stateGroup->getState(),
                                                $updateOption->getHours(),
                                                $updateOption->getMinutes(),
                                                $updateOption->getSeconds()
                                            )){
                                                $groupNeedUpdateI++;
                                            }
                                        }break;
                                    }



                                }

                                if($groupNeedUpdateI > 0){
                                    $groupNeedUpdate = true;
                                }

                            }

                        }

                        if($groupNeedUpdate){
                            $this->groupsToUpdate[$groupKey] = $groupNode;
                        }

                    }
                    else{
                        $errStr = 'Error while loading state group "' . $groupKey . '".';
                        throw new \Exception($errStr);
                    }

                }

            }

        }

        $this->setIsComplete();
        return true;

    }

    /**
     * Get the groups to update
     * @returns &StreamsGroup[]
     */
    public function &getGroupsToUpdate()
    {
        return $this->groupsToUpdate;
    }

}