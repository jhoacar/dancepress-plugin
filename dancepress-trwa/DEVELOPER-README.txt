1. This code must be kept compatible with servers running PHP 5.6 so as to support
the large number of servers still a long way from being upgraded.

2. Database structural changes should be put in install.sql.php using CREATE statements
ONLY. Any other SQL will not work. On activation, Wordpress will run dbDelta() on
the create statements and bring the structure into line if anything is missing.

3. Any rows of data that need to be added should be put in install-data.sql.php
Make sure that the code is written in such a way as not to repeatedly insert the
same data if the user deactivates/reactivates.
