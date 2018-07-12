<div class="col-sm-12 mt-4">
    <div class="card">
        <h5 class="card-header">Транзакции</h5>
        <div class="card-body">
            <table class="table table-bordered">
                <tr class="dark">
                    <td>Дата</td>
                    <td colspan="2">Актив</td>
                    <td>Количество</td>
                    <td>Цена, BTC</td>
                    <td>Цена, USD</td>
                    <td>Стоимость, BTC</td>
                    <td>Операция</td>
                    <td>Комментарий</td>
                    <td></td>
                </tr>

                <tr v-for="t in sortByParam(transactions,'when', 'desc')" :class="{trashed: t.deleted_at}">
                    <td>@{{ t.when | formatDate }}</td>
                    <td>
                        @{{ t.asset.ticker }}
                    </td>
                    <td>
                            <span v-if="t.asset">
                                @{{ t.asset.title }}
                                </span>
                    </td>
                    <td class="text-right">@{{ t.amount}}</td>
                    <td class="text-right">@{{ t.price_btc  | format5}}</td>
                    <td class="text-right">@{{ t.price_usd | formatUsd }}</td>
                    <td class="text-right">@{{ t.amount * t.price_btc | format5}}</td>

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
                    <td class="text-center text-nowrap">
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

<div class="col-sm-6 mt-4">
    <div class="card">
        <h5 class="card-header">Депозит портфеля</h5>
        <div class="card-body">
            <div class="form-inline">
                <label for="inlineFormInputName2">Депозит, Руб.</label>
                <input type="text" class="form-control mb-2 ml-2 mr-2" id="inlineFormInputName2"
                       placeholder="500000" v-model="portfolio.deposit">
                <button type="button" class="btn btn-primary mb-2" @click="updateDeposit()">Сохранить</button>
            </div>
        </div>
    </div>
</div>