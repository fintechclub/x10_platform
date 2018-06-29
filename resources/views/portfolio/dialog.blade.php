<!-- Modal -->
<div class="modal fade" ref="transactionDialog" id="addTransaction" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Новая транзакция</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form">

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Инструмент</label>
                                <select name="ticker" class="form-control" v-model="tr.asset_id"
                                        @change="loadSourceTransactions(tr.asset_id)">
                                    <option value="">Select ticker</option>
                                    @foreach(App\Asset::tickers() as $ticker)
                                        <option value="{{$ticker['value']}}">{{$ticker['label']}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Объем сделки</label>
                                <input type="text" placeholder="Amount" class="form-control" v-model="tr.amount"/>
                            </div>

                            <div class="form-group">
                                <label>Стоимость в BTC</label>
                                <input type="text" placeholder="Price BTC" class="form-control" v-model="tr.price_btc"/>
                            </div>

                            <div class="form-group">
                                <label>Стоимость в USD</label>
                                <input type="text" placeholder="Price USD" class="form-control" v-model="tr.price_usd"/>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Дата</label>
                                <input type="date" placeholder="Дата" class="form-control" v-model="tr.when"/>
                            </div>

                            <div class="form-group">
                                <label>Тип операции</label>
                                <select class="form-control" v-model="tr.type">
                                    <option value="sell">SELL</option>
                                    <option value="buy">BUY</option>
                                    <option value="withdraw">Withdraw</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Комментарий</label>
                                <input type="text" placeholder="Comment" class="form-control" v-model="tr.comment"/>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           value="1"
                                           v-model="tr.deduct_btc"
                                           id="defaultCheck1">

                                    <label class="form-check-label" for="defaultCheck1">
                                        Учитывать в BTC
                                    </label>


                                </div>
                            </div>

                        </div>
                    </div>

                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary"
                        :class="{busy: dialogbusy}"
                        @click="saveTransaction()"
                        :disabled="isDisabled()"
                >Сохранить</button>
            </div>
        </div>
    </div>
</div>