#!/bin/bash
# Linux installation script to be used inside packages (deb, rmp)
# or launched manually with the appropriate variables set...
#
# $Id$
#
#set -v

if [ "_$_nt3_SYSCONFDIR_" = "_" ]; then
	_nt3_SYSCONFDIR_="/etc"
fi
if [ "_$_nt3_VARDIR_" = "_" ]; then
	_nt3_VARDIR_="/var"
fi
if [ "_$_nt3_NAME_" = "_" ]; then
	_nt3_NAME_="nt3-itsm"
fi

if [ "_$PREFIX" != "_" ]; then
	local=${HEAD}$PREFIX
	sublocal=$PREFIX
	conf=${HEAD}$_nt3_SYSCONFDIR_/$_nt3_NAME_
	subconf=$_nt3_SYSCONFDIR_/$_nt3_NAME_
	var=${HEAD}$_nt3_VARDIR_
	subvar=$_nt3_VARDIR_
	webconf=${HEAD}$_nt3_WEBCONFDIR_
	subwebconf=$_nt3_WEBCONFDIR_
else
	local=/usr/local
	sublocal=$local
	conf=$local/$_nt3_SYSCONFDIR_
	subconf=$conf
	var=$local/$_nt3_VARDIR_
	subvar=$var
	webconf=$local/$_nt3_WEBCONFDIR_ 
	subwebconf=$_nt3_WEBCONFDIR_
fi

if [ "_$_nt3_WEBCONFDIR_" = "_" ]; then
	_nt3_WEBCONFDIR_="$conf/../httpd"
	if [ ! -d $_nt3_WEBCONFDIR_ ]; then
		exit "Please define a valid _nt3_WEBCONFDIR_ variable"
	fi
fi

# Define additional dirs
if [ _"$_nt3_LOGDIR_" = _"" ]; then
        _nt3_LOGDIR_="$var/log/$_nt3_NAME_"
else
        _nt3_LOGDIR_="${HEAD}$_nt3_LOGDIR_"
fi

if [ _"$_nt3_VARLIBDIR_" = _"" ]; then
        _nt3_VARLIBDIR_="$var/lib/$_nt3_NAME_"
else
        _nt3_VARLIBDIR_="${HEAD}$_nt3_VARLIBDIR_"
fi

if [ _"$_nt3_DATADIR_" = _"" ]; then
        _nt3_DATADIR_="$local/share/$_nt3_NAME_"
else
        _nt3_DATADIR_="${HEAD}$_nt3_DATADIR_"
fi

# From now on Variables are correctly setup, just use them
#
echo "$_nt3_NAME_ will be installed under $_nt3_DATADIR_"

echo "Creating target directories ..."
for d in production test toolkit; do
	install -m 755 -d $conf/$d $_nt3_VARLIBDIR_/env-$d 
done
install -m 755 -d $_nt3_DATADIR_ $_nt3_LOGDIR_ "$_nt3_VARLIBDIR_/data"

echo "Copying files ..."
cp -a ./web/* $_nt3_DATADIR_

echo "Fixing line endings in LICENSE and README files"
sed -i -e "s/\r$//g" ./LICENSE ./README

echo "Creating symlinks..."
(cd $_nt3_DATADIR_ ; \
ln -s $subconf conf ;\
ln -s $subvar/log/$_nt3_NAME_ log ;\
ln -s $subvar/lib/$_nt3_NAME_/env-production env-production ;\
ln -s $subvar/lib/$_nt3_NAME_/env-test env-test ;\
ln -s $subvar/lib/$_nt3_NAME_/data data ;\
)
(cd  $_nt3_VARLIBDIR_ ; ln -s $sublocal/share/$_nt3_NAME_/approot.inc.php approot.inc.php)


if [ _"$HEAD" != _"" ]; then
	echo Creating $webconf/conf.d, $conf/../cron.d and $conf/../logrotate.d directories
	install -m 755 -d $webconf/conf.d $conf/../cron.d $conf/../logrotate.d
fi

# Substitute variables for templates
sed -e "s~_nt3_NAME_~$_nt3_NAME_~g" -e "s~_nt3_SYSCONFDIR_~$subconf~g" -e "s~_nt3_DATADIR_~$sublocal/share~g" -e "s~_nt3_LOGDIR_~$subvar/log~g" ./web/setup/install/apache.conf.tpl > $webconf/conf.d/$_nt3_NAME_.conf
sed -e "s~_nt3_NAME_~$_nt3_NAME_~g" -e "s~_nt3_SYSCONFDIR_~$subconf~g" -e "s~_nt3_DATADIR_~$sublocal/share~g" -e "s~_nt3_LOGDIR_~$subvar/log~g" ./web/setup/install/cron.tpl > $conf/../cron.d/$_nt3_NAME_
sed -e "s~_nt3_NAME_~$_nt3_NAME_~g" -e "s~_nt3_SYSCONFDIR_~$subconf~g" -e "s~_nt3_DATADIR_~$sublocal/share~g" -e "s~_nt3_LOGDIR_~$subvar/log~g" ./web/setup/install/logrotate.tpl > $conf/../logrotate.d/$_nt3_NAME_
chmod 644 $webconf/conf.d/$_nt3_NAME_.conf $conf/../cron.d/$_nt3_NAME_ $conf/../logrotate.d/$_nt3_NAME_

exit 0
