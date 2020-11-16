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
 * StreamFTP
 * Class representing a stream downloadable by FTP in a StreamList.
 */

namespace PHPStreamsAggregator\Models;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Models\StreamTypes;
use PHPStreamsAggregator\Models\Stream;

/**
 * StreamFTP
 */
class StreamFTP extends Stream{

	/** @var string The address of the FTP server. */
	private $ftpAddress;

	/** @var string The port of the FTP server. */
	private $ftpPort;

	/** @var string The login to access the FTP server. */
	private $ftpLogin;

	/** @var string The password to access the FTP server. */
	private $ftpPassword;

	/** @var string|boolean The path of the file to download, on the server side, or a boolean false. */
	private $ftpFilePath;

	/**
	 * Constructor
	 * @param string The full name of the download.
	 * @param string The ID/name of the download.
	 * @param string The address of the FTP server.
	 * @param string The port of the FTP server.
	 * @param string The login to access the FTP server.
	 * @param string The password to access the FTP server.
	 * @param string|boolean The path of the file on server side, or a boolean false.
	 * @param boolean Whether the download is enabled or not.
	 * @param string The name of the parser for this download.
	 */
	public function __construct($name, $id, $ftpAddress, $ftpPort, $ftpLogin, $ftpPassword, $ftpFilepath, $active, $parserName){

		parent::__construct(StreamTypes::FTP, $name, $id, $active, $parserName);
		$this->ftpAddress = $ftpAddress;
		$this->ftpPort = $ftpPort;
		$this->ftpLogin = $ftpLogin;
		$this->ftpPassword = $ftpPassword;
		$this->ftpFilepath = $ftpFilepath;
	}

	/**
	 * Get the address of the FTP server of the download.
	 * @return string
	 */
	public function getFtpAddress()
    {
		return $this->ftpAddress;
	}

	/**
	 * Get the port of the FTP server of the download.
	 * @return string
	 */
	public function getFtpPort()
    {
		return $this->ftpPort;
	}

	/**
	 * Get the login to access the FTP server of the download.
	 * @return string
	 */
	public function getFtpLogin()
    {
		return $this->ftpLogin;
	}

	/**
	 * Get the password to access the FTP server of the download.
	 * @return string
	 */
	public function getFtpPassword(){
		return $this->ftpPassword;
	}

	/**
	 * Get the path of the file to download on the server, or a boolean false.
	 * @return string
	 */
	public function getFtpFilepath()
    {
		return $this->ftpFilepath;
	}

    /**
	 * Get a representation of this object in form of a string
	 * @return string
	 */
    public function toString($lvl = 0)
    {

        $lvl = $lvl + 1;
        $lvlstr = "";
        for($i = 0; $i < $lvl; $i++){
            $lvlstr .= Con::TOSTRING_LVL_STR;
        }

        return $lvlstr . get_class($this) . " [id=\"" . $this->getId() . "\"]";

    }

}
