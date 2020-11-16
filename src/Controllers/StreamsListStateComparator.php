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
 * StreamsListStateComparator
 * Class used to compare the streams list with the list state. It is mainly used
 * to warranty that the state file correspond to the list file.
 */

namespace PHPStreamsAggregator\Controllers;

/**
 * StreamsListStateComparator
 */
class StreamsListStateComparator{

	/** @var boolean The two arrays of nodes are equal. */
	private $equal;

	/**
	 * Constructor
	 *
	 * @param StreamsList The streams list group to compare with the state list.
	 * @param StreamsListState The state list group.
	 */
	public function __construct(&$streamsList, &$streamsState)
    {

		$this->equal = false;

        // If State loading encountered an error. Consider reloading all data.
        if($streamsState->getDAO()->getTotalLoadingErrors() > 0){
            $this->equal = false;
            return;
        }

		$downloadListSize = $streamsList->size();

		if($downloadListSize != $streamsState->size()){
			$this->equal = false;
		}
		else{

            $errs = 0;
            $downloadGroupsKeys = array_keys($streamsList->getChildren());
            $stateGroupsKeys = array_keys($streamsState->getChildren());

            for($i = 0; $i < $downloadListSize; $i++){

                if(strcmp($downloadGroupsKeys[$i], $stateGroupsKeys[$i]) !== 0){
                    $errs++;
                }
                else{

                    $downloadListGroup = $streamsList->getChild($downloadGroupsKeys[$i]);
                    $downloadStateGroup = $streamsState->getChild($stateGroupsKeys[$i]);

                    if($downloadListGroup->size() != $downloadStateGroup->size()){
                        $errs++;
                    }
                    else{

                        $downloadNodesKeys = array_keys($downloadListGroup->getChildren());
                        $stateNodesKeys = array_keys($downloadStateGroup->getChildren());

                        for($j = 0; $j < $downloadListGroup->size(); $j++){
                            if(strcmp($downloadNodesKeys[$j], $stateNodesKeys[$j]) !== 0){
                                $errs++;
                            }
                        }

                    }

                }

            }

            if($errs == 0){
                $this->equal = true;
            }
            else{
                $this->equal = false;
            }

		}

	}

	/**
	 * Check if the two lists are equal.
	 *
	 * @return boolean True if the two lists are equal, or False.
	 */
	public function equal()
    {
		return $this->equal;
	}

}