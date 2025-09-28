<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
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
                    <div class="card-header"><strong>Monthly Income vs. Expenses Summary</strong></div>

                    <div class="card-body">
                        <p>Your monthly income is:</p>
                        <h2>{{ this.formatPeso(monthlyIncome) }}</h2>
                        <p>Your monthly expenses are:</p>
                        <h2>{{ this.formatPeso(monthlyExpenses) }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row py-4 justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Recent Transactions</strong></div>

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
                    <div class="card-header"><strong>Monthly Spending</strong></div>

                    <div class="card-body">
                        <canvas id="spendingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { ref, onMounted } from 'vue'
    import { Chart, Title, Tooltip, Legend, ArcElement, DoughnutController} from 'chart.js'
    import { Doughnut } from 'vue-chartjs'
    import { Helpers } from '../methods/helpers.js';

    Chart.register(Title, Tooltip, Legend, ArcElement, DoughnutController)

    export default {
        mixins: [Helpers],
        mounted() {
            this.fetchBalance();
            this.fetchRecentTransactions();
            this.fetchMonthlyIncome();
            this.fetchMonthlyExpenses();
            this.fetchSpendingCategories();
        },
        data() {
            return {
                recentTransactions: [],
                totals: {
                    income: 0,
                    expenses: 0,
                    balance: 0
                },
                monthlyIncome: 0,
                monthlyExpenses: 0,
                chart: null,
                chartData:{
                    labels: [],
                    datasets: [
                        {
                            label: 'Spending by Category',
                            data: [],
                            backgroundColor: [
                                '#FF6384', // Soft Red
                                '#36A2EB', // Sky Blue
                                '#FFCE56', // Sunny Yellow
                                '#4BC0C0', // Teal
                                '#9966FF', // Lavender Purple
                                '#F67019', // Orange
                                '#00A950', // Emerald Green
                                '#C9CBCF'  // Cool Gray
                            ],
                        }
                    ]
                },
                chartOptions: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                generateLabels: () => []
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: () => ''
                            }
                        },
                        title: {
                            display: true,
                            text: 'Spending Categories'
                        }
                    },
                    maintainAspectRatio: false
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
            },
            fetchMonthlyIncome(){
                axios.get('/api/dashboard/monthly-income').then(response => {
                    this.monthlyIncome = response.data;
                }).catch(error => {
                    console.log(error);
                });
            },
            fetchMonthlyExpenses(){
                axios.get('/api/dashboard/monthly-expenses').then(response => {
                    this.monthlyExpenses = response.data;
                }).catch(error => {
                    console.log(error);
                });
            },
            fetchSpendingCategories(){
                axios.get('/api/dashboard/spending-categories').then(response => {
                    const categories = response.data;
                    this.chartData.labels = Object.keys(categories);
                    this.chartData.datasets[0].data = Object.values(categories);

                    const pesoFormatter = this.formatPeso
                    this.chartOptions.plugins.tooltip.callbacks.label = function(context) {
                        return pesoFormatter(context.parsed)
                    }

                    this.chartOptions.plugins.legend.labels.generateLabels = function(chart) {
                        return chart.data.labels.map((label, i) => {
                        const value = chart.data.datasets[0].data[i]
                        return {
                            text: `${label}: ${pesoFormatter(value)}`,
                            fillStyle: chart.data.datasets[0].backgroundColor[i],
                            strokeStyle: chart.data.datasets[0].backgroundColor[i],
                            lineWidth: 1,
                            hidden: false,
                            index: i
                        }
                        })
                    }

                    const ctx = document.getElementById('spendingChart').getContext('2d');
                    this.chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: this.chartData,
                        options: this.chartOptions
                    });
                }).catch(error => {
                    console.log(error);
                });
            },
        }
    }
</script>
