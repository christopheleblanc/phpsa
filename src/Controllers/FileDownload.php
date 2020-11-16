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
 * FileDownload
 * Class representing a file to download. Instances of
 * this class are automatically created when the program must download a file. 
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Models\StreamTypes;

/**
 * FileDownload
 */
abstract class FileDownload{

	/** @var int The type of the download (0= URL, 1= FTP). */
	protected $type;

	/** @var string The path of the file stored on the server once downloaded. */
	protected $filePath;

	/** @var string The name of the file stored on the server once downloaded. */
	protected $fileName;

	/** @var int The timestamp of the last modification of the file. Collected from "Last-Modified" header. */
	protected $lastModified;

	/** @var boolean Defines if the file was correctly downloaded or not. */
	protected $isComplete;

	/** @var boolean Defines if an error has occurred when downloading. */
	protected $error;

	/** @var string The error message if an error has occurred when downloading. */
	protected $errorMessage;

	/**
	 * Constructor
	 * @param int The type of the download (0= URL, 1=FTP).
	 * @param string The path of the file stored on the server once downloaded.
	 * @param string The name of the file stored on the server once downloaded.
	 */
	public function __construct($type, $filePath, $fileName){
		
		$this->type = $type;
		$this->filePath = $filePath;
		$this->fileName = $fileName;
		$this->lastModified = null;
		$this->isComplete = false;
		$this->error = false;
		$this->errorMessage = "";
		
	}

	/**
	 * Check if an error has occured when downloading.
	 * @return boolean True if an error has occurred when downloading, or False.
	 */
	public function error(){
		return $this->error;
	}

	/**
	 * Get the error message if an error has occured when downloading.
	 * @return string The error message.
	 */
	public function getErrorMessage(){
		return $this->errorMessage;
	}

	/**
	 * Get the type of the download.
	 * @return int The Type.
	 */
	public function getDownloadType(){
		return $this->type;
	}

	/**
	 * Get the path of the file stored on the server once downloaded.
	 * @return string The path of the file on the server.
	 */
	public function getFilePath(){
		return $this->filePath;
	}

	/**
	 * Get the name of the file stored on the server once downloaded.
	 * @return string The name of the file on the server.
	 */
	public function getFileName(){
		return $this->fileName;
	}

	/**
	 * Get the timestamp of the last modification of the file.
	 * @return int The timestamp.
	 */
	public function getLastModified(){
		return $this->lastModified;
	}

	/**
	 * Check if the download is complete or not.
	 * @return boolean True if the download is complete, or False.
	 */
	public function getIsComplete(){
		return $this->isComplete;
	}

	/**
	 * Return a string representation of the object created from this class.
	 * @return boolean True if the download is complete, or False.
	 */
	public function __toString(){

		$typeStr = null;
		switch($this->type){
			case StreamTypes::FTP:{
				$typeStr = "FTP";
			}break;
			case StreamTypes::URL:{
				$typeStr = "URL";
			}break;
			default:{
				$typeStr = "NULL";
			}break;
		}

		$s = get_class($this) . " [ ";
		$s .= "Type: " . $typeStr . ", ";
		$s .= "fileName: " . $this->fileName . "]";
		return $s;
	}

}