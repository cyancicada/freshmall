<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-body  am-margin-bottom-lg">
                        <div id="ddd" style="height: 400px">
                            <form id="delivery" class="my-form am-form tpl-form-line-form" method="post"
                                  action="<?= url('order/print') ?>">
                                <div class="am-form-group">
                                    <label for="order_sn" class="am-u-sm-3 am-u-lg-2 am-form-label form-require">订单号： </label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" id="order_sn" class="tpl-form-input" name="order_sn"/>
                                        <small>多个订单号用英文逗号,隔开；如21231,21321,....</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                        <button type="submit" class="j-submit am-btn am-btn-sm am-btn-secondary">
                                            确定
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
            </div>
        </div>
    </div>
</div>
    <script>
        $(function () {

            /**
             * 表单验证提交
             * @type {*}
             */
            $('.my-form').superForm();

        });
    </script>