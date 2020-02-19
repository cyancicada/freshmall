CREATE TABLE `yoshop_score_shop` (
  `score_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品id',
  `name` varchar(200) DEFAULT NULL COMMENT '换购商品名称',
  `img` int(11) DEFAULT NULL COMMENT '商品图片',
  `score_num` int(11) DEFAULT NULL COMMENT '需要的积分',
  `stock_num` int(10) DEFAULT '0' COMMENT '商品当前库存',
  `status` tinyint(3) DEFAULT '10' COMMENT '商品状态(10上架 20下架)',
  `wxapp_id` int(11) unsigned DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`score_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分可兑换商品';