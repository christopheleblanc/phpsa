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
 * FileDownloadURL
 * Class representing a file to download from URL. Instance of this class are
 * automatically created when the program must download a file. 
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Models\StreamTypes;
use PHPStreamsAggregator\Controllers\FileDownload;

/**
 * FileDownloadURL
 */
class FileDownloadURL extends FileDownload{

    /** @var string The URL of the file to download. */
    private $url;

    /**
     * Constructor
     * @param string The URL of the download.
     * @param string The path of the file stored on the server once downloaded.
     * @param string The name of the file stored on the server once downloaded.
     */
    public function __construct($url, $filePath, $fileName)
    {
        parent::__construct(StreamTypes::URL, $filePath, $fileName);
        $this->url = $url;
    }

    /**
     * Start downloading this file.
     * @throw Exception If an error has occured when downloading.
     * @return boolean True if an error has occurred when downloading, or False.
     */
    public function start()
    {

        $this->error = true;
        $this->isComplete = false;
        $connected = false;

        /* Prevent SSL failure on HTTPS URLs */
        stream_context_set_default( [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $fileHeaders = @get_headers($this->url, 1);
        if($fileHeaders === false || 
        $fileHeaders["0"] == 'HTTP/1.0 404 Not Found'
        || ($fileHeaders["0"] == 'HTTP/1.0 302 Found' && $fileHeaders["Connection"] == 'HTTP/1.0 404 Not Found')){
            $this->errorMessage = "DOWNLOAD_ERROR_404";
            throw new \Exception($this->errorMessage);
        }
        else{

            $fileDateTime = null;
            try{
                $fileDateTime = @date_create(trim($fileHeaders["Last-Modified"]));
                $this->lastModified = $fileDateTime->getTimestamp();
            }
            catch(\Exception $ex){
                $this->errorMessage = "DOWNLOAD_NO_LAST_MODIFIED";
                throw new \Exception($this->errorMessage);
            }

            $stream = @fopen($this->url, 'r');
            if($stream === false){
                $this->errorMessage = "DOWNLOAD_FAILED";
                throw new \Exception($this->errorMessage);
            }
            else{

                $connected = true;
                $filePut = @file_put_contents($this->filePath, $stream);
                if($filePut === false){
                    $this->errorMessage = "DOWNLOAD_FILE_SAVE_FAILED";
                    throw new \Exception($this->errorMessage);
                }
                else{
                    
                    // Apply chmod 777 to the file (Read, Write, Execute for all)
                    chmod($this->filePath, 0777);
                    
                    $this->error = false;
                    $this->isComplete = true;
                }

            }

        }

        return $this->error;

    }

    /**
     * Get the URL of the file to download.
     * @return string The URL.
     */
    public function getSourceURL()
    {
        return $this->url;
    }

}