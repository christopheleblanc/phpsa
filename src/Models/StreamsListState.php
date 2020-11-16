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
 * StreamsListState
 * Class representing the state of the streams list. It is the main class and
 * root node of a parent/children structure representing each stream state.
 */

namespace PHPStreamsAggregator\Models;

use PHPStreamsAggregator\Controllers\StreamsListStateDAO;
use PHPStreamsAggregator\Models\StreamsGroupState;
use PHPStreamsAggregator\Models\StreamState;

/**
 * StreamsListState
 */
class StreamsListState{

	/** @var StreamsListStateDAO DAO class used to load and save data. */
	private $dao;

	/** @var int The total number of completions. */
	private $count;

	/**
     * @var int The state of the list
     * @see StreamStates to understand states codes
     */
	private $state;

	/** @var int The timestamp of the last completion. */
	private $lastCompletionTime;

	/** @var int The timestamp of the last download. */
	private $lastDownloadTime;

	/** @var int The timestamp of the last parse. */
	private $lastParseTime;

	/** @var int The timestamp of the last update called. */
    private $lastUpdateTime;

	/** @var int The timestamp of the last update called. */
    private $currentOutdateTime;

    /** @var int The timestamp of the last validation time. */
    private $lastValidationTime;

    /** @var int The number of entities which were missing during the last validation. */
    private $lastValidationMissing;

	/** @var StreamsGroupState[] Array of groups */
	private $list;

	/** @var int The length of the list. */
	private $listLength = 0;

	/** 
	 * Defines if the state of the list has changed.
	 * This variable is automatically set to true when a change occurs in the list 
	 * and allows the program to know whether or not to save a new version of the file.
	 *
	 * @var boolean Defines if the state of the list has changed.
	 */
	private $hasChanged;

	/** 
	 * Defines if the current state data is new or if it was loaded from existing data.
	 *
	 * @var boolean True if the data is new, or False.
	 */
	private $isNew;

	/**
	 * Constructor
	 * @param &StreamsListStateDAO - The instance of StreamsListStateDAO used to load and save.
     * @param boolean - Defines if the data is new or loaded from existing data.
     * @param integer - The number of times #####################
     * @param integer - The state of the last process
     * @param integer - The timestamp of the last time the process has been completed (in seconds)
     * @param integer - The timestamp of the last time one stream has been downloaded (in seconds)
     * @param integer - The timestamp of the last time one stream has been parsed (in seconds)
     * @param integer - The timestamp of the last time the output file has been validated (in seconds)
     * @param integer - The number of entities which were missing during the last validation
     * @param &StreamsGroupState[] - The array of groups
     * @param integer|null - The size of the array of groups
	 */
	public function __construct(&$dao, $isNew, $count, $state, $lastUpdateTime, $lastCompletionTime,
    $lastDownloadTime, $lastParseTime, $lastValidationTime, $lastValidationMissing, &$currentOutdateTime)
    {
        $this->dao = $dao;
        $this->isNew = $isNew;
        $this->count = $count;
        $this->state = $state;
        $this->lastUpdateTime = $lastUpdateTime;
        $this->lastCompletionTime = $lastCompletionTime;
        $this->lastDownloadTime = $lastDownloadTime;
        $this->lastParseTime = $lastParseTime;
        $this->lastValidationTime = $lastValidationTime;
        $this->lastValidationMissing = $lastValidationMissing;
        $this->currentOutdateTime = $currentOutdateTime;

        $this->list = [];
        $this->listLength = count($this->list);

        $this->hasChanged = false;

	}

    /**
	 * Return the instance of StreamsListStateDAO used to load and save.
     * @return &StreamsListStateDAO
	 */
    public function &getDAO()
    {
        return $this->dao;
    }

    /**
	 * Defines if the current state data is new or if it was loaded from existing data
     * @return boolean True if the data is new, or False.
	 */
    public function getIsNew()
    {
        return $this->isNew;
    }

	/**
	 * Clear the states list.
	 * Warning: Use this function with precaution.
	 */
	public function clear()
    {
		$this->list = [];
		$this->listLength = 0;
		$this->setChanged();
	}

