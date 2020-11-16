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
 * StreamState
 * Class representing the state of a stream in the streams list state file.
 */

namespace PHPStreamsAggregator\Models;

/**
 * StreamState
 */
class StreamState{

	/** @var StreamsListState The state object of the streams list managing this node. */
	private $stateManager;

    /**
     * @var int The state of the node
     * @see StreamStates to understand states codes
     */
	private $state;

    /** @var string The name of the node. */
	private $name;

	/** @var int The timestamp of the last download. */
	private $lastTime;

	/** @var int The timestamp of the last download. */
	private $totalDownloads;

	/**
	 * Constructor
	 * @param StreamsListState The state object of the streams list managing this node.
	 * @param string The name of the node.
	 * @param int The state of the download (see StreamStates to understand states codes).
	 * @param int The timestamp of the last time the file was downloaded.
     * @param int The total number of downloads
	 */
	public function __construct(&$stateManager, $name, $state = 0, $lastTime = 0, $totalDownloads = 0)
    {
		$this->stateManager = $stateManager;
		$this->name = $name;
		$this->state = $state;
		$this->lastTime = $lastTime;
        $this->totalDownloads = $totalDownloads;
	}

	/**
	 * Get the ID/name of this download.
	 * @return string
	 */
	public function getName()
    {
		return $this->name;
	}

	/**
	 * Get the current state of this download.
	 * @return int
     * @see StreamStates to understand states codes
	 */
	public function &getState()
    {
		return $this->state;
	}

	/**
	 * Get the timestamp of the last download completion occurred.
	 * @return int
	 */
	public function &getLastTime()
    {
		return $this->lastTime;
	}

	/**
	 * Get the total number of downloads.
	 * @return int
	 */
	public function &getTotalDownloads()
    {
		return $this->totalDownloads;
	}

	/**
	 * Set the state of this download.
	 * @param int The new state of the download
     * @see StreamStates to understand states codes
	 */
	public function setState($state)
    {
		$this->state = $state;
		$this->stateManager->setChanged();
	}

	/**
	 * Set the timestamp of the last download completion.
	 * @param int
	 */
	public function setLastTime($time)
    {
		$this->lastTime = $time;
		$this->stateManager->setChanged();
	}

	/**
	 * Set the total number of downloads
	 * @param int
	 */
	public function setTotalDownloads($v)
    {
		$this->totalDownloads = $v;
		$this->stateManager->setChanged();
	}

	/**
	 * Increase the total number of downloads.
	 * @param int
	 */
	public function increaseTotalDownloads($v = 1)
    {
		$this->totalDownloads += $v;
		$this->stateManager->setChanged();
	}

}