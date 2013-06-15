<?php
  $installer = $this;
  $installer->getConnection()->addColumn($installer->getTable('sales/order'), 'mintzone_txn_id', 'string(120) NULL');
  $installer->endSetup();