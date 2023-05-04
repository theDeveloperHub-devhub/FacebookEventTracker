# Overview #

Magento 2 Facebook Event Tracker is the extension that allows you to add a Facebook pixel to Magento 2 pages faster,
easier and without any technical knowledge. This extension enables you to track customers'
actions on your store and use that data to create personalized Facebook Ads.

### Features ###

* Better event tracking.
* Personalized Facebook Ads.
* Product Performance tracking.
* Checkout Event Tracking.
* Facebook pixel IDs per store view.

### Installation ###

1. Please run the following command
```shell
composer require thedevhub/facebook-event-tracker
```

2. Update the composer if required
```shell
composer update
```

3. Enable module
```shell
php bin/magento module:enable DeveloperHub_FacebookEventTracker
php bin/magento setup:upgrade
php bin/magento cache:clean
php bin/magento cache:flush
```
4. If your website is running on the production mode then you need to run the following command
```shell
php bin/magento setup:static-content:deploy
php bin/magento setup:di:compile
```

