ALTER TABLE `yoshop_goods`
MODIFY COLUMN `deduct_stock_type`  tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '库存计算方式(10下单减库存 20付款减库存)' AFTER `spec_type`;