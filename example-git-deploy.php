<?php

/**
 * SITO è l'utente proprietario del sito (es.: arredofamily2.remedia.me =>
 * arredofamily2)
 * per ottenere SITO, spostarsi in /var/www/vhosts/sito.dominio/ e digitare il
 * comando
 user=$(ls -l | grep httpdocs/ | awk '{print $3}')
 sito=$(basename `pwd`)
 # nel caso della 253, bisogna usare www-data come user
 repo="git@bitbucket.org:design_remedia/REPOSITORY.git"
 mkdir /var/www/vhosts/$sito/.ssh
 chown -R $user:psacln /var/www/vhosts/$sito/.ssh
 su -m $user -c "ssh-keygen -q -t rsa -f /var/www/vhosts/$sito/.ssh/id_rsa -P ''" # choose "no passphrase"
 cat /var/www/vhosts/$sito/.ssh/id_rsa.pub # aggiungere al repository su bitbucket con designers@remedia.it
 cd /var/www/vhosts/$sito/httpdocs
 su -m $user -c "git init"
 su -m $user -c "git remote add origin $repo"
 su -m $user -c "ssh $repo" # per confermare la chiave
 chown -R $user:psacln ./
 su -m $user -c "git pull -u origin master"

 */

date_default_timezone_set( 'Europe/Rome' );

include_once dirname( __FILE__ ) . '/get-envinronment.php';
include_once dirname( __FILE__ ) . '/GitDeploy.class.php';

$repos = array( array(
        'path' => dirname( __FILE__ ) . '',
        'settings' => array(
            'log' => dirname( __FILE__ ) . '/deployments.log',
            'remote' => 'origin',
            'branch' => 'master',
            // 'branch' => DEV_ENVINRONMENT == Envinronments::production ?
            // 'master' : 'development',
        )
    ), );

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