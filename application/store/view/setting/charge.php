<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my_form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">充值设置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label form-require">（条件一）充值满： </label>
                                <div class="am-u-sm-9 am-input-group">
                                    <div class="am-u-sm-12">
                                        <input type="number" class="am-form-field" name="charge[0][amount]"
                                               value="<?= isset($values[0]['amount']) ? $values[0]['amount'] : 0 ?>"
                                               pattern="^(0|\+?[1-9][0-9]*)$" required>

                                    </div>
                                </div>
                                <label class="am-u-sm-3 am-form-label form-require">赠送： </label>
                                <div class="am-u-sm-9 am-input-group">
                                    <div class="am-u-sm-12">
                                        <input type="number" class="am-form-field" name="charge[0][free_get]"
                                               value="<?= isset($values[0]['free_get']) ? $values[0]['free_get'] : 0 ?>"
                                               pattern="^(0|\+?[1-9][0-9]*)$" required>

                                    </div>
                                </div>
                            </div>


                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label form-require">（条件二）充值满： </label>
                                <div class="am-u-sm-9 am-input-group">
                                    <div class="am-u-sm-12">
                                        <input type="number" class="am-form-field" name="charge[1][amount]"
                                               value="<?= isset($values[1]['amount']) ? $values[1]['amount'] : 0 ?>"
                                               pattern="^(0|\+?[1-9][0-9]*)$" required>

                                    </div>
                                </div>
                                <label class="am-u-sm-3 am-form-label form-require">赠送： </label>
                                <div class="am-u-sm-9 am-input-group">
                                    <div class="am-u-sm-12">
                                        <input type="number" class="am-form-field" name="charge[1][free_get]"
                                               value="<?= isset($values[1]['free_get']) ? $values[1]['free_get'] : 0 ?>"
                                               pattern="^(0|\+?[1-9][0-9]*)$" required>

                                    </div>
                                </div>
                            </div>



                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label form-require">（条件三）充值满： </label>
                                <div class="am-u-sm-9 am-input-group">
                                    <div class="am-u-sm-12">
                                        <input type="number" class="am-form-field" name="charge[2][amount]"
                                               value="<?= isset($values[2]['amount']) ? $values[2]['amount'] : 0 ?>"
                                               pattern="^(0|\+?[1-9][0-9]*)$" required>

                                    </div>
                                </div>
                                <label class="am-u-sm-3 am-form-label form-require">赠送： </label>
                                <div class="am-u-sm-9 am-input-group">
                                    <div class="am-u-sm-12">
                                        <input type="number" class="am-form-field" name="charge[2][free_get]"
                                               value="<?= isset($values[2]['free_get']) ? $values[2]['free_get'] : 0 ?>"
                                               pattern="^(0|\+?[1-9][0-9]*)$" required>

                                    </div>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label form-require">活动时间： </label>
                                <div class="am-u-sm-9 am-input-group">
                                    <div class="am-u-sm-12">
                                        <input type="text" class="layui-input time_range_selector" autocomplete="off"
                                               name="charge[time_range]"
                                               readonly
                                               value="<?= $values['time_range'] ?>"
                                               required>

                                    </div>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="button" onclick="submitSetting()" class="j-submit am-btn am-btn-secondary">提交
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    var url = 'index.php?s=/store/setting/charge';
    var my_form = $('#my_form');
    function submitSetting() {
        $.ajax({
            type: "post",
            url: url,
            async: true,
            data: my_form.serializeArray(),
            success: function (r) {
                layer.msg(r.msg);
                if (r.code === 1){
                    window.location.reload();
                }
            } // 注意不要在此行增加逗号
        });
    }
</script>
