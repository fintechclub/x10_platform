<div class="col-sm-12 mt-4">
    <div class="card">
        <h5 class="card-header">Транзакции</h5>
        <div class="card-body">
            <table class="table table-bordered">
                <tr class="dark">
                    <td>№</td>
                    <td>Актив</td>
                    <td>Дата</td>
                    <td>Источник</td>
                    <td>Кол-во</td>
                    <td>Курс, btc</td>
                    <td>Курс, usd</td>
                    <td>Операция</td>
                    <td>Комментарий</td>
                    <td></td>
                </tr>

                <tr v-for="t in transactions">
                    <td>
                        @{{ t.id }}
                    </td>
                    <td>
                            <span v-if="t.asset">
                                @{{ t.asset.title }}
                                <small>@{{ t.asset_id }}</small>
                                </span>
                        <span class="badge badge-primary" v-if="t.closed==1">closed</span>

                    </td>
                    <td>@{{ t.when }}</td>
                    <td>#@{{ t.source_id }}</td>
                    <td>@{{ t.amount }}</td>
                    <td>@{{ t.price_btc  | formatBtc}}</td>
                    <td>@{{ t.price_usd | formatUsd }}</td>
                    <td>
                                <span
                                        class="badge"
                                        :class="t.type=='sell' ? 'badge-danger' : 'badge-success'"
                                >
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