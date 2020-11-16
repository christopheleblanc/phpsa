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
 * StreamsListCompleteState
 * Class representing the complete state of the streams list. This class is virtually an
 * "extension" of (or a "bridge to") the class StreamsListState which allow the user to access all the functions
 * from class StreamsListState but also allow to access the value $isUpToDate which defines if the list
 * is up to date.
 *
 * This class is made this way because the state "is up to date" is calculated AFTER loading the
 * class DownloadListState, and because the class DownloadListState concern only the data stored in the state file.
 *
 * This class is mostly intended to be used in a StateViewer context.
 */

namespace PHPStreamsAggregator\Models;

use PHPStreamsAggregator\Controllers\StreamsListStateDAO;
use PHPStreamsAggregator\Models\StreamsGroupState;
use PHPStreamsAggregator\Models\StreamState;

/**
 * StreamsListCompleteState
 */
class StreamsListCompleteState{

	/** @var StreamsListState Instance of StreamsListState. */
	private $state;

	/** @var boolean Defines if the list is up to date. */
	private $isUpToDate;

	/**
	 * Constructor
	 * @param &StreamsListState - Instance of StreamsListState.
     * @param boolean - Defines if the list is up to date.
	 */
	public function __construct(&$state, &$isUpToDate)
    {
        $this->state = $state;
        $this->isUpToDate = $isUpToDate;
	}

    /**
	 * Return the instance of StreamsListStateDAO used to load and save.
     * @return &StreamsListStateDAO
	 */
    public function &getDAO()
    {
        return $this->state->getDAO();
    }

	/**
	 * Clear the states list.
	 * Warning: Use this function with precaution.
	 */
	public function clear()
    {
		$this->state->clear();
	}

	/**
	 * Get the number of nodes in this list.
	 * @return int The number of nodes in the list.
	 */
	public function size()
    {
		return $this->state->size();
	}

	/**
	 * Get a node of the list by its ID/name.
	 * @param string The ID/name of the node.
	 * @return StreamState|null The node, or null.
	 */
	public function &getChild($id)
    {
		return $this->state->getChild($id);
	}

	/**
	 * Get the current state of this list.
	 * @return int The state of the list
     * @see StreamStates to understand states codes
	 */
	public function &getState()
    {
		return $this->state->getState();
	}

	/**
	 * Get the timestamp of the last completion.
	 * @return int The timestamp of the last completion.
	 */
	public function &getLastCompletionTime()
    {
		return $this->state->getLastCompletionTime();
	}

	/**
	 * Get the timestamp of the last download.
	 * @return int The timestamp of the last download.
	 */
	public function &getLastDownloadTime()
    {
		return $this->state->getLastDownloadTime();
	}

	/**
	 * Get the timestamp of the last parse.
	 * @return int The timestamp of the last parse.
	 */
	public function &getLastParseTime()
    {
		return $this->state->getLastParseTime();
	}

	/**
	 * Get the timestamp of the last validation.
	 * @return int The timestamp of the last parse.
	 */
	public function &getLastValidationTime()
    {
		return $this->state->getLastValidationTime();
	}

	/**
	 * Get the number of entities which were missing during the last validation
	 * @return int The number of entities
	 */
	public function &getLastValidationMissing()
    {
		return $this->state->getLastValidationMissing();
	}

	/**
	 * Get the total number of completions.
	 * @return int The total number of completions.
	 */
	public function &getTotalCount()
    {
		return $this->state->getTotalCount();
	}

	/**
	 * Get the array of nodes of this list.
	 * @return StreamState[] The array of nodes of this list.
	 */
	public function &getChildren()
    {
		return $this->state->getChildren();
	}

	/**
	 * Get the array of nodes of this list.
	 * @return StreamState[] The array of nodes of this list.
	 */
	public function setChildren(&$children)
    {
		return $this->state->setChildren($children);
	}

	/**
	 * Declare that a change has occurred in the list.
	 */
	public function setChanged($v = null)
    {
		return $this->state->setChanged($v);
	}

    /**
	 * Declare that a change has occurred in the list.
	 */
	public function resetChanged()
    {
		return $this->state->resetChanged();
	}

	/**
	 * Check if a change has occured in the list.
	 * @return boolean True if a change has occured, or False.
	 */
	public function hasChanged()
    {
		return $this->state->hasChanged();
	}

	/**
	 * Increase the total number of completions.
	 */
	public function increaseTotalCount()
    {
		return $this->state->increaseTotalCount();
	}

	/**
	 * Set the timestamp of the last completion.
	 * @param int The timestamp.
	 */
	public function setLastCompletionTime($time)
    {
		return $this->state->setLastCompletionTime($time);
	}

	/**
	 * Set the timestamp of the last download.
	 * @param int The timestamp.
	 */
	public function setLastDownloadTime($time)
    {
		return $this->state->setLastDownloadTime($time);
	}

	/**
	 * Set the timestamp of the last parse.
	 * @param int The timestamp.
	 */
	public function setLastParseTime($time)
    {
		return $this->state->setLastParseTime($time);
	}

    /**
	 * Set the timestamp of the last validation.
	 * @param int The timestamp.
	 */
	public function setLastValidationTime($time)
    {
		return $this->state->setLastValidationTime($time);
	}

	/**
	 * Get the number of entities which were missing during the last validation
	 * @return int The number of entities
	 */
	public function setLastValidationMissing($v)
    {
		return $this->state->setLastValidationMissing($v);
	}

	/**
	 * Set a state of the list.
	 * @param int The new state of the list 
     * @see StreamStates to understand states codes
	 */
	public function setState($state)
    {
		return $this->state->setState($state);
	}

	/**
	 * Save the state file.
	 */
	public function save()
    {
        return $this->state->save();
	}

    /**
	 * Delete the state file.
	 */
    public function deleteFile()
    {
        return $this->state->deleteFile();
    }

	/**
	 * Save the state file only if a change has occurred.
	 */
	public function saveIfChanged()
    {
		return $this->state->saveIfChanged();
	}

    /**
     * Check if the streams list is up to date
     * @return boolean
     */
    public function getIsUpToDate()
    {
        return $this->isUpToDate;
    }

	/**
	 * Create
	 * @param &StreamsListState - Instance of StreamsListState.
     * @param boolean - Defines if the list is up to date.
	 */
    static public function create(&$streamsState, $isUpToDate)
    {
        return new StreamsListCompleteState($streamsState, $isUpToDate);
    }

}