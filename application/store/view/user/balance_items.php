<script id="balance_items" type="text/html">

<table  class="am-table am-table-compact am-table-striped
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
    {{ each $data.list }}
        <tr>
            <td class="am-text-middle">{{ $value.trade_no }}</td>
            <td class="am-text-middle">{{ $value.create_time }}</td>
            <td class="am-text-middle">{{ $value.actual_amount }}</td>
            <td class="am-text-middle">{{ $value.extra }}</td>
            <td class="am-text-middle">{{ $value.balance }}</td>
            <td class="am-text-middle">{{ $value.type_name }}</td>
            <td class="am-text-middle">{{ $value.latest_balance }}</td>
            <td class="am-text-middle">{{ $value.mark }}</td>
        </tr>
    {{ /each }}
    </tbody>
</table>
</script>

