<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf">用户列表</div>
                </div>
                <div class="widget-body am-fr">
                    <form style="padding: 0 10px;" action="index.php" method="get">
                        <input type="hidden" value="<?= request()->path() ?>" name="s">
                        <label for="username">用户名：<input style="width: 100px;" type="text" value="<?= request()->get('username') ?>" id="username" class="tpl-form-input" name="username"/></label>
                        <button type="submit"  style="padding:3px;" class="j-submit am-btn am-btn-sm am-btn-secondary">
                            搜索
                        </button>
                    </form>
                </div>
                <div class="widget-body am-fr">
                    <div class="am-scrollable-horizontal am-u-sm-12">
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                            <tr>
                                <th>用户ID</th>
                                <th>微信昵称</th>
                                <th>余额</th>
                                <th>更新时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()): foreach ($list as $item): ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['user_id'] ?></td>
                                    <td class="am-text-middle"><?= $item['nickName'] ?></td>
                                    <td class="am-text-middle"><?= $item['balance'] ?></td>
                                    <td class="am-text-middle"><?= $item['update_time'] ?></td>
                                    <td class="am-text-middle">
                                        <a class="tpl-table-black-operation"
                                           onclick="balanceItems('<?= $item['nickName'] ?>','<?= $item['user_id'] ?>')"
                                           href="javascript:void(0) ;">明细</a>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="8" class="am-text-center">暂无记录</td>
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

<!-- 商品多规格模板 -->
{{include file="user/balance_items" /}}

<script>
  var url = 'index.php?s=store/user/balanceItems';


  function balanceItems(nickName,userId) {
    let balanceItemsElement = $('#balance_items');
    $.ajax({
      type: "post",
      url: url,
      async: true,
      data: JSON.stringify({
        user_id: userId,
        nickName: nickName,
      }),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      success: function (r) {

        balanceItemsElement.html(template('balance_items', {list: r.data, nickName: nickName}));
        layer.open({
          area: ['850px', '500px'],
          title: '用户【' + nickName + '】余额明细',
          btn: ['确定', '取消'],
          content: balanceItemsElement.html(),
        })
      } // 注意不要在此行增加逗号
    });

  }
</script>

