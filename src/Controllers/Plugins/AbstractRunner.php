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
 * AbstractRunner
 * Abstract class used as parent for plugins of type "Runner".
 * All classes which must be included in the program as "Runner" must inherit
 * from this class.
 */

namespace PHPStreamsAggregator;

use PHPStreamsAggregator\Texts;

/**
 * AbstractRunner
 */
abstract class AbstractRunner{

    /**
     * @var string - The error text. Use this variable to return an error to user's view
     *               and error log.
     */
    protected $context;

	/**
	 * Constructor
	 */
	public function __construct(&$context)
    {
        $this->context = $context;
    }

}