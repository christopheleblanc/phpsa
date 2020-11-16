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
 * TestReport
 * Class intended to store test variables (for "test" mode).
 */

namespace PHPStreamsAggregator\Models;

/**
 * TestReport
 */
class TestReport{

    /**
     * The result of any error occured while loading a streams list file.
     * @var boolean
     */
    private $streamsListResult;

    /**
     * Message describing the error that occurred during loading list file.
     * @var string
     */
    private $streamsListError;

    /**
     * The result of any error occured while loading plugins.
     * @var boolean
     */
    private $pluginsResult;

    /**
     * Message describing the error that occurred during loading plugins.
     * @var string
     */
    private $pluginsError;

    /**
     * Define if the streams list must being updated
     * @var boolean
     */
    private $updateNow;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->streamsListResult = false;
        $this->streamsListError = null;
        $this->pluginsResult = false;
        $this->pluginsError = null;
        $this->updateNow = false;
    }

    /**
     * Set the result of the test while loading a streams list file.
     * @param boolean
     */
    public function setStreamsListResult($bool)
    {
        $this->streamsListResult = $bool;
    }

    /**
     * Set the message describing the error that occurred during loading list file.
     * @param string
     */
    public function setStreamsListError($err)
    {
        $this->streamsListError = $err;
    }

    /**
     * Set the result of the test while loading plugins.
     * @param boolean
     */
    public function setPluginsResult($bool)
    {
        $this->pluginsResult = $bool;
    }

    /**
     * Set the message describing the error that occurred during loading plugins.
     * @param string
     */
    public function setPluginsError($err)
    {
        $this->pluginsError = $err;
    }

    /**
     * Set if the streams list must being updated.
     * @param boolean
     */
    public function setUpdateNow($v)
    {
        $this->updateNow = $v;
    }

    /**
     * Get the result of the test while loading a streams list file.
     * @return boolean
     */
    public function getStreamsListResult()
    {
        return $this->streamsListResult;
    }

    /**
     * Get the message describing the error that occurred during loading streams list file.
     * @param string
     */
    public function getStreamsListError()
    {
        return $this->streamsListError;
    }

    /**
     * Get the result of the test while loading plugins.
     * @return boolean
     */
    public function getPluginsResult()
    {
        return $this->pluginsResult;
    }

    /**
     * Get the message describing the error that occurred during loading plugins.
     * @param string
     */
    public function getPluginsError()
    {
        return $this->pluginsError;
    }

    /**
     * Check if the test has been passed
     * @param boolean
     */
    public function isValidated()
    {
        return ($this->pluginsResult && $this->streamsListResult);
    }

    /**
     * Check if the streams list must being updated
     * @param boolean
     */
    public function getUpdateNow()
    {
        return $this->updateNow;
    }

}