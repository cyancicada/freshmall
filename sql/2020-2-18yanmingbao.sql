ALTER TABLE `yoshop_goods`
ADD COLUMN `is_score`  tinyint(3) NULL COMMENT '1为可用积分兑换购买，（初定比例1：100）' AFTER `is_delete`;