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
 * AbstractMixer
 * Abstract class used as parent for plugins of type "Mixer".
 * All classes which must be included in the program as "Mixer" must inherit from
 * this class.
 */

namespace PHPStreamsAggregator;

/**
 * AbstractMixer
 */
abstract class AbstractMixer{

	/** @var boolean If the parsing process is complete or not. */
	protected $isComplete;

	/** @var string|null Error text (if an error occured). */
	protected $errorText;

    /** @var Context The context. */
    protected $context;

	/**
	 * Constructor
	 */
	public function __construct(&$context)
    {
        $this->isComplete = false;
        $this->errorText = null;
        $this->context = $context;
    }

	/**
	 * Get the error text.
	 * @param string|null
	 */
	public function getErrorText()
    {
		return $this->errorText;
	}

	/**
	 * Check if the process is complete or not.
	 * @param boolean
	 */
	public function &getIsComplete()
    {
		return $this->isComplete;
	}

    /**
     * Mix parsed entities
     * @param &Context            The context
     */
    public function init(&$context){}

    /**
     * Mix parsed entities
     * @param &Context            The context
     * @param Object[][]          An array containing parsed streams
     */
    public function &mix(&$context, &$entities){}

	/**
	 * Defines the process as complete.
	 */
	protected function setIsComplete($v = true)
    {
		$this->isComplete = $v;
	}

    /**
     * Add an alert event / message
     * @param string The key/id of the alert
     * @param Array|null An associative array containing arguments
     */
    protected function addAlert($key, $args = null)
    {
        $this->context->addAlert($key, $args);
    }

}