<?php
  $installer = $this;
  $installer->startSetup();
  $installer->run("

    CREATE TABLE IF NOT EXISTS `aadhaarup_checkout_orders`  (
      `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `amount` bigint(20) NOT NULL,
      `response_url`  varchar(500),
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8
  ");

  $installer->endSetup();