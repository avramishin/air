------------------------------------------------------------
This utility generates classes out of DB structure.
Generated classes are placed under

ROOT/app/models/dbname/tables/* - table definitions
ROOT/app/models/dbname/* - Entities

Make sure that you've configured DB connection properties in 
ROOT/app/conf/config.local.json file.

run: php generate.php

Have fun!
------------------------------------------------------------