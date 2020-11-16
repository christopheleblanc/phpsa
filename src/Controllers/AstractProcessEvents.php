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
 * AstractProcessEvents
 * Class inherited from AstractProcess used as parent for several processing
 * classes. Especially used to process plugins/classes and report events from them.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Controllers\AstractProcess;

/**
 * AstractProcessEvents
 */
class AstractProcessEvents extends AstractProcess{

    /** @var Object[] Events */
    private $alerts;

    /** @var Object[] Alerts */
    private $events;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alerts = [];
        $this->events = [];
        parent::__construct();
    }

    /**
     * Get the alerts
     * @returns &Object[]
     */
    public function &getAlerts()
    {
        return $this->alerts;
    }

    /**
     * Get the events
     * @returns &Object[]
     */
    public function &getEvents()
    {
        return $this->events;
    }

}