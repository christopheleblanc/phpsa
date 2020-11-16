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
 * AbstractParser
 * Abstract class used as parent for stream parsers.
 * All classes which must be included in the program as parsers must inherit
 * from this class.
 */

namespace PHPStreamsAggregator;

/**
 * AbstractParser
 */
abstract class AbstractParser{

	/** @var boolean If the parsing process is complete or not. */
	protected $isComplete;

	/** @var boolean If the parsing process is complete or not. */
	protected $errorText;

    /** @var Context The context. */
    protected $context;

	/**
	 * Constructor
     * @param &Context - The context
	 */
	public function __construct(&$context)
    {
		$this->isComplete = false;
        $this->errorText = null;
        $this->context = $context;
	}

	/**
	 * Function used to initialize plugin
	 * @param &Context - The context
	 */
	public function init(&$context){}

	/**
	 * Get the error text
	 * @returns string|null
	 */
	public function getErrorText()
    {
		return $this->errorText;
	}

	/**
	 * Check if the process is complete or not.
	 * @param boolean
	 */
	public function getIsComplete()
    {
		return $this->isComplete;
	}

	/**
	 * Function used to parse the XML file.
	 * @param mixed Depending on the type of parser: The data to parse / The path of the file
	 * @param array A reference to the array in which the objects will be added.
	 */
	public function parse(&$context, &$mixed, &$array){}

	/**
	 * Defines the process as complete.
	 */
	protected function setIsComplete()
    {
		$this->isComplete = true;
	}

}