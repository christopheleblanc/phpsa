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
 * AstractProcess
 * Class used as parent for several processing classes.
 */

namespace PHPStreamsAggregator\Controllers;

/**
 * AstractProcess
 */
class AstractProcess{

    /**
     * The result of any error occured while loading a streams list file.
     * @var boolean
     */
    protected $isComplete;

    /**
     * The error text, if it has been defined, or null.
     * @var string|null
     */
    protected $errorText;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isComplete = false;
        $this->errorText = null;
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
	 * Defines the process as complete or not.
     * @param boolean - If the process is complete or not.
	 */
	protected function setIsComplete($v = true)
    {
		$this->isComplete = $v;
	}

}