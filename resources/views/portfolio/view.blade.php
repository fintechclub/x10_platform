@extends('layouts.app')
@section('content')

    <div class="row" id="portfolio" v-cloak>


        <div class="col-sm-12 mb-4">

            <button class="btn btn-success btn-sm" @click="openTransactionDialog()">
                <i class="fas fa-plus"></i>
            </button>

            <button class="btn btn-primary btn-sm" :class="{busy: busy}" @click="updatePortfolio()">
                Обновить
            </button>

            <span class="text-muted small"
                  v-if="current.current_date">Показаны данные на @{{ current.current_date.date | formatDate }}</span>

        </div>

        <div class="col-sm-6">
            <div class="card">
                <h3 class="card-header">Баланс</h3>
                <div class="card-body" v-if="current.snapshot">
                    <table class="table text-center">
                        <tr class="dark">
                            <td>BTC</td>
                            <td>USD</td>
                            <td>RUB</td>
                        </tr>
                        <tr>
                            <td>@{{ current.snapshot.btc | format5 }}</td>
                            <td>@{{ current.snapshot.usd  | formatUsd}}</td>
                            <td>@{{ current.snapshot.rub  | formatUsd}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card">
                <h3 class="card-header">Курс</h3>

                <div class="card-body">
                    <table class="table text-center">
                        <tr class="dark">
                            <td>BTC/USD</td>
                            <td>BTC/RUB</td>
                        </tr>
                        <tr>
                            <td><span v-if="rates.btc_usd">@{{ rates.btc_usd  | formatUsd}}</span></td>
                            <td><span v-if="rates.btc_rub">@{{ rates.btc_rub  | formatUsd}}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-sm-12 mt-4">
            <div class="card">
                <h5 class="card-header">Состав портфеля</h5>
                <div class="card-body">

                    <table class="table table-bordered">
                        <tr class="dark">
                            <td>Символ</td>
                            <td>Наименование</td>
                            <td>Баланс</td>
                            <td>Средняя цена покупки, BTC</td>
                            <td>Средняя цена покупки, USD</td>
                            <td>Средняя цена продажи, BTC</td>
                            <td>Доля актива в портфеле %</td>
                            <td>Стоимость, BTC</td>
                            <td>Стоимость, USD</td>
                        </tr>

                        <tr v-for="item in sortArrays(current.items)" v-if="item.amount>min_amount">
                            <td>@{{ item.asset.ticker }}</td>
                            <td>@{{ item.asset.title }}</td>
                            <td class="text-right">@{{ item.amount | format5 }}</td>
                            <td class="text-right">@{{ item.avg_buy_price_btc |  format5}}</td>
                            <td class="text-right">@{{ item.avg_buy_price_usd | format5}}</td>
                            <td class="text-right">
                                <span v-if="item.avg_sell_price_btc>0">
                                @{{item.avg_sell_price_btc | format5}}
                                </span>
                            </td>
                            <td class="text-right">
                                @{{ item.amount * item.avg_buy_price_btc / current.snapshot.btc * 100 | formatPercent }}
                                %
                            </td>
                            <td class="text-right">@{{ item.amount * item.avg_buy_price_btc | format5 }}</td>
                            <td class="text-right">@{{ item.amount * item.avg_buy_price_usd | format5}}</td>
                        </tr>

                    </table>


                </div>
            </div>

        </div>

        @include('portfolio.history')

        @include('portfolio.transactions')

        @include('portfolio.dialog')

    </div>
@endsection

@section('scripts')

    <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script src="https://unpkg.com/vue-select@latest"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script src="https://unpkg.com/vue-chartjs/dist/vue-chartjs.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

    <script>

        Vue.filter('formatDate', function (value) {
            if (value) {
                return moment(String(value)).format('DD-MM-YYYY')
            }
        });


        Vue.filter("format5", function (value) {
            return numeral(value).format("0,0.00000");
        });

        Vue.filter("formatNumber", function (value) {
            return numeral(value).format("0,0.00000000");
        });

        Vue.filter("formatUsd", function (value) {
            return numeral(value).format("0,0.00");
        });

        Vue.filter("formatPercent", function (value) {
            return numeral(value).format("0,0.0");
        });

        Vue.filter("formatBtc", function (value) {
            return numeral(value).format("0,0.00000000");
        });

        let sample = {
            user_id: '{{$user->id}}',
            portfolio_id: '{{$portfolio->id}}',
            asset_id: '',
            deduct_btc: '',
            source_id: '',

        }

        Vue.component('line-chart-usd', {
            extends: VueChartJs.Line,
            mounted () {

                axios.get('/api/portfolio/charts/{{$portfolio->id}}/usd').then(response => {
                    let labels = response.data.labels;
                    let data = response.data.usd;

                    console.log(labels);

                    this.renderChart({
                            labels: labels,
                            datasets: [
                                {
                                    label: 'USD',
                                    data: data
                                }
                            ]
                        }, {
                            responsive: true, maintainAspectRatio: false
                        }
                    )

                });
            }
        });

        Vue.component('line-chart-btc', {
            extends: VueChartJs.Line,
            mounted () {

                axios.get('/api/portfolio/charts/{{$portfolio->id}}/btc').then(response => {
                    let labels = response.data.labels;
                    let data = response.data.btc;

                    this.renderChart({
                            labels: labels,
                            datasets: [
                                {
                                    label: 'BTC',
                                    backgroundColor: '#f87979',
                                    data: data
                                }
                            ]
                        }, {
                            responsive: true, maintainAspectRatio: false
                        }
                    )

                });
            }
        });

        var portfolio = new Vue({
            el: '#portfolio',
            data: {
                min_amount: {{env('min_amount', 0.000001)}},
                portfolio: {!! $portfolio !!},
                transactions: [],
                current: [],
                items: [],
                snapshots: [],
                tr: sample,
                sources: [],
                busy: false,
                dialogbusy: false,
                rates: {
                    btc_usd: '',
                    btc_rub: ''
                }
            },
            mounted: function () {

                // get transactions from API
                axios.get('/api/transactions/get/' + this.portfolio.id).then(response => {
                    this.transactions = response.data;
                });

                // get current state for portfolio
                axios.get('/api/portfolio/current/' + this.portfolio.id).then(response => {
                    this.current = response.data;
                    this.rates = response.data.rates;
                });

                // get current state for portfolio
                axios.get('/api/portfolio/snapshots/' + this.portfolio.id).then(response => {
                    this.snapshots = response.data;
                });


            },
            methods: {

                reloadDashboard(){

                    // get current state for portfolio
                    axios.get('/api/portfolio/current/' + this.portfolio.id).then(response => {
                        this.current = response.data;
                        this.rates = response.data.rates;
                        this.busy = false;
                    });

                    // get current state for portfolio
                    axios.get('/api/portfolio/snapshots/' + this.portfolio.id).then(response => {
                        this.snapshots = response.data;
                        this.busy = false;
                    });

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
                        //this.snapshots = response.data;
                        //this.busy = false;
                        this.reloadDashboard();
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
                        //copy transaction
                        this.tr = Object.assign({}, t);
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
                        let diff = (s[type] - prev[type]) / s[type] * 100;

                        return diff;

                    }

                    return -1;

                },
                sortArrays(arrays) {
                    return _.orderBy(arrays, 'asset.ticker', 'asc');
                },
                sortByParam(arrays, param, type) {
                    return _.orderBy(arrays, param, type);
                },
                updateDeposit(){
                    axios.post('/api/portfolio/save', this.portfolio).then(response => {
                        this.portfolio = response.data;
                    });
                }
            },
            computed: {}
        });

    </script>
@endsection