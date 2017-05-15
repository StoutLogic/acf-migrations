#!/bin/bash

DEBUG=1

# defaults
WP_VERSION="latest"
WP_DIR="${PWD}/tmp/wordpress"
WP_MULTISITE=0
WP_MUSUBDOMAINS=0
WP_DBNAME="acf-migrations"
WP_DBUSER="root"
WP_DBPASS=""
WP_DBHOST="localhost"
WP_DBPREFIX="wp_"
WP_DOMAIN="localhost"
WP_PORT="9000"
WP_MUBASE="/"
WP_TITLE="Test"
WP_ADMIN_USER="admin"
WP_ADMIN_PASS="admin"
WP_ADMIN_EMAIL="admin@$WP_DOMAIN"
EMPTY=0
WP_THEME="twentysixteen"
ROOT=$PWD

PARSED_OPTIONS=$(getopt -n "$0"  -o 'me' --long "dir::,version::,multisite,subdomains,empty,dbname::,dbuser::,dbpass::,dbhost::,dbprefix::,domain::,port::,url::,title::,base::,admin_user::,admin_password::,admin_email::,theme::"  -- "$@")

#Bad arguments, something has gone wrong with the getopt command.
if [ $? -ne 0 ];
then
  exit 1
fi

eval set -- "$PARSED_OPTIONS"

# extract options and their arguments into variables.
while true ; do
    case "$1" in
        --dir ) WP_DIR="$2"; shift 2;;
        --version ) WP_VERSION="$2"; shift 2;;
        -m|--multisite ) WP_MULTISITE=1; shift;;
        -s|--subdomains ) WP_MUSUBDOMAINS=1; shift;;
        -e|--empty ) EMPTY=1; shift;;
        --dbname ) WP_DBNAME="$2"; shift 2;;
        --dbuser ) WP_DBUSER="$2"; shift 2;;
        --dbpass ) WP_DBPASS="$2"; shift 2;;
        --dbhost ) WP_DBHOST="$2"; shift 2;;
        --dbprefix ) WP_DBPREFIX="$2"; shift 2;;
        --domain ) WP_DOMAIN="$2"; shift 2;;
        --port ) WP_PORT=":$2"; shift 2;;
        --url ) WP_URL="$2"; shift 2;;
        --title ) WP_TITLE="$2"; shift 2;;
        --base ) WP_MUBASE="$2"; shift 2;;
        --admin_user ) WP_ADMIN_USER="$2"; shift 2;;
        --admin_password ) WP_ADMIN_PASS="$2"; shift 2;;
        --admin_email ) WP_ADMIN_EMAIL="$2"; shift 2;;
        --theme ) WP_THEME="$2"; shift 2;;
        --) shift; break;;
    esac
done

if [[ ! $WP_URL ]]; then
    [[ $WP_PORT == "80" ]] && WP_URL="$WP_DOMAIN" || WP_URL="$WP_DOMAIN:$WP_PORT"
fi

[[ $WP_ADMIN_EMAIL =~ .*@.*\..* ]] && WP_ADMIN_EMAIL=$WP_ADMIN_EMAIL || WP_ADMIN_EMAIL="$WP_ADMIN_EMAIL.com"

if [[ $DEBUG == 1 ]]; then
    echo "WordPress will be installed in $WP_DIR (WP_DIR)"
    if [[ $WP_MULTISITE == 1 ]]; then
        if [[ $WP_MUSUBDOMAINS == 1 ]]; then
            echo "Multisite is enabled with sub-domains, base path is $WP_MUBASE"
        elif [[ $WP_MUSUBDOMAINS == 0 ]]; then
            echo "Multisite is enabled with sub-folders, base path is $WP_MUBASE"
        fi
    fi
    echo "Database name is $WP_DBNAME"
    echo "Database user is $WP_DBUSER"
    echo "Database password is $WP_DBPASS"
    echo "Database host is $WP_DBHOST"
    echo "WordPress database prefix is $WP_DBPREFIX"
    echo "WordPress domain is $WP_DOMAIN"
    echo "WordPress port is $WP_PORT"
    echo "WordPress url is $WP_URL"
    echo "WordPress title is $WP_TITLE"
    echo "WordPress admin user is $WP_ADMIN_USER, with password $WP_ADMIN_PASS and email $WP_ADMIN_EMAIL"
    echo "Active theme will is $WP_THEME"
    if [[ $EMPTY == 1 ]]; then
        echo "WordPress Installation will be emptied"
    fi
fi

BREATH="\n\n"
SEP="================================================================================\n"


# create the folder that will store the WordPress installation
printf $BREATH
echo "Creating folder $WP_DIR"
mkdir -p $WP_DIR


printf $BREATH
echo "Installing wp-cli"
printf $SEP
# install wp-cli and make it available in PATH
composer global require wp-cli/wp-cli --no-interaction
export PATH="$HOME/.composer/vendor/bin:$PATH"

printf $BREATH
echo "Downloading WordPress"
printf $SEP
# download wordpress
cd $WP_DIR && wp core download --version=$WP_VERSION

printf $BREATH
if [[ $WP_MULTISITE == 1 ]]; then
    echo "Configuring WordPress for multisite installation"
    wp core config --dbname=$WP_DBNAME --dbuser=$WP_DBUSER --dbpass=$WP_DBPASS --dbhost=$WP_DBHOST --dbprefix=$WP_DBPREFIX --skip-salts

    if [[ $WP_MUSUBDOMAINS == 1 ]]; then
        wp core multisite-install --url=$WP_URL --base=$WP_MUBASE --title=$WP_TITLE --admin_user=$WP_ADMIN_USER --admin_password=$WP_ADMIN_PASS --admin_email=$WP_ADMIN_EMAIL --subdomains --skip-email
    elif [[ $WP_MUSUBDOMAINS == 0 ]]; then
        wp core multisite-install --url=$WP_URL --base=$WP_MUBASE --title=$WP_TITLE --admin_user=$WP_ADMIN_USER --admin_password=$WP_ADMIN_PASS --admin_email=$WP_ADMIN_EMAIL --skip-email
    fi

elif [[ $WP_MULTISITE == 0 ]]; then
    echo "Configuring WordPress for single site installation"
    wp core config --dbname=$WP_DBNAME --dbuser=$WP_DBUSER --dbpass=$WP_DBPASS --dbhost=$WP_DBHOST --dbprefix=$WP_DBPREFIX --skip-salts
    wp core install --url=$WP_URL --title=$WP_TITLE --admin_user=$WP_ADMIN_USER --admin_password=$WP_ADMIN_PASS --admin_email=$WP_ADMIN_EMAIL --skip-email
fi

printf $SEP

if [[ $EMPTY == 1 ]]; then
    printf $BREATH
    echo "Emptying WordPress installation"
    printf $SEP
    wp site empty --yes
    wp plugin delete $(wp plugin list --field=name)
    wp theme activate $WP_THEME && wp theme delete $(wp theme list --field=name --status=inactive)
fi

printf $BREATH
echo "Editing integration.suite.yml"
sed -i '' "s=wpRootFolder:\(.*\)=wpRootFolder: $WP_DIR=" $ROOT/tests/integration.suite.yml

printf $SEP

wp core version
