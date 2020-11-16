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
 * FileDownloadFTP
 * Class representing a file to download from FTP. Instance of this class are
 * automatically created when the program must download a file. 
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Models\StreamTypes;
use PHPStreamsAggregator\Controllers\FileDownload;

/**
 * FileDownloadFTP
 */
class FileDownloadFTP extends FileDownload{

    /** @var string The server address / IP of the file to download. */
    private $ftpAddress;

    /** @var string The port used to connect on the FTP server. */
    private $ftpPort;

    /** @var string The login/username of the FTP server. */
    private $ftpLogin;

    /** @var string The password used to connect on the FTP server. */
    private $ftpPassword;

    /** @var string|boolean The path of the file to download, or a boolean false. */
    private $ftpFilepath;

    public function __construct($ftpAddress, $ftpPort, $ftpLogin, $ftpPassword, $ftpFilepath, $filePath, $fileName)
    {
        parent::__construct(StreamTypes::FTP, $filePath, $fileName);
        $this->ftpAddress = $ftpAddress;
        $this->ftpPort = $ftpPort;
        $this->ftpLogin = $ftpLogin;
        $this->ftpPassword = $ftpPassword;
        $this->ftpFilepath = $ftpFilepath;
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

        $ftpStream = null;

        if($this->ftpPort !== null){
            $ftpStream = @ftp_connect($this->ftpAddress, $this->ftpPort);
        }
        else{
            $ftpStream = @ftp_connect($this->ftpAddress);
        }

        if($ftpStream === false){
            $this->errorMessage = "FTP_CONNECTION_FAILED";
            throw new \Exception($this->errorMessage);
        }

        $login_result = @ftp_login($ftpStream, $this->ftpLogin, $this->ftpPassword);
        if($login_result === false){
            $this->errorMessage = "FTP_LOGIN_ERROR";
            throw new \Exception($this->errorMessage);
        }

        $ftpFileNameDefined = false;
        $ftpStartingPos = false;
        $hasFileName = false;
        $ftpFileFound = false;
        $ftpFileName = null;

        $ftpFiles = @ftp_mlsd($ftpStream , "."); // ftp_mlsd need PHP >= 7.2.0
        if($ftpFiles === false){
            $this->errorMessage = "FTP_SCAN_ERROR";
            throw new \Exception($this->errorMessage);
        }
        else{

            $ftpFilesCount = count($ftpFiles);

            $searchFileNameAuto = true;// Default

            // If have a path for the file to download
            if($this->ftpFilepath !== false){

                if(is_string($this->ftpFilepath)){
                    $ftpFileFound = true;

                    $searchFileNameAuto = false;
                    $ftpFileNameDefined = true;

                    $slashPos = strpos($ftpFileName, "/");
                    if($slashPos !== false){
                        $path_parts = pathinfo($ftpFileName);
                        $ftpFileName = $path_parts['basename'];
                        $ftpStartingPos = $path_parts['dirname'];
                    }
                    else{
                        $ftpFileName = $this->ftpFilepath;
                    }

                }

            }

            // Or, scan the dir to get automatically a file.
            if($searchFileNameAuto){

                if($ftpFilesCount == 1){
                    $ftpFileFound = true;
                    $ftpFileName = ($ftpFiles[0])["name"];
                }
                else{

                    //Get the name of the last modified file of the directory

                    $lastTimestamp = 0;
                    $lastModifiedFileIndex = 0;

                    for($i = 0; $i < $ftpFilesCount; $i++){
                        $ftpFile = $ftpFiles[$i];
                        if($ftpFile["name"] != "." && $ftpFile["name"] != ".."){

                            $ftpFileModifTime = intval($ftpFile["modify"]);

                            if($ftpFileModifTime > $lastTimestamp){
                                $lastTimestamp = $ftpFileModifTime;
                                $lastModifiedFileIndex = $i;
                            }

                        }

                    }

                    $ftpFileFound = true;
                    $ftpFileName = ($ftpFiles[$lastModifiedFileIndex])["name"];

                }

            }

        }

        if(!$ftpFileFound){
            @ftp_close($ftpStream);
            $this->errorMessage = "FTP_SCAN_FILE_NOT_FOUND";
            throw new \Exception($this->errorMessage);
        }
        else{
            $hasFileName = true;
        }

        if($hasFileName){

            $startPos = null;
            if($ftpFileNameDefined && $ftpStartingPos !== false){
                $startPos = $ftpStartingPos;
            }

            $get = @ftp_get($ftpStream, $this->filePath, $ftpFileName, FTP_BINARY, $startPos);

            if($get === false){
                $lastError = error_get_last();
                if($lastError["message"] == "ftp_get(): File not found"){
                    $this->errorMessage = "FILE_NOT_FOUND";
                }
                else{
                    $this->errorMessage = "DOWNLOAD_FAILED";
                }
                @ftp_close($ftpStream);
                throw new \Exception($this->errorMessage);
            }
            else{
                $this->error = false;
                $this->isComplete = true;
                @ftp_close($ftpStream);

                // Apply chmod 777 to the file (Read, Write, Execute for all)
                chmod($this->filePath, 0777);

            }

        }
        else{
            $this->errorMessage = "FILE_NO_NAME";
            throw new \Exception($this->errorMessage);
        }

        return $this->error;

    }

    /**
     * Get the address of the FTP server of the download.
     * @return string The address of the FTP server.
     */
    public function getFtpAddress()
    {
        return $this->ftpAddress;
    }

    /**
     * Get the port of the FTP server of the download.
     * @return string The port of the FTP server.
     */
    public function getFtpPort()
    {
        return $this->ftpPort;
    }

    /**
     * Get the login to access the FTP server of the download.
     * @return string The login to access the FTP server.
     */
    public function getFtpLogin()
    {
        return $this->ftpLogin;
    }

    /**
     * Get the password to access the FTP server of the download.
     * @return string The password to access the FTP server.
     */
    public function getFtpPassword()
    {
        return $this->ftpPassword;
    }

    /**
     * Get the path of the file on the server side, or a boolean false.
     * @return string|boolean The path, or boolean false.
     */
    public function getFtpFilepath()
    {
        return $this->ftpFilepath;
    }

}