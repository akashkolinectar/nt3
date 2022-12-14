Alias /_nt3_NAME_ _nt3_DATADIR_/_nt3_NAME_

<Directory _nt3_DATADIR_/_nt3_NAME_>
    Options FollowSymLinks
    DirectoryIndex index.php

    <IfModule mod_authz_core.c>
        Require all granted
    </IfModule>
    <IfModule !mod_authz_core.c>
        Allow from all
    </IfModule>

    <IfModule mod_php5.c>
      AddType application/x-httpd-php .php
      php_flag magic_quotes_gpc Off
      php_flag track_vars On
      php_flag register_globals Off
     </IfModule>
</Directory>

# Disallow web access to directories that don't need it
<Directory _nt3_DATADIR_/_nt3_NAME_/lib>
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Deny from all
    </IfModule>
</Directory>
<Directory _nt3_DATADIR_/_nt3_NAME_/conf>
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Deny from all
    </IfModule>
</Directory>
<Directory _nt3_DATADIR_/_nt3_NAME_/log>
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Deny from all
    </IfModule>
</Directory>
<Directory _nt3_DATADIR_/_nt3_NAME_/data>
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Deny from all
    </IfModule>
</Directory>
