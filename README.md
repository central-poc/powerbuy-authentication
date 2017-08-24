# README #

A Magento 2 module to support lagacy Powerbuy authentication hashing.

# Installation

Please use composer to install the extension. 

    1. Add ssh public key to your bitbucket account.
    2. Contact nachatchai@central.co.th to grant your access.
    3. At your magento root, 

        * composer config repositories.powerbuyauthentication git git@bitbucket.org:centraltechnology/powerbuy-authentication.git
        * composer require powerbuy/authentication:dev-master
        * php bin/magento module:enable Powerbuy_Authentication