	/**
     * Completely remake the structure from an instance of StreamsList.
     * This function is faster alternative to the function "updateFromDownloadList"
     * which can be used when the data is clearly not up to date.
	 * @param StreamsList The streams list.
	 */
	private function remakeFromDownloadList(&$streamsList)
    {

        $groupsArray = $streamsList->getChildren();

		foreach($groupsArray as $groupKey => $groupNode){

            $stateNodesArray = [];
            foreach($groupNode->getChildren() as $nodeKey => $node){

                $stateNodesArray[$node->getId()] = new StreamState(
                    $this,
                    $node->getId()
                );

            }

            $this->list[$groupNode->getId()] = new StreamsGroupState(
                $this,
                $groupNode->getId(),
                $stateNodesArray
            );

		}

        $this->listLength = count($this->list);
        $this->hasChanged = true;

	}

    /**
	 * Update the structure with missing groups/nodes from an instance of StreamsList.
     * This function is dedicated to update the structure to correspond to the instance
     * of StreamsList by checking the existence of each group and each node and create
     * ones when they don't exist.
     * Please note that this function can be replaced by the function "remakeFromDownloadList",
     * which is much faster and must be privileged when it is obvious that all nodes
     * (or a large part of them) needs to be updated.
	 * @param StreamsList The streams list.
	 */
    private function updateFromDownloadList(&$streamsList)
    {
        $newGroupsArray = [];

        $groupsArray = $streamsList->getChildren();
        foreach($groupsArray as $groupKey => $groupNode){

            $nodesArray = [];
            $newNodesArray = [];

            $groupExists;
            if(array_key_exists($groupKey, $this->list)){
                $nodesArray = $this->getChild($groupKey)->getChildren();
                $groupExists = true;
            }
            else{
                $nodesArray = [];
                $groupExists = false;
            }

            $stateNodesArray = [];
            foreach($groupNode->getChildren() as $nodeKey => $node){

                $nodeExists;
                if(array_key_exists($nodeKey, $nodesArray)){
                    $newNodesArray[$nodeKey] = $this->list[$groupKey][$nodeKey];
                }
                else{
                    $newNodesArray[$nodeKey] = new StreamState(
                        $this,
                        $nodeKey
                    );
                }

            }

            if($groupExists){
                $stateGroup = $this->getChild($groupKey);
                $stateGroup->setChildren($newNodesArray);
                $newGroupsArray[$groupKey] = $stateGroup;
            }
            else{
                $newGroupsArray[$groupKey] = new StreamsGroupState(
                    $this,
                    $groupNode->getId(),
                    $tmpStatesArray
                );
            }

        }

        $this->list = $newGroupsArray;
        $this->listLength = count($this->list);
        $this->hasChanged = true;
    }

    /**
	 * Update the structure of the state file to correspond to the StreamsList file.
	 * @param StreamsList The streams list.
	 */
    public function updateStructure(&$streamsList)
    {
        if($this->size() == 0){
            $this->remakeFromDownloadList($streamsList);
        }
        else{
            $this->updateFromDownloadList($streamsList);
        }
    }

	/**
	 * Get the number of nodes in this list.
	 * @return int The number of nodes in the list.
	 */
	public function size()
    {
		return $this->listLength;
	}

	/**
	 * Get a node of the list by its ID/name.
	 * @param string The ID/name of the node.
	 * @return StreamState|null The node, or null.
	 */
	public function &getChild($id)
    {
		if(isset($this->list[$id])){
			return $this->list[$id];
		}
		$out = null;
		return $out;
	}

	/**
	 * Get the current state of this list.
	 * @return int The state of the list
     * @see StreamStates to understand states codes
	 */
	public function &getState()
    {
		return $this->state;
	}

	/**
	 * Get the timestamp of the last completion.
	 * @return int The timestamp of the last completion.
	 */
	public function &getLastCompletionTime()
    {
		return $this->lastCompletionTime;
	}

	/**
	 * Get the timestamp of the last completion.
	 * @return int The timestamp of the last completion.
	 */
	public function &getCurrentOutdateTime()
    {
		return $this->currentOutdateTime;
	}

	/**
	 * Get the timestamp of the last download.
	 * @return int The timestamp of the last download.
	 */
	public function &getLastUpdateTime()
    {
		return $this->lastUpdateTime;
	}

	/**
	 * Get the timestamp of the last download.
	 * @return int The timestamp of the last download.
	 */
	public function &getLastDownloadTime()
    {
		return $this->lastDownloadTime;
	}

