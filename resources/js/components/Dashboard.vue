<template>
    <div class="container-fluid">
        <span class="">As of: {{ new Date() }}</span><hr>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header text-center"><i class="fa-solid fa-money-bill me-2"></i>Available Balance</div>
                            <div class="card-body">
                                <h3 class="card-title text-center text-secondary">{{ this.formatPeso(totals.balance) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header text-center"><i class="fa-solid fa-wallet me-2"></i>Total Income</div>
                            <div class="card-body">
                                <h3 class="card-title text-center text-success">{{ this.formatPeso(totals.income) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header text-center"><i class="fa-solid fa-receipt me-2"></i>Total Expenses</div>
                            <div class="card-body">
                                <h3 class="card-title text-center text-danger">{{ this.formatPeso(totals.expenses) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-header"><i class="fa-solid fa-chart-area me-2"></i>Income & Expenses Report</div>

                            <div class="card-body">
                                <canvas id="incomeVsExpenseChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"><i class="fa-solid fa-chart-pie me-2"></i>Overall Category Spending</div>

                            <div class="card-body">
                                <canvas id="spendingChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header text-center"><i class="fa-solid fa-arrow-up me-2"></i>Average Monthly Income</div>
                            <div class="card-body">
                                <h3 class="text-center text-success">{{ this.formatPeso(monthlyIncome) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header text-center"><i class="fa-solid fa-arrow-down me-2"></i>Average Monthly Expenses</div>
                            <div class="card-body">
                                <h3 class="text-center text-danger">{{ this.formatPeso(monthlyExpenses) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"><i class="fa-solid fa-file-invoice me-2"></i>Recent Transactions</div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped table-hover">
                                    <tr>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-end">Date</th>
                                    </tr>
                                    <tr v-for="transaction in recentTransactions" :key="transaction.id">
                                        <td>
                                            <span v-if="transaction.type == 1" class="badge bg-success text-white">
                                                <i class="fa-solid fa-arrow-up me-2"></i>Income
                                            </span>
                                            <span v-else class="badge bg-danger text-white">
                                                <i class="fa-solid fa-arrow-down me-2"></i>Expense
                                            </span>
                                        </td>
                                        <td>{{ transaction.category ? transaction.category.name : '(Uncategorized)' }}</td>
                                        <td class="text-end">{{ this.formatPeso(transaction.amount) }}</td>
                                        <td class="text-end">{{ transaction.date }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row py-4 justify-content-center">
            <div class="col-md-6">
            </div>
        </div>
    </div>
</template>

<script>
    import { Chart, Title, Tooltip, Legend, ArcElement, DoughnutController, LineController, LineElement, CategoryScale, LinearScale, PointElement, Filler} from 'chart.js'
    import { Doughnut } from 'vue-chartjs'
    import { Helpers } from '../methods/helpers.js';

    Chart.register(Title, Tooltip, Legend, ArcElement, DoughnutController, LineController, LineElement, CategoryScale, LinearScale, PointElement, Filler)

    export default {
        mixins: [Helpers],
        mounted() {
            this.fetchBalance();
            this.fetchRecentTransactions();
            this.fetchMonthlyIncome();
            this.fetchMonthlyExpenses();
            this.fetchMonthlyIncomeExpenses();
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
            fetchMonthlyIncomeExpenses() {
                axios.get('/api/reports/monthly-income-expenses')
                    .then(response => {
                        const data = response.data;
                        this.renderIncomeVsExpenseChart(data);
                    })
                    .catch(error => {
                        console.error('Error fetching monthly income and expenses:', error);
                    });
            },
            renderIncomeVsExpenseChart(data) {
                const currentYear = new Date().getFullYear();
                const ctx = document.getElementById('incomeVsExpenseChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Income',
                                data: data.income,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                fill: true,
                            },
                            {
                                label: 'Expenses',
                                data: data.expense,
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                fill: true,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: `Income vs Expenses (${currentYear})`
                            },
                            tooltip:{
                                callbacks: {
                                    label: context => {
                                        const value = context.parsed.y
                                        return this.formatPeso(value);
                                    }
                                }
                            },
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                ticks: {
                                    callback: value => this.formatPeso(value)
                                }
                            }
                        }
                    }
                });
            },
        }
    }
</script>
