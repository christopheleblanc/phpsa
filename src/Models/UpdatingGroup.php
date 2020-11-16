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
 * UpdatingGroup
 * Class used to store several data during the update of a group of streams/feeds.
 */

namespace PHPStreamsAggregator\Models;

/**
 * UpdatingGroup
 */
class UpdatingGroup{

    /** @var string - The id of the group **/
    private $id;

    /** @var StreamsListState - The state of the group **/
    private $state;

    /** @var Array - An array containing the files to download. **/
	private $filesToDownload;

    /** @var integer - The total number of downloaded files. **/
    private $totalDownloaded;

    /** @var Array - An array containing the ids/names of the files which were not downloaded. **/
    private $filesNotDownloaded;

	/**
	 * Constructor
     * @var string The id of the group
     * @var &StreamsListState Instance of the StreamsListState for the current group.
	 */
	public function __construct($id, &$state)
    {

        $this->id = $id;
        $this->state = $state;
		$this->filesToDownload = [];
        $this->totalDownloaded = 0;
        $this->filesNotDownloaded = [];

	}

    /**
     * Add a file to download
     * @param string - The id/name of the file
     * @param XXX - The file to download
     **/
    public function addFileToDownload($id, &$file)
    {
        $this->filesToDownload[$id] = $file;
    }

    /**
     * Add a file not downloaded
     * @param string - The id/name of the file
     * @param XXX - The file not downloaded
     **/
    public function addFileNotDownloaded($id, &$file)
    {
        $this->filesNotDownloaded[$id] = $file;
    }

    /**
     * Get the id/name of the group
     * @returns string
     **/
    public function &getId()
    {
        return $this->id;
    }

    /**
     * Get the instance of StreamsListState of the group
     * @returns StreamsListState
     **/
    public function &getState()
    {
        return $this->state;
    }

    /**
     * Get the array of files to download
     * @returns Array
     **/
    public function &getFilesToDownload()
    {
        return $this->filesToDownload;
    }

    /**
     * Get the array of files which were not downloaded
     * @returns Array
     **/
    public function &getFilesNotDownloaded()
    {
        return $this->filesNotDownloaded;
    }

    /**
     * Get the total number of downloaded
     * @returns integer
     **/
    public function &getTotalDownloaded()
    {
        return $this->totalDownloaded;
    }

    /**
     * Increase the total number of downloaded files by the value given in parameter.
     * @param integer - The value that needs to be added
     **/
    public function increaseTotalDownloaded($v)
    {
        $this->totalDownloaded += $v;
    }

}