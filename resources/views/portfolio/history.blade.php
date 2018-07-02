<div class="col-sm-12 mt-4">

    <div class="card">
        <h5 class="card-header">Динамика</h5>
        <div class="card-body">

            <table class="table table-bordered">
                <tr class="dark">
                    <td colspan="4">Баланс портфеля на</td>
                    <td colspan="2">Изменение USD %</td>
                    <td colspan="2">Изменение BTC %</td>
                    {{--<td colspan="2">Цена BTC</td>--}}
                </tr>
                <tr class="light">
                    <td>Дата</td>
                    <td>USD</td>
                    <td>RUB</td>
                    <td>BTC</td>
                    <td>С прошлой записи</td>
                    <td>С начала создания</td>
                    <td>С прошлой записи</td>
                    <td>С начала создания</td>
{{--                    <td>USD</td>
                    <td>Изменения за период, %</td>--}}
                </tr>

                <tr v-for="(s,index) in snapshots">
                    <td>@{{ s.created_at | formatDate }}</td>
                    <td>@{{ s.stats.balance_usd| formatUsd }}</td>
                    <td>@{{ s.stats.balance_rub | formatUsd}}</td>
                    <td>@{{ s.stats.balance_btc| formatBtc}}</td>
                    <td>@{{ getDifference(s, index, 'balance_usd') | formatPercent}}%</td>
                    <td></td>
                    <td>@{{ getDifference(s, index, 'balance_btc')  | formatPercent}}%</td>
                    <td></td>
                </tr>

            </table>


        </div>
    </div>

</div>

<div class="col-sm-12">
    <div class="row">

        <div class="col-sm-6">
            <line-chart-usd></line-chart-usd>
        </div>

        <div class="col-sm-6">
            <line-chart-btc></line-chart-btc>
        </div>

    </div>
</div>