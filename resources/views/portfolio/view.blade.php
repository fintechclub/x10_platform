@extends('layouts.app')
@section('content')

    <div class="row" id="portfolio">

        <div class="col-sm-12 mb-4">
            <button class="btn btn-primary btn-sm" :class="{busy: busy}" @click="updatePortfolio()">
                Обновить
            </button>

            <span class="text-muted small"
                  v-if="current.current_date">Показаны данные на @{{ current.current_date.date }}</span>

        </div>

        <div class="col-sm-2">
            <div class="card">
                <h3 class="card-header">Баланс BTC</h3>
                <div class="card-body" v-if="current.stats">@{{  current.stats.balance_btc | formatNumber }}</div>
            </div>
        </div>

        <div class="col-sm-2">
            <div class="card">
                <h3 class="card-header">Баланс USD</h3>
                <div class="card-body" v-if="current.stats">@{{ current.stats.balance_usd  | formatNumber}}</div>
            </div>
        </div>

        <div class="col-sm-2">
            <div class="card">
                <h3 class="card-header">Баланс RUB</h3>
                <div class="card-body" v-if="current.stats">@{{ current.stats.balance_rub  | formatNumber}}</div>
            </div>
        </div>

        <div class="col-sm-2">
            <div class="card">
                <h3 class="card-header">BTC/USD</h3>
                <div class="card-body" v-if="current.stats">@{{ current.rates.btc_usd  | formatNumber}}</div>
            </div>
        </div>

        <div class="col-sm-2">
            <div class="card">
                <h3 class="card-header">BTC/RUB</h3>
                <div class="card-body" v-if="current.stats">@{{ current.rates.btc_rub  | formatNumber}}</div>
            </div>
        </div>

        <div class="col-sm-12 mt-4">

            <div class="card">
                <h5 class="card-header">Состав портфеля #{{$portfolio->id}}</h5>
                <div class="card-body">

                    <table class="table table-bordered">
                        <tr class="dark">
                            <td>Символ</td>
                            <td>Наименование</td>
                            <td>Баланс</td>
                            <td>Стоимость BTC</td>
                            <td>Стоимость USD</td>
                            <td>Стоимость RUB</td>
                            <td>Доля актива в портфеле %</td>
                            <td>Цена актива, BTC</td>
                            <td>Цена актива, USD</td>
                        </tr>

                        <tr v-for="item in current.items">
                            <td>@{{ item.asset.ticker }}</td>
                            <td>@{{ item.asset.title }}</td>
                            <td>@{{ item.amount }}</td>
                            <td>@{{ item.btc  | formatNumber }}</td>
                            <td>@{{ item.usd }}</td>
                            <td>@{{ item.rub }}</td>
                            <td></td>
                            <td>@{{ item.rate_btc  | formatNumber }}</td>
                            <td>@{{ item.usd/item.amount }}</td>
                        </tr>

                    </table>


                </div>
            </div>

        </div>

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

        <div class="col-sm-12 mt-4">
            <div class="card">
                <h5 class="card-header">Транзакции</h5>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr class="dark">
                            <td>№</td>
                            <td>Актив</td>
                            <td>Дата</td>
                            <td>Кол-во</td>
                            <td>Курс, btc</td>
                            <td>Курс, usd</td>
                            <td>Операция</td>
                            <td>Комментарий</td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="9">
                                <strong>Добавление / Редактирование транзакций</strong> <br/>
                                <p class="text-muted mt-2">
                                    Чтобы добавить новую транзакцию выберите тикер, введите цену закупки/продажи в BTC и
                                    USD, укажите количество и тип операции. Далее нажмите Save. <br/>
                                    Для редактирования - используйте иконку карандашик <i class="fa fa-edit"></i>
                                    напротив транзакции, ее данные занесутся в форму.
                                </p>
                            </td>
                        </tr>
                        <tr class="form-td-row">
                            <td>
                                <button class="btn btn-sm btn-primary" @click="saveTransaction()">
                                    SAVE
                                </button>
                            </td>
                            <td>
                                <select name="ticker" class="form-control" v-model="tr.asset_id">
                                    <option value="">Select ticker</option>
                                    @foreach(App\Asset::tickers() as $ticker)
                                        <option value="{{$ticker['value']}}">{{$ticker['label']}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="date" class="form-control" v-model="tr.when"/>
                            </td>
                            <td>
                                <input type="text" placeholder="Amount" class="form-control" v-model="tr.amount"/>
                            </td>
                            <td>
                                <input type="text" placeholder="Price BTC" class="form-control" v-model="tr.price_btc"/>
                            </td>
                            <td>
                                <input type="text" placeholder="Price USD" class="form-control" v-model="tr.price_usd"/>
                            </td>
                            <td>
                                <select class="form-control" style="width: 90px;" v-model="tr.type">
                                    <option value="sell">SELL</option>
                                    <option value="buy">BUY</option>
                                </select>
                            </td>
                            <td colspan="2">
                                <input type="text" placeholder="Comment" class="form-control" v-model="tr.comment"/>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="9">
                                <strong>История транзакций</strong>
                            </td>
                        </tr>

                        <tr v-for="t in transactions">
                            <td>
                                @{{ t.id }}
                            </td>
                            <td>
                            <span v-if="t.asset">
                                @{{ t.asset.title }}
                                </span>
                            </td>
                            <td>@{{ t.when }}</td>
                            <td>@{{ t.amount }}</td>
                            <td>@{{ t.price_btc  | formatNumber}}</td>
                            <td>@{{ t.price_usd }}</td>
                            <td>@{{ t.type }}</td>
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
    </div>
@endsection

@section('scripts')

    <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script src="https://unpkg.com/vue-select@latest"></script>

    <script>

        Vue.filter("formatNumber", function (value) {
            return numeral(value).format("0.00000000");
        });

        var portfolio = new Vue({
            el: '#portfolio',
            data: {
                portfolio: {!! $portfolio !!},
                transactions: [],
                current: [],
                items: [],
                snapshots: [],
                tr: {
                    user_id: '{{$user->id}}',
                    portfolio_id: '{{$portfolio->id}}',
                    asset_id: ''
                },
                busy: false
            },
            mounted: function () {

                // get transactions from API
                axios.get('/api/transactions/get/' + this.portfolio.id).then(response => {
                    this.transactions = response.data;
                });

                // get current state for portfolio
                axios.get('/api/portfolio/current/' + this.portfolio.id).then(response => {
                    this.current = response.data;
                });

                // get current state for portfolio
                axios.get('/api/portfolio/snapshots/' + this.portfolio.id).then(response => {
                    this.snapshots = response.data;
                });

            },
            methods: {

                // create new snapshot and reload data for current portfolio
                updatePortfolio(){

                    this.busy = true;
                    axios.get('/api/portfolio/update/' + this.portfolio.id).then(response => {
                        this.current = response.data;
                        this.busy = false;
                    });

                },
                // save selected transaction
                saveTransaction(){
                    axios.post('/api/transactions/add', this.tr).then(response => {

                        // check if it's a new transaction or not
                        let index = this.transactions.findIndex(item => item.id === this.tr.id);

                        if (index === -1) {
                            // add created transaction to list if not found
                            this.transactions.push(response.data);
                        } else {
                            // or just update transaction
                            this.transactions[index] = response.data;
                        }

                    });
                },

                // edit transaction
                editTransaction(t){
                    this.tr = t;
                },

                // remove selected transaction
                removeTransaction(t){

                    if (confirm('Are you sure?')) {

                        // remove transaction from database (hard delete)
                        let trId = t.id;
                        axios.post('/api/transactions/delete', t).then(response => {

                            // find and remove transaction from the list
                            let index = this.transactions.findIndex(item => item.id === trId);
                            if (index !== -1) {
                                this.transactions.splice(index, 1);
                            }
                        });

                    }

                },
                getDifference(s, index, type){

                    if (index < this.snapshots.length - 1) {

                        let prev = this.snapshots[index + 1];
                        let diff = (s.stats[type] - prev.stats[type]) / s.stats[type] * 100;

                        return diff;

                    }

                    return -1;

                }
            },
            computed: {}
        });

    </script>
@endsection