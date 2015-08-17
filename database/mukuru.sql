DROP DATABASE IF EXISTS `mukuru`;

CREATE DATABASE IF NOT EXISTS `mukuru`;

USE `mukuru`;

CREATE TABLE `currencies` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `description` varchar(25) NOT NULL,
  `exchange_rate` decimal(11,8) NOT NULL,
  `surcharge` decimal(11,8) NOT NULL,
  `discount` decimal(3,2) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
);

INSERT INTO `currencies` (`id`, `code`, `description`, `exchange_rate`, `surcharge`, `discount`, `updated`) VALUES
(1, 'USD', 'US Dollars', '0.08082790', '7.50000000', '0.00', '2015-08-13 20:47:08'),
(2, 'GBP', 'British Pound', '0.05270320', '5.00000000', '0.00', '2015-08-13 20:47:08'),
(3, 'EUR', 'Euro', '0.07187100', '5.00000000', '2.00', '2015-08-13 20:47:08'),
(4, 'KES', 'Kenyan Shilling', '7.81498000', '2.50000000', '0.00', '2015-08-13 20:47:08');

CREATE TABLE IF NOT EXISTS `currency_notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) unsigned NOT NULL,
  `mail_to` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `currency_id` (`currency_id`)
);

CREATE TABLE IF NOT EXISTS `currency_notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) unsigned NOT NULL,
  `mail_to` varchar(55) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `currency_notifications` (`id`, `currency_id`, `mail_to`) VALUES
(1, 2, 'keorapetseb@gmail.com');


CREATE TABLE  IF NOT EXISTS `orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `currency_id` int(11) unsigned NOT NULL,
  `exchange_rate` decimal(11,8) NOT NULL,
  `order_total_amount` decimal(11,2) NOT NULL,
  `order_foreign_currency_total_amount` decimal(11,2) NOT NULL,
  `order_surcharge_percent` decimal(11,2) NOT NULL,
  `order_surcharge_amount` decimal(11,2) NOT NULL,
  `order_discount_percent` decimal(3,2) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY(currency_id)
);