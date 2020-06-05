<script id="balance_items" type="text/html">
    <div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf">用户【<?= $userName ?>】余额明细</div>
                </div>
                <div class="widget-body am-fr">
                    <div class="am-scrollable-horizontal am-u-sm-12">
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                            <tr>
                                <th>交易号</th>
                                <th>时间</th>
                                <th>实充金额</th>
                                <th>附加</th>
                                <th>总金额</th>
                                <th>类型</th>
                                <th>账户余额</th>
                                <th>备注</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()): foreach ($list as $item): ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['trade_no'] ?></td>
                                    <td class="am-text-middle"><?= $item['create_time'] ?></td>
                                    <td class="am-text-middle"><?= $item['actual_amount'] ?></td>
                                    <td class="am-text-middle"><?= $item['extra'] ?></td>
                                    <td class="am-text-middle"><?= $item['balance'] ?></td>
                                    <td class="am-text-middle"><?= $item['type_name'] ?></td>
                                    <td class="am-text-middle"><?= $item['latest_balance'] ?></td>
                                    <td class="am-text-middle"><?= $item['mark'] ?></td>
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
</script>

