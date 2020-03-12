<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">新建运费模版</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">模版名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="delivery[name]"
                                           value="" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">计费方式 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <label class="am-radio-inline">
                                        <input type="radio" name="delivery[method]" value="10" data-am-ucheck
                                               checked> 按件数
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" name="delivery[method]" value="20" data-am-ucheck>
                                        按重量
                                    </label>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">
                                    配送区域及运费
                                </label>
                                <div class="am-u-sm-9 am-u-lg-10 am-u-end">
                                    <div class=" am-scrollable-horizontal">
                                        <table class="regional-table am-table am-table-bordered
                                         am-table-centered am-margin-bottom-xs">
                                            <tbody>
                                            <tr>
                                                <th width="50%">可配送区域</th>
                                                <th>
                                                    <span class="first">首件 (个)</span>
                                                </th>
                                                <th>运费 (元)</th>
                                                <th>
                                                    <span class="additional">续件 (个)</span>
                                                </th>
                                                <th>续费 (元)</th>
                                                <th>操作</th>
                                            </tr>
                                            <tr id="regional_choice_tpl" class="delivery_tr">
                                                <td class="am-text-left am-form-inline">
                                                    <div class="am-form-group">
                                                        <label class="am-checkbox-inline" style="padding:0">
                                                            <select name="delivery[rule][region][0][]" onchange="findCity(this)" >
                                                                <option value="">--请选择--</option>
                                                                <?php foreach ($provinceList as $p):?>
                                                                    <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                                                <?php endforeach;?>
                                                            </select>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td >
                                                    <label><input type="number" name="delivery[rule][first][0]"
                                                                  value="1" required=""
                                                                  class="am-field-valid first">
                                                    </label>
                                                </td><td>
                                                    <label>
                                                    <input type="number" name="delivery[rule][first_fee][0]"
                                                           value="0.00" required=""
                                                           class="am-field-valid first_fee">
                                                    </label>
                                                </td><td>
                                                    <label><input type="number" name="delivery[rule][additional][0]"
                                                                  value="0"
                                                                  class="am-field-valid additional"></label>
                                                </td><td>
                                                    <label>
                                                    <input type="number" name="delivery[rule][additional_fee][0]"
                                                           value="0.00" class="am-field-valid additional_fee">
                                                    </label>
                                                </td>
                                                <td>
                                                    <a  href="#" onclick="addDelivery(this)" class="am-icon-plus-square add_operation"></a>
                                                    <a href="#" style="display: none" onclick="removeDelivery(this)" class="am-icon-minus-square del_operation"></a>
                                                </td>
                                            </tr>

                                            <tr id="latest_tr">
                                                <td colspan="6"></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">排序 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="delivery[sort]"
                                           value="100" required>
                                    <small>数字越小越靠前</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">提交
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
<script src="assets/store/js/delivery.js"></script>
<script>
    var tpl_obj=$('#regional_choice_tpl');
    var latest_tr=$('#latest_tr');
    var tplNode = $(tpl_obj.clone());

    tplNode.find('.add_operation').css('display','none');
    tplNode.find('.del_operation').removeAttr('style');
    $(function () {


        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
    var url='<?= url() ?>';

    function findCity(obj,key) {
      var pid = $(obj).val();
      var name =  $(obj).attr('name')
      var select = '<label class="am-checkbox-inline" style="padding:0">' +
        '<select name='+name+' onchange=findCity(this)><option value="">全部</option>';
      $.post(url,{pid:pid},function (res) {
        if (res.data.length === 0) return;
        for (var i=0;i<res.data.length;i++){
          select += '<option value="'+res.data[i].id+'">'+res.data[i].name+'</option>'
        }
        select += '<select></label>';
        $(obj).parent().nextAll().each(function () {
          $(this).remove()
        });
        $(select).insertAfter($(obj).parent());
      })
    }

    function addDelivery(obj) {
      var l = $('.delivery_tr').length;
      tplNode.find('select').attr('name','delivery[rule][region]['+l+'][]');
      tplNode.find('.first').attr('name','delivery[rule][first]['+l+']');
      tplNode.find('.first_fee').attr('name','delivery[rule][first_fee]['+l+']');
      tplNode.find('.additional').attr('name','delivery[rule][additional]['+l+']');
      tplNode.find('.additional_fee').attr('name','delivery[rule][additional_fee]['+l+']');
      latest_tr.parent().append('<tr class="delivery_tr">'+tplNode.html()+'</tr>')

    }
    function removeDelivery(obj) {
      $(obj).parent().parent().remove()
    }
</script>
