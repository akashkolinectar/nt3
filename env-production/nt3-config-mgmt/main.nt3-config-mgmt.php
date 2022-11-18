<?php


// Starting with NT3 1.2 you can restrict the list of organizations displayed in the drop-down list
// by specifying a query as shown below. Note that this is NOT a security settings, since the
// choice 'All Organizations' will always be available in the menu
ApplicationMenu::SetFavoriteSiloQuery('SELECT Organization');

?>
