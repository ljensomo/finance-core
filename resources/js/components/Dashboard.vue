<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-dark">
                    <div class="card-header"><strong>Current Balance <i>(Total Income - Total Expenses)</i></strong></div>

                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="card border-primary mb-3" style="max-width: 18rem;">
                                    <div class="card-header bg-primary text-white">Total Income</div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ this.formatPeso(totals.income) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-danger mb-3" style="max-width: 18rem;">
                                    <div class="card-header bg-danger text-white">Total Expenses</div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ this.formatPeso(totals.expenses) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card border-success mb-3">
                            <div class="card-header bg-success text-white">Current Balance</div>
                            <div class="card-body">
                                <h4 class="card-title">{{ this.formatPeso(totals.balance) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Montly Income vs. Expenses Summary</div>

                    <div class="card-body">
                        <p>Your monthly income is:</p>
                        <h2>₱0.00</h2>
                        <p>Your monthly expenses are:</p>
                        <h2>0.00</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row py-4 justify-content-center">
            <div class="col-md-6">
                <div class="card border-dark">
                    <div class="card-header">Recent Transactions</div>

                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>Type</th>
                                <th>Category</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Date</th>
                            </tr>
                            <tr v-for="transaction in recentTransactions" :key="transaction.id">
                                <td>{{ transaction.type == 1 ? 'Income' : 'Expense' }}</td>
                                <td>{{ transaction.category ? transaction.category.name : '(Uncategorized)' }}</td>
                                <td class="text-end">{{ this.formatPeso(transaction.amount) }}</td>
                                <td class="text-end">{{ transaction.date }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Spending Categories</div>

                    <div class="card-body">
                        <ul>
                            <li>No spending categories.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.fetchBalance();
            this.fetchRecentTransactions();
        },
        data() {
            return {
                recentTransactions: [],
                totals: {
                    income: 0,
                    expenses: 0,
                    balance: 0
                }
            }
        },
        methods:{
            fetchBalance(){
                axios.get('/api/dashboard/balance').then(response => {
                    this.totals = response.data;
                }).catch(error => {
                    console.log(error);
                });
            },
            fetchRecentTransactions(){
                axios.get('/api/dashboard/recent-transactions').then(response => {
                    this.recentTransactions = response.data;
                }).catch(error => {
                    console.log(error);
                });
            }
        }
    }
</script>
