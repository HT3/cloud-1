Prerequisites

    Create a project in the Cloud Console.
    Enable billing for your project.
    Install the Cloud SDK.
    Enable the Cloud SQL API.
    Install Composer.

Create and configure a Cloud SQL for MySQL instance

Note: In this guide, we use wordpress for the instance name and the database name. We use root for the database user name.

    Create a new Cloud SQL for MySQL Second Generation instance with the following command:

    $ gcloud sql instances create wordpress \
        --activation-policy=ALWAYS \
        --tier=db-n1-standard-1 \
        --region=us-central1

Note: you can choose db-f1-micro or db-g1-small instead of db-n1-standard-1 for the Cloud SQL machine type, especially for development or testing purposes. However, those machine types are not recommended for production use and are not eligible for Cloud SQL SLA coverage. See the Cloud SQL SLA for more details.

Create the database you want your WordPress site to use:

$ gcloud sql databases create wordpress --instance wordpress

Change the root password for your instance:

$ gcloud sql users set-password root \
    --host=% \
    --instance wordpress \
    --password=YOUR_INSTANCE_ROOT_PASSWORD # Don't use this password!

Create or update a WordPress project for App Engine

The wp-gae command provides a convenient way for you to either create a new WordPress project or add the required configuration to an existing one.
Setup

    Download the google/cloud-tools package:

    $ composer require google/cloud-tools

Note: If you receive an error about extensions, install phar and zip PHP extensions and retry:

$ sudo apt-get install php7.2-zip php7.2-curl

Now you can run the wp-gae command which is included in that package:

$ php vendor/bin/wp-gae

    Note: You can also install google/cloud-tools globally, which will allow you to execute the command wp-gae anywhere.

The wp-gae command will ask you several question in order to set up your Cloud SQL database connection, and then write the required configuration to your wp-config.php configuration file. It also copies the following files into your project directory to allow WordPress to run on App Engine:

    app.yaml: The App Engine configuration file that specifies the runtime and static asset handlers.
    cron.yaml: The App Engine configuration file that ensures wp-cron.php is run every 15 minutes.
    php.ini: For setting PHP configuration in App Engine specific to WordPress.
    gae-app.php: The Front Controller, which is required for all App Engine applications.

Create a new WordPress project

To download WordPress and set it up for GCP, run the create command:

$ php vendor/bin/wp-gae create

The command asks you several questions. After you answer them, you'll have a new WordPress project. By default, it will create my-wordpress-project in the current directory.

Note: To determine the region your database is in, use the gcloud sql instances describe wordpress command.

$ gcloud sql instances describe wordpress | grep region

Update an existing WordPress project

If you are migrating an existing project to Google Cloud, you can use the update command:

$ php vendor/bin/wp-gae update path/to/your-wordpress-site

The command asks you several questions. After you answer them, your existing project will contain the required files for deploying to App Engine, as well as an updated wp-config.php file. As this command overwrites wp-config.php, be sure to verify the changes are correct before deploying.
Deploy to Google Cloud

cd into the root of your WordPress project:

$ cd my-wordpress-project

Run the following command to deploy your project to App Engine:

$ gcloud app deploy app.yaml cron.yaml

Now you can access your site, and continue the installation step! The URL is https://YOUR_PROJECT_ID.appspot.com/.

NOTE: If you receive any error in your application, such as "Error estabilishing a database connection", set the WP_DEBUG constant to true in wp-config.php and redeploy:

define('WP_DEBUG', true);

Enable the Google Cloud Storage plugin

To use the Google Cloud Storage plugin for media uploads, follow these steps:

    Configure the App Engine default Cloud Storage bucket for later use. The default App Engine bucket is named YOUR_PROJECT_ID.appspot.com. Change the default Access Control List (ACL) of that bucket as follows:

    $ gsutil defacl ch -u AllUsers:R gs://YOUR_PROJECT_ID.appspot.com

    Go to the Dashboard at https://YOUR_PROJECT_ID.appspot.com/wp-admin. On the Plugins page, activate the Google Cloud Storage plugin.

    In the plugins Settings page, set your Bucket name to the bucket you configured in Step 1.

After activating the plugin, try uploading a media object in a new post and confirm the image is uploaded to the Cloud Storage bucket by visiting the Cloud Console Storage page.
Local development

To access this MySQL instance, use Cloud SQL Proxy. Download it to your local computer and make it executable.

To install in Cloud Shell:

$ wget https://dl.google.com/cloudsql/cloud_sql_proxy.linux.amd64 -O cloud_sql_proxy
$ chmod +x cloud_sql_proxy

Go to the the Credentials section of your project in the Console. Click Create credentials and then click Service account key. For the Service account, select App Engine app default service account. Then click Create to create and download the JSON service account key to your local machine. Save it to a safe place.

Run the proxy by the following command:

$ ./cloud_sql_proxy \
    -instances=YOUR_PROJECT_ID:us-central1:wordpress=tcp:3306 \
    -credential_file=/path/to/YOUR_SERVICE_ACCOUNT_JSON_FILE.json &

If running within Cloud Shell:

$ ./cloud_sql_proxy -instances <YOUR_PROJECT_ID>:us-central1:wordpress=tcp:3306 &

Note: See Connecting to Cloud SQL from External Applications for more options when running the Cloud SQL proxy.

Now you can access the Cloud SQL instance with the MySQL client in a separate command-line tab.

$ mysql --host=127.0.0.1 -u root -p
mysql> use database wordpress;
mysql> show tables;
mysql> exit

Various workflows
Install and update WordPress, plugins, and themes

Because the wp-content directory on the server is read-only, you have to perform all code updates locally. Run WordPress locally and update the plugins and themes in the local Dashboard, deploy the code to production, then activate them in the production Dashboard. You can also use the wp-cli utility as follows. Be sure to keep the Cloud SQL proxy running.

# Install the wp-cli utility
$ composer require wp-cli/wp-cli-bundle
# Now you can run the "wp" command to update Wordpress itself
$ vendor/bin/wp core update --path=wordpress
# You can also update all the plugins and themes
$ vendor/bin/wp plugin update --all
$ vendor/bin/wp theme update --all

The following error may occur:

Failed opening required 'google/appengine/api/urlfetch_service_pb.php'

If you get this error, you can set a WP_CLI_PHP_ARGS environment variable to add include_path PHP configuration for wp-cli:

$ export WP_CLI_PHP_ARGS='-d include_path=vendor/google/appengine-php-sdk'

Then try the update commands again.

After everything is up to date, deploy the app again:

$ gcloud app deploy app.yaml cron.yaml

Note: This will deploy a new version of the app and set it as the default while keeping the previous versions available under versioned hostnames. Visit the App Engine, Versions area to see previous versions.

Alternately, you may deploy the new version and stop previous ones so they stop incurring charges:

$ gcloud app deploy app.yaml cron.yaml --promote --stop-previous-versions