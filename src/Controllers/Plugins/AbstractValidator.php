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
 * AbstractValidator
 * Abstract class used as abstract parent class for plugins of type "Validator".
 * All validators which must be included in the program must inherit from this class.
 */

namespace PHPStreamsAggregator;

use PHPStreamsAggregator\Texts;

/**
 * AbstractValidator
 */
abstract class AbstractValidator{

    /** @var Context The context. */
    protected $context;

	/** @var boolean If the parsing process is complete or not. */
	protected $errorText;

    /**
     * Total number of missing elements.
     * Note: In most cases, you will be taken to use a class Validator to verify
     * that the desired data have been processed or are existing in a file.
     * In addition to returning an error message, you can use this function 
     * to set the total number of missing elements. The number will be stored
     * un state data and may be usefull on some case.
     * @var integer
     */
    protected $totalMissings;

	/**
	 * Constructor
     * @param &Context - The context.
     * @param &String  - The file path.
	 */
	public function __construct(&$context)
    {
        $this->context = $context;
		$this->errorText = null;
        $this->totalMissings = 0;
	}

	/**
	 * Get the error text
     * NOTE: DO NOT override in child class.
	 * @returns string|null
	 */
	public function getErrorText()
    {
        if($this->errorText === null){
            return Texts\errorNoError();
        }
		return $this->errorText;
	}

	/**
	 * Get the number of missing data/entities.
     * NOTE: DO NOT override in child class.
	 * @returns integer
	 */
	public function getTotalMissings()
    {
		return $this->totalMissings;
	}

	/**
	 * Set the number of missing data/entities.
     * NOTE: DO NOT override in child class.
	 * @param integer
	 */
	public function setTotalMissings($v)
    {
		$this->totalMissings = $v;
	}

	/**
	 * Validate - Function to be overriden
	 * @param &Context - The context
	 */
    public function validate(&$context){}

    /**
     * Add an alert event / message
     * NOTE: DO NOT override in child class.
     * @param string The key/id of the alert
     * @param Array|null An associative array containing arguments
     */
    protected function addAlert($key, $args = null)
    {
        $this->context->addAlert($key, $args);
    }

}