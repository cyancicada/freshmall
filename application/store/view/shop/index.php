<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf">换购中的商品</div>
                </div>
                <div class="widget-body am-fr">
                    <div class="am-u-sm-12 am-u-md-6 am-u-lg-6">
                        <div class="am-form-group">
                            <div class="am-btn-toolbar">
                                <div class="am-btn-group am-btn-group-xs">
                                    <a class="am-btn am-btn-default am-btn-success am-radius"
                                       href="<?= url('shop/add') ?>">
                                        <span class="am-icon-plus"></span> 新增
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="am-scrollable-horizontal am-u-sm-12">
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                            <tr>
                                <th>商品ID</th>
                                <th>商品图片</th>
                                <th>商品名称</th>
                                <th>所需积分</th>
                                <th>当前库存</th>
                                <!-- <th>商品状态</th>
                                <th>操作</th> -->
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()): foreach ($list as $item): ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['score_id'] ?></td>
                                    <td class="am-text-middle">
                                        <img src="<?= $item['file_url'].$item['file_name'] ?>"
                                             width="50" height="50" alt="商品图片">
                                    </td>
                                    <td class="am-text-middle">
                                        <p class="item-title"><?= $item['name'] ?></p>
                                    </td>
                                    <td class="am-text-middle"><?= $item['score_num'] ?></td>
                                    <td class="am-text-middle"><?= $item['stock_num'] ?></td>
                                    <!-- <td class="am-text-middle">
                                            <span class="<?= $item['status'] == 10 ? 'x-color-green'
                                                : 'x-color-red' ?>">
                                            <?= $item['status'] == 10 ? '上架
                                                ' : '下架' ?>
                                            </span>
                                    </td>
                                    <td class="am-text-middle">
                                        <div class="tpl-table-black-operation">
                                            <a href="javascript:;" class="x-color-green"
                                               data-id="<?= $item['score_id'] ?>">
                                                上架
                                            </a>
                                            <a href="javascript:;" class="x-color-red"
                                               data-id="<?= $item['score_id'] ?>">
                                                下架
                                            </a>
                                            <a href="javascript:;" class="item-delete tpl-table-black-operation-del"
                                               data-id="<?= $item['score_id'] ?>">
                                                <i class="am-icon-trash"></i> 删除
                                            </a>
                                        </div>
                                    </td> -->
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="9" class="am-text-center">暂无记录</td>
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
    $(function () {
        // 删除元素
        var url = "<?= url('shop/delete') ?>";
        $('.item-delete').delete('score_id', url);

    });
</script>

