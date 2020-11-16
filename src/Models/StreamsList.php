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
 * StreamsList
 * Class representing the list of groups of files to download.
 * This list, loaded at the launch of the program, defines the groups 
 * of files to download.
 * It also defines whether a download is enabled or disabled.
 *
 * Read the program documentation for more information.
 */

namespace PHPStreamsAggregator\Models;

use PHPStreamsAggregator\Constants as Con;

/**
 * StreamsList
 */
class StreamsList{

	/** @var boolean Defines if the program is active or not. */
	private $isActive;

    /** @var String The name of the file. */
    private $fileName;

    /** @var string The name of the output file. */
    private $outputFile;

    /**
     * @var int The delay used between two downloads on the same server to limit
     * requests (in milliseconds).
     * Note: Delay is exprimed in milliseconds. During the process, the program call
     * the function "usleep()" which take a number of microseconds, calculated by 
     * multiplying this value by 1000.
     */
    private $urlsDelay;

    /** @var int The name of the class used to validate output file. */
    private $validatorClassName;

    /** @var int The name of the class used to mix parsed objects. */
    private $mixerClassName;

    /** @var int The name of the function used to save the output file. */
    private $makerClassName;

    /** @var string[] The name of the class used to mix parsed objects. */
    private $runnersClassNames;

	/** @var StreamsGroup[] Array of nodes */
	private $children;

	/** @var int The length of the list. */
	private $totalChildren = 0;

    /** string[] Array of options. */
    private $options;

	/**
	 * Constructor
     * @param String - The name of the file
     * @param StreamsGroup[] The array of nodes
     * @param int The size of the array of nodes 
	 */
	public function __construct(&$isActive, &$fileName, &$outputFile, &$runnersClassNames, &$mixerClassName,
    &$validatorClassName, &$makerClassName, &$urlsDelay, &$children, $totalChildren = null, &$options = [])
    {
        $this->isActive = $isActive;
        $this->fileName = $fileName;
        $this->outputFile = $outputFile;
        $this->runnersClassNames = $runnersClassNames;
        $this->mixerClassName = $mixerClassName;
        $this->validatorClassName = $validatorClassName;
        $this->makerClassName = $makerClassName;
        $this->urlsDelay = $urlsDelay;
		$this->children = $children;
		$this->totalChildren = ($totalChildren === null) ? count($totalChildren) : $totalChildren;
        $this->options = $options;
	}

	/**
	 * Get a node of the list by its ID/name, or Null if no node was found.
	 * @param string The ID/name of the node.
	 * @return StreamState|null
	 */
	public function &getChild($id){
		if(isset($this->children[$id])){
			return $this->children[$id];
		}
		$out = null;
		return $out;
	}

	/**
	 * Get the array of children
	 * @return StreamsGroup[] The array of nodes
	 */
	public function &getChildren()
    {
		return $this->children;
	}

	/**
	 * Get the name of the streams list file.
	 * @return String
	 */
	public function &getFileName()
    {
		return $this->fileName;
	}

	/**
	 * Check if the program is active or not
	 * @return boolean The state.
	 */
	public function &getIsActive()
    {
		return $this->isActive;
	}

	/**
	 * Get the name of the class used as "Maker" if it has been defined, or null.
	 * @return &string|null
	 */
	public function &getMakerClassName()
    {
		return $this->makerClassName;
	}

	/**
	 * Get the name of the class used as "Mixer" if it has been defined, or null.
	 * @return int The hour of the daily update.
	 */
	public function &getMixerClassName()
    {
		return $this->mixerClassName;
	}

	/**
	 * Get a stream list option by its id/key, or null if no option was found.
     * Will return null if the option was not found.
	 * @return string|null The option value, in form of a string, or null.
	 */
	public function getOption($id)
    {
		if(array_key_exists($id, $this->options)){
            return $this->options[$id];
        }
        return null;
	}

    /**
	 * Get the output file data
	 * @return &FileData
	 */
	public function &getOutputFile()
    {
		return $this->outputFile;
	}

	/**
	 * Get the names of the classes used as "Runner"
	 * @return &String[]
	 */
	public function &getRunnersClassNames()
    {
		return $this->runnersClassNames;
	}

    /**
	 * Get the delay between downloads on same server.
	 * @return int
	 */
    public function getUrlsDelay()
    {
        return $this->urlsDelay;
    }

	/**
	 * Get the name of the class used as "Mixer" if it has been defined, or null.
	 * @return &string|null
	 */
	public function &getValidatorClassName()
    {
		return $this->validatorClassName;
	}

	/**
	 * Get the number of nodes contained in this list
	 * @return int
	 */
	public function size()
    {
		return $this->totalChildren;
	}

    /**
	 * Get a representation of this object in form of a string
	 * @return sring The representation of this object in form of a string
	 */
    public function toString($lvl = 0)
    {

        $lvl = $lvl + 1;
        $lvlstr = "";
        for($i = 0; $i < $lvl; $i++){
            $lvlstr .= Con::TOSTRING_LVL_STR;
        }

        $str = get_class($this) . " (" . $this->totalChildren . ")" . PHP_EOL;
        $i = 0;
        foreach($this->list as $group){
            $str .= $lvlstr . get_class($group) . " [id=\"" . $group->getId() . "\", size=" . $group->size() . "]" . PHP_EOL;
            $i++;
        }
        return $str;
    }

}

