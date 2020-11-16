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
 * AbstractMaker
 * Abstract class used as parent for plugins of type "Maker".
 * All classes which must be included in the program as "File Maker" must inherit
 * from this class.
 */

namespace PHPStreamsAggregator;

use PHPStreamsAggregator\Texts;

/**
 * AbstractMaker
 */
abstract class AbstractMaker{

    /** @var Context The context. */
    protected $context;

    /**
     * @var boolean - Defines if the process is complete.
     *                This variable must be defined as TRUE at the end of the process,
     *                otherwise the program will consider the process as not complete
     *                and will add an error to the user's view and the error log.
     */
    protected $isComplete;

    /**
     * @var string - The error text. Use this variable to return an error to user's view
     *               and error log.
     */
    protected $errorText;

	/**
	 * Constructor
     * @param &Context - The context.
	 */
	public function __construct(&$context)
    {
        $this->isComplete = false;
        $this->errorText = null;
        $this->context = $context;
    }

	/*
	 * Make - Function to be overriden
     * NOTE: This function is intended to be overridden in child class.
     * @param &Context - The context.
     * @param &mixed[] - Array containing all parsed values/objects.
	 */
	public function make(&$context, &$array){}

    /**
	 * Check if the process is complete.
     * NOTE: DO NOT override in child class.
     * @returns boolean - True if the process is complete, or False.
	 */
    public function getIsComplete()
    {
        return $this->isComplete;
    }

    /**
	 * Get the error text.
     * NOTE: DO NOT override in child class.
     * @returns string|null - The error text (if it has been defined), or NULL.
	 */
    public function getErrorText()
    {
        if($this->errorText === null){
            return Texts\errorNoError();
        }
        return $this->errorText;
    }

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