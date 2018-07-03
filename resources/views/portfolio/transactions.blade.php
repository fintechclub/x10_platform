<div class="col-sm-12 mt-4">
    <div class="card">
        <h5 class="card-header">Транзакции</h5>
        <div class="card-body">
            <table class="table table-bordered">
                <tr class="dark">
{{--                    <td>№</td>--}}
                    <td>Дата</td>
                    <td>Актив</td>
                    <td>Кол-во</td>
                    <td>Цена, btc</td>
                    <td>Цена, usd</td>
                    <td>Стоимость, BTC</td>
                    <td>Операция</td>
                    <td>Комментарий</td>
                    <td></td>
                </tr>

                <tr v-for="t in sortByParam(transactions,'when', 'desc')" :class="{trashed: t.deleted_at}">
{{--                    <td>
                        @{{ t.id }}
                    </td>--}}
                    <td>@{{ t.when | formatDate }}</td>
                    <td>
                            <span v-if="t.asset">
                                @{{ t.asset.title }}
                                {{--<small>@{{ t.asset_id }}</small>--}}
                                </span>
                        <span class="badge badge-primary" v-if="t.closed==1">closed</span>

                    </td>
                    <td>@{{ t.amount}}</td>
                    <td>@{{ t.price_btc  | formatBtc}}</td>
                    <td>@{{ t.price_usd | formatUsd }}</td>
                    <td>@{{ t.amount * t.price_btc | formatBtc}}</td>

                    <td class="op-type">
                                <span class="badge badge-danger" v-if="t.type=='sell'">
                                @{{ t.type }}
                                </span>

                        <span class="badge badge-success" v-if="t.type=='buy'">
                                @{{ t.type }}
                                </span>

                        <span class="badge badge-warning" v-if="t.type=='withdraw'">
                                @{{ t.type }}
                                </span>
                    </td>
                    <td>@{{ t.comment }}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-light" @click="editTransaction(t)">
                            <i class="far fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-light" @click="removeTransaction(t)">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>