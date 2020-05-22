<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf"><?= $title ?></div>
                </div>
                <div class="widget-body am-fr">
                    <form style="padding: 0 10px;" action="index.php" method="get">
                        <input type="hidden" value="<?= request()->path() ?>" name="s">
                        <label for="order_no">订单号：<input style="width: 100px;" type="text" value="<?= request()->get('order_no') ?>" id="order_no" class="tpl-form-input" name="order_no"/></label>
                        <label for="username">用户名：<input style="width: 100px;" type="text" value="<?= request()->get('username') ?>" id="username" class="tpl-form-input" name="username"/></label>

                        <label for="claim_time">配送时间：
                            <input style="width: 200px;" readonly type="text" value="<?= request()->get('claim_time') ?>"
                                   class="layui-input date_range_seletor" autocomplete="off"
                                   name="claim_time"/>
                            <select name="claim_range">
                                <option value="">全部</option>
                                <?php  foreach ($range[1] as $item):?>
                                <option <?= request()->get('claim_range') == $item ? 'selected' :''; ?> value="<?= $item ?>"><?= $item ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <button type="submit"  style="padding:3px;" class="j-submit am-btn am-btn-sm am-btn-secondary">
                            搜索
                        </button>
                    </form>
                </div>
                <div class="widget-body am-fr">
                    <div class="order-list am-scrollable-horizontal am-u-sm-12 am-margin-top-xs">
                        <table width="100%" class="am-table am-table-centered
                        am-text-nowrap am-margin-bottom-xs">
                            <thead>
                            <tr>
                                <th width="30%" class="goods-detail">商品信息</th>
                                <th width="10%">单价/数量</th>
                                <th width="15%">实付款</th>
                                <th>买家</th>
                                <th>交易状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()): foreach ($list as $order): ?>
                                <tr class="order-empty">
                                    <td colspan="6"></td>
                                </tr>
                                <tr>
                                    <td class="am-text-middle am-text-left" colspan="6">
                                        <span class="am-margin-right-lg"> <?= $order['create_time'] ?></span>
                                        <span class="am-margin-right-lg">订单号：<?= $order['order_no'] ?></span>
                                        <span class="am-margin-right-lg">配送时间：<?= $order['claim_delivery_time'] ?> <?= $order['claim_time_range'] ?></span>
                                    </td>
                                </tr>
                                <?php $i = 0;
                                foreach ($order['goods'] as $goods): $i++; ?>
                                    <tr>
                                        <td class="goods-detail am-text-middle">
                                            <div class="goods-image">
                                                <img src="<?= $goods['image']['file_path'] ?>" alt="">
                                            </div>
                                            <div class="goods-info">
                                                <p class="goods-title"><?= $goods['goods_name'] ?></p>
                                                <p class="goods-spec am-link-muted">
                                                    <?= $goods['goods_attr'] ?>
                                                </p>
                                            </div>
                                        </td>
                                        <td class="am-text-middle">
                                            <p>￥<?= $goods['goods_price'] ?></p>
                                            <p>×<?= $goods['total_num'] ?></p>
                                        </td>
                                        <?php if ($i === 1) : $goodsCount = count($order['goods']); ?>
                                            <td class="am-text-middle" rowspan="<?= $goodsCount ?>">
                                                <p>￥<?= $order['pay_price'] ?></p>
                                                <p class="am-link-muted">(含运费：￥<?= $order['express_price'] ?>)</p>
                                            </td>
                                            <td class="am-text-middle" rowspan="<?= $goodsCount ?>">
                                                <p><?= $order['user']['nickName'] ?></p>
                                                <p class="am-link-muted">(用户id：<?= $order['user']['user_id'] ?>)</p>
                                            </td>
                                            <td class="am-text-middle" rowspan="<?= $goodsCount ?>">
                                                <p>付款状态：
                                                    <span class="am-badge
                                                <?= $order['pay_status']['value'] == 20 ? 'am-badge-success' : '' ?>">
                                                        <?= $order['pay_status']['text'] ?></span>
                                                </p>
                                                <p>发货状态：
                                                    <span class="am-badge
                                                <?= $order['delivery_status']['value'] == 20 ? 'am-badge-success' : '' ?>">
                                                        <?= $order['delivery_status']['text'] ?></span>
                                                </p>
                                                <p>收货状态：
                                                    <span class="am-badge
                                                <?= $order['receipt_status']['value'] == 20 ? 'am-badge-success' : '' ?>">
                                                        <?= $order['receipt_status']['text'] ?></span>
                                                </p>
                                            </td>
                                            <td class="am-text-middle" rowspan="<?= $goodsCount ?>">

                                                <div class="tpl-table-black-operation">
                                                    <a class="tpl-table-black-operation-green"
                                                       href="<?= url('order/detail', ['order_id' => $order['order_id']]) ?>">
                                                        订单详情</a>
                                                    <?php if ($order['pay_status']['value'] == 20
                                                        && $order['delivery_status']['value'] == 10): ?>
                                                        <a class="tpl-table-black-operation"
                                                           href="<?= url('order/detail#delivery',
                                                               ['order_id' => $order['order_id']]) ?>">
                                                            去发货</a>
                                                    <?php endif; ?>

                                                    <?php if ($order['pay_status']['value'] == 20 &&
                                                        $order['is_refund'] == 'N' &&
                                                        in_array($order['order_status']['value'],[10])): ?>
                                                        <a class="tpl-table-black-operation"
                                                           onclick="refundEvent('<?= $order['order_no'] ?>','<?= $order['pay_price'] ?>')"
                                                           href="javascript:void(0) ;">
                                                            退款</a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="6" class="am-text-center">暂无记录</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="am-u-lg-12 am-cf">
                        <div class="am-fr"><?= $list->render() ?> </div>
                        <div class="am-fr pagination-total am-margin-right">
                            <div class="am-vertical-align-middle">总记录：<?= $list->total() ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

  var url = 'index.php?s=store/order/refund';

  function refundEvent(orderNo, refundAmount) {
    var html = '    <form class="my-form am-form tpl-form-line-form" method="post">' +
      '        <div class="am-form-group">' +
      '            <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">退款订单号 </label>' +
      '            <div class="am-u-sm-9 am-u-end">' +
      '                <input type="text" id="refund_order_no" value="' + orderNo + '" readonly' +
      '                       class="tpl-form-input" name="refund[order_no]"' +
      '                       required>' +
      '            </div>' +
      '        </div>' +
      '        <div class="am-form-group">' +
      '            <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">最高可退金额 </label>' +
      '            <div class="am-u-sm-9 am-u-end">' +
      '                <input type="number" id="refund_amount" max="' + refundAmount + '" value="' + refundAmount + '"' +
      '                       class="tpl-form-input" name="refund[amount]" required>' +
      '            </div>' +
      '        </div>' +
      '        <div class="am-form-group">' +
      '            <label class="am-u-sm-3 am-u-lg-2 am-form-label">备注 </label>' +
      '            <div class="am-u-sm-9 am-u-end">' +
      '                <input type="text" id="refund_mark" value="" class="tpl-form-input" name="refund[mark]">' +
      '            </div>' +
      '        </div>' +
      '    </form>';
    layer.open({
      area: ['500px', '350px'],
      btn: ['确定', '取消'],
      content: html
      , yes: function (index, layero) {
        let amount = parseFloat($('#refund_amount').val());
        if (amount > parseFloat(refundAmount)) {
          layer.msg('最高可退金额不可超过订单支付金额：' + refundAmount);
          return true;
        }
        var param = {
          orderNo: orderNo,
          amount: amount,
          mark: $('#refund_mark').val(),
        };
        console.log(param);
        $.ajax({
          type: "post",
          url: url,
          async: true,
          data: JSON.stringify(param),
          contentType: "application/json; charset=utf-8",
          dataType: "json",
          success: function (r) {
            layer.msg(r.msg);
            return  r.code !== 1;
          } // 注意不要在此行增加逗号
        });
        return false;
      }
      , btn2: function () {

      }
    });
  }
</script>


