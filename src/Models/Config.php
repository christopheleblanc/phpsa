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
 * Config
 * Class representing the configuration of the program, loaded from
 * configuration file by class "ConfigLoader".
 */

namespace PHPStreamsAggregator\Models;

/**
 * Config
 */
class Config{

	/** @var boolean Defines if the program is active or not. */
	private $active;

	/** @var string Temp directory. */
	private $tempDir;

    /** @var int The name of the streams list file. */
    private $listFile;

    /**
     * @var int The delay used between two downloads on the same server to limit
     * requests (in milliseconds).
     * Note: Delay is exprimed in milliseconds. During the process, the program call
     * the function "usleep()" which take a number of microseconds, calculated by 
     * multiplying this value by 1000.
     */
    private $urlsDelay;

    private $unregisteredValues;

	/**
	 * Constructor
	 */
	public function __construct($active = true, &$tempDir, &$listFile = null, $urlsDelay = 0, &$unregisteredValues = [])
    {
        $this->active = $active;
        $this->tempDir = $tempDir;
        $this->listFile = $listFile;
        $this->urlsDelay = $urlsDelay;
        $this->unregisteredValues = $unregisteredValues;
	}

	/**
	 * Get the state "Active" of the program.
	 * @return boolean The state.
	 */
	public function &isActive()
    {
		return $this->active;
	}

	/**
	 * Get the temporary files directory.
	 * @return string.
	 */
	public function &getTempDir()
    {
		return $this->tempDir;
	}

	/**
	 * Get the hour of the daily update.
	 * @return int The hour of the daily update.
	 */
	public function &getDownloadListFile()
    {
		return $this->listFile;
	}

	/**
	 * Get the SimpleXMLElement.
	 * @return SimpleXMLElement The SimpleXMLElement.
	 */
	public function &getSimpleXMLElement()
    {
		return $this->simpleXml;
	}

    /**
	 * Get the delay between downloads on same server.
	 * @return SimpleXMLElement The SimpleXMLElement.
	 */
    public function getUrlsDelay()
    {
        return $this->urlsDelay;
    }

    /**
	 * Get an configuration value by its id / node name.
     * Will return null if no option found.
	 * @return string|null
	 */
    public function getValue($id)
    {
        if(array_key_exists($id, $this->unregisteredValues)){
            return $this->unregisteredValues[$id];
        }
        return null;
    }

}