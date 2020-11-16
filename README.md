# PHP Streams Aggregator
PHP Streams Aggregator is a program written in PHP intended to parse multiple streams, keep them up to date, process/mix the results and send them to a database or store them into a single output file.

Designed to keep data up to date at regular intervals (daily, at a particular hour, every X minutes etc ...), it is particularly useful for collecting or keeping up to date important data for your application, server or website:
* Currencies exchange rates from multiple api
* Available products from multiple suppliers
* Live news from different sources
* etc...

Regularly started by a "cron" or "scheduled" task, it will make sure your streams/feeds are up to date, will be updated whenever necessary, and will notify you of any problems by storing various messages in its log files.

Easy to run, just create your streams list (in XML) and code your own parsing/exporting functions in PHP to start.

With some basic knowledge, you will easily be able to keep your sources up to date, parse and export many types of files (XML, JSON, CSV, and many other types).

**Read Documentation.html for more informations...**

# Installation
PHP Streams Aggregator does not require any specific installation. Just place the application folder in the location of your choice.

**From a compressed archive:** Extract the contents of the archive to the location of your choice.

**From sources:** Download/copy the program directory to the location of your choice.

Depending on your installation method, you may need to create the following directories used to store config, logs and temp files:

> /data /data/config /data/logs /data/output /data/tmp /data/tmp/files /data/tmp/out /data/tmp/state

*Note: Temporary files directory can be modified in configuration file.

**Requires PHP 7.0 and higher**

# Configuration
The use cases, the types of streams/feeds, the entities to be parsed and exported can be very varied, each process will require specific plugins and configuration.

To configure a process, you may use two different XML files.

The first file is the configuration file, which can contains various options relating to the entire program, regardless of the streams/feeds to be processed.

The second file, called "streams list", is most important. It is used to define all the streams that must be loaded (and parsed), as well as the options specific to the process or to the streams. The id/name of each stream, the method used to download it (URL, FTP...), the plugin used to parse it, the update options etc....

To facilitate the execution of the program, the streams list file can be defined directly in the configuration file by defining the option "list".

Once the streams list file is created, you will be able to test it using the command parameter "test" ("-t" or "--test"). Of course, you can also start the update process directly. Any errors found in the file will be displayed and the process will be terminated.

# Plugins
Unfortunately, there is no magic in the way the program works. Each stream can have a different structure, the data to be parsed can be of various types, the output file can be totally different depending on your use, so you will have to develop your own plugins and integrate them into the program.

Don't panic! This concept has been simplified as much as possible, and if the streams processed are simple, your plugins will only represent a few lines of code. Either way, if you've already developed a little piece of code to parse a stream, add entities to an array or export a file, it will be a snap!

**In its current version, the program take five types of plugins:**

**Parser:** A type of plugins intended to parse a stream.

**Mixer:** A type of plugins intended to "mix" or aggregate all parsed entities from all streams. This type of plugin is optional but can be useful for many other things.

**Maker:** A type of plugins intended to finalize the process with parsed data. It can be used to store parsed data into an output file or in a database, send data by email, pass them to an other program...

**Validator:** A type of plugins intended to validate the exported file. This type of plugins is optional but can be useful to validate your output file and doing some stuff if you got errors.

**Runner:** A type of plugins which has the ability to reacts to event during all the process. Can be used to listen errors etc...

# Run the program
As a simple PHP command line program, you can use any terminal/command prompt and execute the following commands from the program directory:
```
php run.php [optional arguments]
```

# Demonstration
A demonstration is available with the program. Its purpose is to demonstrate a basic use case.

# More informations
Please read Documentation.html
