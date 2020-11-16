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
 * StreamsGroupState
 * Class representing the state of a streams group in the streams list state file.
 */

namespace PHPStreamsAggregator\Models;

use PHPStreamsAggregator\Models\StreamState;

/**
 * StreamsGroupState
 */
class StreamsGroupState{

    /** @var StreamsListState The state object of the streams list managing this node. */
	private $stateManager;

	/** @var int The total number of completions. */
	private $totalDownloads;

	/**
     * @var int The state of the list
     * @see StreamStates to understand states codes
     */
	private $state;

	/** @var int The timestamp of the last completion. */
	private $lastCompletionTime;

	/** @var int The timestamp of the last download. */
	private $lastDownloadTime;

	/** @var string The id of the node. */
	private $id;

    /** @var StreamsListState The state object of the streams list managing this node. */
	private $children;

    /** @var int The length of the list. */
	private $childrenLength = 0;

	/** 
	 * Defines if the state of the list has changed.
	 * This variable is automatically set to true when a change occurs in the list 
	 * and allows the program to know whether or not to save a new version of the file.
	 * @var boolean Defines if the state of the list has changed.
	 */
	private $hasChanged;

	/**
	 * Constructor
	 * @param StreamsListState The state object of the streams list managing this node.
	 * @param string The unique name/id of this node.
     * @param &StreamsListState[] The array of children of this group.
     * @param integer The number of children in this group.
     * @param integer The number of times the process of this group has been completed.
     * @param integer The state of this group (see StreamStates to understand states codes)
     * @param integer - The timestamp of the last time the process has been completed (in seconds)
     * @param integer - The timestamp of the last time all streams has been downloaded (in seconds)
     * @param integer - The timestamp of the last time all streams has been parsed (in seconds)
	 */
	public function __construct(&$stateManager, $id, &$children, $childrenLength = null,
    $totalDownloads = 0, $state = 0, $lastCompletionTime = 0, $lastDownloadTime = 0)
    {
        $this->stateManager = $stateManager;
        $this->id = $id;
		$this->children = $children;
        $this->childrenLength = ($childrenLength === null) ? count($children) : $childrenLength;
        $this->totalDownloads = $totalDownloads;
        $this->state = $state;
        $this->lastCompletionTime = $lastCompletionTime;
        $this->lastDownloadTime = $lastDownloadTime;
        $this->hasChanged = false;
	}

    /**
	 * Get the array of children
	 */
	public function &getChildren()
    {
		return $this->children;
	}

    /**
	 * Get the array of children
	 */
	public function setChildren(&$children, $childrenLength = null)
    {
		$this->children = $children;
        $this->childrenLength = ($childrenLength === null) ? count($children) : $childrenLength;
	}

	/**
	 * Get the ID/name of this download.
	 * @return string The ID/name of this download
	 */
	public function getId()
    {
		return $this->id;
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
	 * Get the timestamp of the last download.
	 * @return int The timestamp of the last download.
	 */
	public function &getLastDownloadTime()
    {
		return $this->lastDownloadTime;
	}

	/**
	 * Get the total number of completions.
	 * @return int The total number of completions.
	 */
	public function &getTotalDownloads()
    {
		return $this->totalDownloads;
	}

    /**
	 * Add a node to the list.
	 * @param Stream The node of the streams list to add in the list of states.
	 * @return int The number of nodes in the list.
	 */
	public function addChild($child)
    {

		$name = $child->getId();

		if(!isset($this->list[$name])){
			$this->children[$name] = new StreamState($this->stateManager, $name, 0, 0, 0);
			$this->childrenLength++;
			$this->setChanged();
		}

	}

    /**
	 * Get a node of the list by its ID/name.
	 * @param string The ID/name of the node.
	 * @return StreamState|null The node, or null.
	 */
	public function &getChild($id)
    {
		if(array_key_exists($id, $this->children)){
			return $this->children[$id];
		}
		$out = null;
		return $out;
	}

    /**
	 * Get the number of nodes in this list.
	 *
	 * @return int The number of nodes in the list.
	 */
	public function size()
    {
		return $this->childrenLength;
	}

	/**
	 * Declare that a change has occurred in the list.
	 */
	public function setChanged()
    {
		$this->hasChanged = true;
        $this->stateManager->setChanged();
	}

    /**
	 * Clear the states list.
	 * Warning: Use this function with precaution.
	 */
	public function clear()
    {
		$this->children = [];
		$this->childrenLength = 0;
		$this->setChanged();
	}

    /**
	 * Set a state of the list.
	 * @param int The new state of the list.
     * @see StreamStates to understand states codes
	 */
	public function setState($state)
    {
		$this->state = $state;
		$this->setChanged();
	}

    /**
	 * Increase the total number of completions.
	 */
	public function increaseTotalDownloads($v = 1)
    {
		$this->totalDownloads += $v;
		$this->setChanged();
	}

    /**
	 * Set the timestamp of the last completion.
	 * @param int The timestamp.
	 */
	public function setLastDownloadTime($time)
    {
		$this->lastDownloadTime = $time;
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
	 * Add a new node in the list for each node contained in the streams list.
	 * @param StreamsGroup The streams list group.
	 */
	public function createFromDownloadListGroup(&$downloadListGroup)
    {
        $nodesArray = $downloadListGroup->getChildren();
        foreach($nodesArray as $nodeKey => $node){
            $this->addChild($node);
        }
	}

}