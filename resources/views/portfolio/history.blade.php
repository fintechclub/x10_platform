<div class="col-sm-12 mt-4">

    <div class="card">
        <h5 class="card-header">Динамика</h5>
        <div class="card-body">

            <table class="table table-bordered">
                <tr class="dark">
                    <td colspan="3">Баланс портфеля</td>
                    <td colspan="2">Изменение USD %</td>
                    <td colspan="2">Изменение BTC %</td>
                    {{--<td colspan="2">Цена BTC</td>--}}
                </tr>
                <tr class="light">
                    <td>Дата</td>
                    <td>USD</td>
                    <td>BTC</td>
                    <td>С прошлой записи</td>
                    <td>С начала создания</td>
                    <td>С прошлой записи</td>
                    <td>С начала создания</td>
                    {{--                    <td>USD</td>
                                        <td>Изменения за период, %</td>--}}
                </tr>

                <tr v-for="(s,index) in snapshots" v-if="s.btc>0">
                    <td nowrap>@{{ s.created_at | formatDate }}</td>
                    <td class="text-right">@{{ s.usd| formatUsd }}</td>
                    <td class="text-right">@{{ s.btc| format5}}</td>
                    <td class="text-right" :class="[getDifference(s, index, 'usd')<0 ? 'text-danger' : 'text-success']">
                        @{{ getDifference(s, index, 'usd') | formatPercent}}%
                    </td>
                    <td class="text-right" :class="[s.usd_from_start<0 ? 'text-danger' : 'text-success']">
                        @{{ s.usd_from_start | formatPercent }}%
                    </td>
                    <td class="text-right" :class="[getDifference(s, index, 'btc')<0 ? 'text-danger' : 'text-success']">
                        @{{ getDifference(s, index, 'btc')  | formatPercent}}%
                    </td>
                    <td class="text-right" :class="[s.btc_from_start<0 ? 'text-danger':'text-success']">
                        @{{ s.btc_from_start | formatPercent }}%
                    </td>
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