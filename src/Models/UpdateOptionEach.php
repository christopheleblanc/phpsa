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
 * UpdateOptionEach
 * Class representing an update option of type "Each".
 * This type of update options means that an update must being processed each
 * time the program is launched.
 *
 * The update option is obtained by parsing the configuration files.
 *
 * Example of correctly formatted option in configuration files:
 *  --------------------------------------------------------------------
 * | OPTION           | TRANSLATION                                     |
 *  --------------------------------------------------------------------
 * | "each"           | Update each time                                |
 * | "e"              | Update each time                                |
 *  --------------------------------------------------------------------
 */

namespace PHPStreamsAggregator\Models;

use PHPStreamsAggregator\Models\UpdateOption;
use PHPStreamsAggregator\Models\UpdateOptionTypes;

/**
 * UpdateOptionEach
 */
class UpdateOptionEach extends UpdateOption{

	/**
	* Constructor
	*/
	function __construct()
    {
		parent::__construct(UpdateOptionTypes::EACH);
	}

}