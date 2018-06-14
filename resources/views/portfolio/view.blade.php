@extends('layouts.app')
@section('content')

    <div class="row" id="portfolio" v-cloak>


        <div class="col-sm-12 mb-4">

            <button class="btn btn-success btn-sm" @click="openTransactionDialog()">
                <i class="fas fa-plus"></i>
            </button>

            <button class="btn btn-primary btn-sm d-none" :class="{busy: busy}" @click="updatePortfolio()">
                Обновить
            </button>

            <span class="text-muted small"
                  v-if="current.current_date">Показаны данные на @{{ current.current_date.date }}</span>

        </div>

        <div class="col-sm-2">
            <div class="card">
                <h3 class="card-header">BTC</h3>
                <div class="card-body" v-if="current.stats">
                    @{{  current.stats.total_btc | formatBtc }}
                </div>
            </div>
        </div>

        <div class="col-sm-2">
            <div class="card">
                <h3 class="card-header">Баланс BTC</h3>
                <div class="card-body" v-if="current.stats">@{{  current.stats.balance_btc | formatBtc }}</div>
            </div>
        </div>

        <div class="col-sm-2">
            <div class="card">
                <h3 class="card-header">Баланс USD</h3>
                <div class="card-body" v-if="current.stats">@{{ current.stats.balance_usd  | formatUsd}}</div>
            </div>
        </div>

        <div class="col-sm-2">
            <div class="card">
                <h3 class="card-header">Баланс RUB</h3>
                <div class="card-body" v-if="current.stats">@{{ current.stats.balance_rub  | formatUsd}}</div>
            </div>
        </div>

        <div class="col-sm-2">
            <div class="card">
                <h3 class="card-header">BTC/USD</h3>
                <div class="card-body" v-if="current.stats">@{{ current.rates.btc_usd  | formatUsd}}</div>
            </div>
        </div>

        <div class="col-sm-2">
            <div class="card">
                <h3 class="card-header">BTC/RUB</h3>
                <div class="card-body" v-if="current.stats">@{{ current.rates.btc_rub  | formatUsd}}</div>
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
                            <td>ср.взв.Цена BTC buy</td>
                            <td>ср.взв.Цена BTC sell</td>
                            <td>Цена RUB</td>
                            <td>Доля актива в портфеле %</td>
                            <td>Стоимость, BTC</td>
                            <td>Стоимость, USD</td>
                        </tr>

                        <tr v-for="item in current.items">
                            <td>@{{ item.asset.ticker }}</td>
                            <td>@{{ item.asset.title }}</td>
                            <td>@{{ item.amount }}</td>
                            <td>@{{ item.avg_buy_price_btc }}</td>
                            <td>@{{item.avg_sell_price_btc }}</td>
                            <td>@{{ }}</td>
                            <td></td>
                            <td>@{{  }}</td>
                            <td>@{{  }}</td>
                        </tr>

                    </table>


                </div>
            </div>

        </div>

        {{--@include('portfolio.history')--}}

        @include('portfolio.transactions')

        @include('portfolio.dialog')

    </div>
@endsection

@section('scripts')

    <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script src="https://unpkg.com/vue-select@latest"></script>

    <script>

        Vue.filter("formatNumber", function (value) {
            return numeral(value).format("0.00000000");
        });

        Vue.filter("formatUsd", function (value) {
            return numeral(value).format("0.00");
        });

        Vue.filter("formatBtc", function (value) {
            return numeral(value).format("0.00000000");
        });

        let sample = {
            user_id: '{{$user->id}}',
            portfolio_id: '{{$portfolio->id}}',
            asset_id: '',
            deduct_btc: '',
            source_id: ''
        }

        var portfolio = new Vue({
            el: '#portfolio',
            data: {
                portfolio: {!! $portfolio !!},
                transactions: [],
                current: [],
                items: [],
                snapshots: [],
                tr: sample,
                sources: [],
                busy: false,
                dialogbusy: false
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
                /*               axios.get('/api/portfolio/snapshots/' + this.portfolio.id).then(response => {
                 this.snapshots = response.data;
                 });*/

            },
            methods: {

                reloadDashboard(){

                    // get current state for portfolio
                    axios.get('/api/portfolio/current/' + this.portfolio.id).then(response => {
                        this.current = response.data;
                    });

                    // get current state for portfolio
                    /*               axios.get('/api/portfolio/snapshots/' + this.portfolio.id).then(response => {
                     this.snapshots = response.data;
                     });*/

                },

                openTransactionDialog(){
                    this.tr = {};
                    this.tr = sample;
                    $(this.$refs.transactionDialog).modal('show');
                },
                // load BUY transations by asset_id
                loadSourceTransactions(asset_id){

                    axios.get('/api/transactions/opened/' + asset_id + '/' + this.portfolio.id).then(response => {
                        this.sources = response.data;
                    });

                    // update rate for selected asset
                    axios.get('/api/assets/' + asset_id + '/price').then(response => {
                        this.tr.price_btc = response.data.btc;
                        this.tr.price_usd = response.data.usd;
                        Vue.nextTick();
                    });

                },
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

                    this.dialogbusy = true;
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

                        $(this.$refs.transactionDialog).modal('hide');
                        this.tr = sample;

                        this.dialogbusy = false;

                        this.reloadDashboard();

                    });
                },

                // edit transaction
                editTransaction(t){

                    // get sources for element firstly
                    axios.get('/api/transactions/opened/' + t.asset_id + '/' + this.portfolio.id).then(response => {
                        this.sources = response.data;
                        this.tr = t;
                        $(this.$refs.transactionDialog).modal('show');
                    });

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

                            this.reloadDashboard();

                        });

                    }


                },
                isDisabled(){

                    if (!this.tr.asset_id || !this.tr.amount || !this.tr.price_btc || !this.tr.type) {
                        return true;
                    }

                    return false;
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