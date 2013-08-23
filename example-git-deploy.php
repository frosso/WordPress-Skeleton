<?php

date_default_timezone_set( 'Europe/Rome' );

if ( isset( $_GET['out'] ) ) {
    header( "Content-Type: text/plain" );
}

/**
 * SITO è l'utente proprietario del sito (es.: arredofamily2.remedia.me => arredofamily2)
 * per ottenere SITO, spostarsi in /var/www/vhosts/sito.dominio/ e digitare il comando
 *       user=$(ls -l | grep httpdocs | awk '{print $3}')
 *       sito=$(basename `pwd`)
 *       repo="git@bitbucket.org:design_remedia/REPOSITORY.git"
 mkdir /var/www/vhosts/$sito/.ssh
 chown -R $user:psacln /var/www/vhosts/$sito/.ssh
 su -m $user -c "ssh-keygen -q -t rsa -f /var/www/vhosts/$sito/.ssh/id_rsa -P ''" # choose "no passphrase"
 cat /var/www/vhosts/$sito/.ssh/id_rsa.pub # aggiungere al repository su bitbucket con designers@remedia.it
 cd /var/www/vhosts/$sito/httpdocs/repository_path
 git init .
 git remote add origin $repo
 chown -R $user:psacln .
 su -m $user -c "git pull -u origin master"

 */

class Deploy {

    /**
     * A callback function to call after the deploy has finished.
     *
     * @var callback
     */
    public $post_deploy;

    /**
     * The name of the file that will be used for logging deployments. Set to
     * FALSE to disable logging.
     *
     * @var string
     */
    private $_log = 'deployments.log';

    /**
     * The timestamp format used for logging.
     *
     * @link    http://www.php.net/manual/en/function.date.php
     * @var     string
     */
    private $_date_format = 'Y-m-d H:i:sP';

    /**
     * The name of the branch to pull from.
     *
     * @var string
     */
    private $_branch = 'master';

    /**
     * The name of the remote to pull from.
     *
     * @var string
     */
    private $_remote = 'origin';

    /**
     * The directory where your website and git repository are located, can be
     * a relative or absolute path
     *
     * @var string
     */
    private $_directory;

    /**
     * Sets up defaults.
     *
     * @param  string  $directory  Directory where your website is located
     * @param  array   $data       Information about the deployment
     */
    public function __construct( $directory, $options = array() ) {
        // Determine the directory path
        $this->_directory = realpath( $directory ) . DIRECTORY_SEPARATOR;

        $available_options = array(
            'log',
            'date_format',
            'branch',
            'remote'
        );

        foreach ( $options as $option => $value ) {
            if ( in_array( $option, $available_options ) ) {
                $this->{'_' . $option} = $value;
            }
        }

        $this->log( 'Attempting deployment...' );
    }

    /**
     * Writes a message to the log file.
     *
     * @param  string  $message  The message to write
     * @param  string  $type     The type of log message (e.g. INFO, DEBUG, ERROR, etc.)
     */
    public function log( $message, $type = 'INFO' ) {
        if ( $this->_log ) {
            // Set the name of the log file
            $filename = $this->_log;

            if ( !file_exists( $filename ) ) {
                // Create the log file
                file_put_contents( $filename, '' );

                // Allow anyone to write to log files
                chmod( $filename, 0666 );
            }

            // Write the message into the log file
            // Format: time --- type: message
            if ( !is_array( $message ) ) {
                $messages = array( $message );
            } else {
                $messages = $message;
            }

            if ( isset( $_GET['out'] ) ) {
                foreach ( $messages as $key => $message ) {
                    echo $message . "\n\r";
                }
            } else {
                foreach ( $messages as $key => $message ) {
                    file_put_contents( $filename, date( $this->_date_format ) . ' --- ' . $type . ': ' . $message . PHP_EOL, FILE_APPEND );
                }
            }

        }
    }

    /**
     * Executes the necessary commands to deploy the website.
     */
    public function execute( ) {
        try {
            // Make sure we're in the right directory
            // exec( 'cd ' . $this->_directory, $output );
            chdir( $this->_directory );
            $this->log( "Changing working directory $this->_directory ... " );

            exec( 'ls -ltr ', $output );
            $this->log( 'ls -ltr ' );
            $this->log( $output, 'OUT' );
            unset( $output );

            // Discard any changes to tracked files since our last deploy
            exec( 'git reset --hard HEAD', $output );
            $this->log( 'Reseting repository... ' );
            $this->log( $output, 'OUT' );
            unset( $output );

            // Update the local repository
            exec( 'git pull ' . $this->_remote . ' ' . $this->_branch, $output );
            $this->log( 'Pulling in changes... ' );
            $this->log( $output, 'OUT' );
            unset( $output );

            // Update submodules
            exec( 'git submodule update --recursive --init ', $output );
            $this->log( 'Updating submodules... ' );
            $this->log( $output, 'OUT' );
            unset( $output );

            // Update submodules
            exec( 'git submodule foreach git pull origin master ', $output );
            $this->log( 'Updating submodules... ' );
            $this->log( $output, 'OUT' );
            unset( $output );

            // Secure the .git directory
            exec( 'chmod -R og-rx .git' );
            $this->log( 'Securing .git directory... ' );

            if ( is_callable( $this->post_deploy ) ) {
                call_user_func( $this->post_deploy, $this->_data );
            }

            $this->log( 'Deployment successful.' );
        } catch (Exception $e) {
            $this->log( $e, 'ERROR' );
        }
    }

}

$repos = array(
    array(
        'path' => dirname( __FILE__ ) . '/wp-content/plugins/unionlido-surveys-mvc',
        'settings' => array(
            'log' => dirname( __FILE__ ) . '/unionlido-surveys-mvc_deployments.log',
            'remote' => 'origin',
            'branch' => 'master',
        )
    ),
    array(
        'path' => dirname( __FILE__ ) . '/wp-content/plugins/geo-arredofamily',
        'settings' => array(
            'log' => dirname( __FILE__ ) . '/geo-arredofamily_deployments.log',
            'remote' => 'origin',
            'branch' => 'master',
        )
    ),
    array(
        'path' => dirname( __FILE__ ) . '/wp-content/plugins/punti-arredofamily',
        'settings' => array(
            'log' => dirname( __FILE__ ) . '/punti-arredofamily_deployments.log',
            'remote' => 'origin',
            'branch' => 'develop',
        )
    ),
);

foreach ( $repos as $repo ) {
    $deploy = new Deploy( $repo['path'], $repo['settings'] );

    // $deploy->post_deploy = function( ) use ( $deploy ) {
    // hit the wp-admin page to update any db changes
    // exec('curl http://www.foobar.com/wp-admin/upgrade.php?step=upgrade_db');
    // $deploy->log( 'Updating wordpress database... ' );
    // };

    $deploy->execute( );
}
?>