	/**
	 * Get the timestamp of the last parse.
	 * @return int The timestamp of the last parse.
	 */
	public function &getLastParseTime()
    {
		return $this->lastParseTime;
	}

	/**
	 * Get the timestamp of the last validation.
	 * @return int The timestamp of the last parse.
	 */
	public function &getLastValidationTime()
    {
		return $this->lastValidationTime;
	}

	/**
	 * Get the number of entities which were missing during the last validation
	 * @return int The number of entities
	 */
	public function &getLastValidationMissing()
    {
		return $this->lastValidationMissing;
	}

	/**
	 * Get the total number of completions.
	 * @return int The total number of completions.
	 */
	public function &getTotalCount()
    {
		return $this->count;
	}

	/**
	 * Get the array of nodes of this list.
	 * @return StreamState[] The array of nodes of this list.
	 */
	public function &getChildren()
    {
		return $this->list;
	}

	/**
	 * Get the array of nodes of this list.
	 * @return StreamState[] The array of nodes of this list.
	 */
	public function setChildren(&$children)
    {
		$this->list = $children;
        $this->listLength = count($this->list);
	}

	/**
	 * Declare that a change has occurred in the list.
	 */
	public function setChanged($v = null)
    {
		$this->hasChanged = ($v !== null) ? $v : true;
	}

    /**
	 * Declare that a change has occurred in the list.
	 */
	public function resetChanged()
    {
		$this->hasChanged = false;
	}

	/**
	 * Check if a change has occured in the list.
	 * @return boolean True if a change has occured, or False.
	 */
	public function hasChanged()
    {
		return $this->hasChanged;
	}

	/**
	 * Increase the total number of completions.
	 */
	public function increaseTotalCount()
    {
		$this->count++;
		$this->setChanged();
	}

	/**
	 * Set the timestamp of the last completion.
	 * @param int The timestamp.
	 */
	public function setLastCompletionTime($time)
    {
		$this->lastCompletionTime = $time;
		$this->setChanged();
	}

	/**
	 * Set the timestamp of the last completion.
	 * @param int The timestamp.
	 */
	public function setCurrentOutdateTime($time)
    {
		$this->currentOutdateTime = $time;
		$this->setChanged();
	}

	/**
	 * Set the timestamp of the last download.
	 * @param int The timestamp.
	 */
	public function setLastDownloadTime($time)
    {
		$this->lastDownloadTime = $time;
		$this->setChanged();
	}

	/**
	 * Set the timestamp of the last update called.
	 * @param int The timestamp.
	 */
	public function setLastUpdateTime($time)
    {
		$this->lastUpdateTime = $time;
		$this->setChanged();
	}

	/**
	 * Set the timestamp of the last parse.
	 * @param int The timestamp.
	 */
	public function setLastParseTime($time)
    {
		$this->lastParseTime = $time;
		$this->setChanged();
	}

    /**
	 * Set the timestamp of the last validation.
	 * @param int The timestamp.
	 */
	public function setLastValidationTime($time)
    {
		$this->lastValidationTime = $time;
		$this->setChanged();
	}

	/**
	 * Get the number of entities which were missing during the last validation
	 * @return int The number of entities
	 */
	public function setLastValidationMissing($v)
    {
		$this->lastValidationMissing = $v;
	}

	/**
	 * Set a state of the list.
	 * @param int The new state of the list 
     * @see StreamStates to understand states codes
	 */
	public function setState($state)
    {
		$this->state = $state;
		$this->setChanged();
	}

	/**
	 * Save the state file.
	 */
	public function save()
    {
        $this->dao->save($this);
	}

    /**
	 * Delete the state file.
	 */
    public function deleteFile()
    {
        $this->dao->deleteFile();
    }

	/**
	 * Save the state file only if a change has occurred.
	 */
	public function saveIfChanged()
    {
		if($this->hasChanged){
			$this->save();
            $this->resetChanged();
		}
	}

    /**
	 * Create an instance of StreamsListState from loaded data
     * @param string  - The program temporary directory
	 * @param string  - The file name
	 */
    static public function create(&$tempPath, &$fileName)
    {
        $dao = StreamsListStateDAO::create($tempPath, $fileName);
        return $dao->load();
    }

}