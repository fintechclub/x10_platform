<div class="col-sm-12 mt-4">

    <div class="card">
        <h5 class="card-header">Динамика</h5>
        <div class="card-body">

            <table class="table table-bordered">
                <tr class="dark">
                    <td colspan="3">Баланс портфеля на</td>
                    <td colspan="2">Изменение USD %</td>
                    <td colspan="2">Изменение BTC %</td>
                    <td colspan="2">Цена BTC</td>
                </tr>
                <tr class="light">
                    <td>Дата</td>
                    <td>USD</td>
                    <td>RUB</td>
                    <td>С прошлой записи</td>
                    <td>С начала создания</td>
                    <td>С прошлой записи</td>
                    <td>С начала создания</td>
                    <td>USD</td>
                    <td>Изменения за период, %</td>
                </tr>

                <tr v-for="(s,index) in snapshots">
                    <td>@{{ s.created_at }}</td>
                    <td>@{{ s.stats.balance_usd }}</td>
                    <td>@{{ s.stats.balance_rub }}</td>
                    <td>@{{ getDifference(s, index, 'balance_usd') }}</td>
                    <td></td>
                    <td>@{{ getDifference(s, index, 'balance_btc')  | formatNumber}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

            </table>


        </div>
    </div>

</div>