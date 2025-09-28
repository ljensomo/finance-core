<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"><strong>Monthly Income vs Expenses</strong></div>

                            <div class="card-body">
                                <canvas id="incomeVsExpenseChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 py-4">
                        <div class="card">
                            <div class="card-header"><strong>Export Transactions</strong></div>
                            <div class="card-body">
                                <form @submit.prevent="downloadCSV">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="startDate">Start Date</label>
                                                <input type="date" class="form-control" id="startDate" name="startDate" v-model="startDate" required>
                                            </div>
                                            <div class="form-group mt-3">
                                                <label for="Type">Type</label>
                                                <select class="form-control" id="type" name="type" v-model="type">
                                                    <option value="">All Types</option>
                                                    <option value="1">Income</option>
                                                    <option value="2">Expense</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="endDate">End Date</label>
                                                <input type="date" class="form-control" id="endDate" name="endDate" v-model="endDate" required>
                                            </div>
                                            <div class="form-group mt-3">
                                                <label for="category">Category</label>
                                                <select class="form-control" id="category" name="category" v-model="category">
                                                    <option value="">All Categories</option>
                                                    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success mt-3">Download as CSV</button>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Spending Breakdown</strong></div>

                    <div class="card-body">
                        <canvas id="spendingChart" width="200" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { ref, onMounted } from 'vue'
    import { Chart, Title, Tooltip, Legend, ArcElement, LineController, LineElement, CategoryScale, LinearScale, PointElement, Filler } from 'chart.js'
    import { Doughnut } from 'vue-chartjs'
    import { Helpers } from '../methods/helpers.js';

    Chart.register(Title, Tooltip, Legend, ArcElement, LineController, LineElement, CategoryScale, LinearScale, PointElement, Filler)

    export default {
        data() {
            return {
                categories: [],
                startDate: '',
                endDate: '',
                type: '',
                category: ''
            }
        },
        mounted() {
            this.fetchMonthlyIncomeExpenses();
            this.fetchSpendingBreakdown();
            this.fetchCategories();
        },
        methods: {
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
            fetchSpendingBreakdown() {
                axios.get('/api/reports/spending-breakdown')
                    .then(response => {
                        const data = response.data;
                        this.renderSpendingChart(data);
                    })
                    .catch(error => {
                        console.error('Error fetching spending breakdown:', error);
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
            renderSpendingChart(data) {
                const ctx = document.getElementById('spendingChart').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Spending by Category',
                            data: data.data,
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(153, 102, 255, 1)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'left',
                            },
                            title: {
                                display: true,
                                text: 'Spending Breakdown by Category'
                            }
                        }
                    }
                });
            },
            downloadCSV() {
                axios.post('/api/reports/export-transactions', {
                    start_date: this.startDate,
                    end_date: this.endDate,
                    type: this.type,
                    category: this.category
                }, {
                    responseType: 'blob'
                }).then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'transactions.csv');
                    document.body.appendChild(link);
                    link.click();
                }).catch(error => {
                    console.error('Error exporting transactions:', error);
                });
            },
        }
    }
</script>
