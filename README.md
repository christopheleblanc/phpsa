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
"PHP Streams Aggregator" does not require any special installation. Just place the application folder in the location of your choice.

**From a compressed archive:** Extract the contents of the archive to the location of your choice.

**From sources:** Download/copy the program directory to the location of your choice.

# Demonstration
A demonstration is available with the program. Its purpose is to demonstrate a basic use case.

# More informations
Please read Documentation.html
