PHP Streams Aggregator v1.0.0 - Demonstration program 1


INSTRUCTIONS:

1 - Define the path of the demo streams in the stream list file "data/config/demo1.xml" by
    changing the value "[path_to]" by the real path.

2 - Copy the content of the directories "config" and "plugins" in the program directory.

data/config/demo1.xml                  >  [program directory]/data/config/demo1.xml
plugins/makers/DemoMoviesMaker.php     >  [program directory]/plugins/makers/DemoMoviesMaker.php
plugins/mixers/DemoMoviesMixer         >  [program directory]/plugins/mixers/DemoMoviesMixer
plugins/parsers/DemoMoviesParser1.php  >  [program directory]/plugins/parsers/DemoMoviesParser1.php
plugins/parsers/DemoMoviesParser2.php  >  [program directory]/plugins/parsers/DemoMoviesParser2.php
plugins/parsers/DemoMoviesParser3.php  >  [program directory]/plugins/parsers/DemoMoviesParser3.php
plugins/parsers/DemoMoviesParser4.php  >  [program directory]/plugins/parsers/DemoMoviesParser4.php


3 - Open a terminal/command prompt in the program directory and run the following command:

php [path_to_program_directory]run.php