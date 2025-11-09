<template>
    <div class="container-fluid">
        <form action="">
            <label class="form-label" for="">Select Month:</label>
            <div class="row">
                <div class="col-sm-5">
                    <div class="row g-2">
                        <div class="col-sm-8">
                            <input type="month" v-model="monthYear" name="month" class="form-control">
                        </div>
                        <div class="col-sm">
                            <button type="button" class="btn btn-primary" @click="reloadDashboard">Load Dashboard</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-sm-7">
                <div class="card mt-3">
                    <div class="card-header">Monthly Dashboard</div>
                    <div class="card-body" style="height: 600px;">
                        <canvas id="monthlyDashboard"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card mt-3">
                            <div class="card-header">
                            Savings Rate for {{ monthYear }} 
                            </div>
                            <div class="card-body">
                                <h2 class="card-title text-center text-primary">
                                    <i class="fa-solid fa-piggy-bank me-2"></i>
                                    {{ this.savingsRate }}%
                                </h2>
                                <p class="text-muted text-center">{{ this.savingsRateDescription }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card mt-3">
                            <div class="card-header">
                            Debt-To-Income Ratio for {{ monthYear }} 
                            </div>
                            <div class="card-body">
                                <h2 class="card-title text-center text-primary">
                                    <i class="fa-solid fa-balance-scale me-2"></i>
                                    {{ this.debtToIncomeRatio }}%
                                </h2>
                                <p class="text-muted text-center">{{ this.debtToIncomeRatioDescription }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        Total Income for {{ monthYear }} 
                    </div>
                    <div class="card-body">
                        <h2 class="card-title text-center text-success">
                            <i class="fa-solid fa-arrow-up me-2"></i>
                            {{ this.formatPeso(monthIncome) }}
                        </h2>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        Total Expense for {{ monthYear }} 
                    </div>
                    <div class="card-body">
                        <h2 class="card-title text-center text-danger">
                            <i class="fa-solid fa-arrow-down me-2"></i>
                            {{ this.formatPeso(monthExpense) }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { Chart, Title, Tooltip, Legend, ArcElement, DoughnutController, LineController, LineElement, BarElement } from 'chart.js'

    Chart.register(Title, Tooltip, Legend, ArcElement, DoughnutController, LineController, LineElement, BarElement);

    export default{
        data(){
            return{
                monthYear: '',
                monthIncome: 0,
                monthExpense: 0,
                monthSavings: 0,
                monthDebtPayments: 0,
                savingsRate: 0,
                debtToIncomeRatio: 0,
                chart: null,
            }
        },
        methods: {
            getCurrentMonthYear(){
                const today = new Date();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const year = today.getFullYear();
                return `${year}-${month}`;
            },
            fetchChartData(monthYear){
                axios.get('/api/monthly-dashboard/data', {
                    params: {
                        monthYear: monthYear
                    }
                })
                    .then(response => {
                        const data = response.data;
                        this.renderChart(data);
                    });
            },
            fetchMonthSummary(monthYear){
                axios.get('/api/monthly-dashboard/summary', {
                    params: {
                        monthYear: monthYear
                    }
                }).then(response => {
                    const data = response.data;
                    this.monthIncome = data.income;
                    this.monthExpense = data.expense;
                    this.monthSavings = data.savings;
                    this.monthDebtPayments = data.debt_payments;
                    this.computeSavingsRate();
                    this.computeDebtToIncomeRatio();
                });
            },
            reloadDashboard(){
                this.fetchChartData(this.monthYear);
                this.fetchMonthSummary(this.monthYear);
            },
            renderChart(data){
                const ctx = document.getElementById('monthlyDashboard').getContext('2d');

                if(this.chart){
                    this.chart.destroy();
                }

                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Expenses by Category',
                            data: data.values,
                            backgroundColor: data.colors,
                            borderColor: '#000',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => this.formatPeso(value)
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: context => {
                                        const value = context.parsed.y;
                                        return this.formatPeso(value);
                                    }
                                }
                            },
                            legend: {
                                display: true
                            }
                        }
                    }
                });
                
            },
            computeSavingsRate(){
                if(this.monthIncome === 0) {
                    this.savingsRate = 0;
                } else {
                    this.savingsRate = ((this.monthSavings / this.monthIncome) * 100).toFixed(1);
                }
            },
            computeDebtToIncomeRatio(){
                if(this.monthIncome === 0) {
                    this.debtToIncomeRatio = 0;
                } else {
                    this.debtToIncomeRatio = ((this.monthDebtPayments / this.monthIncome) * 100).toFixed(1);
                }
            },
        },
        mounted(){
            this.monthYear = this.getCurrentMonthYear();
            this.fetchMonthSummary(this.monthYear);
            this.fetchChartData(this.monthYear);
        },
        computed: {
            savingsRateDescription(){
                const rate = this.savingsRate;
                if (rate >= 20) {
                    return 'Excellent savings rate!';
                } else if (rate >= 10) {
                    return 'Good savings rate.';
                } else if (rate >= 5) {
                    return 'Average savings rate.';
                } else {
                    return 'Consider improving your savings.';
                }
            },
            debtToIncomeRatioDescription(){
                const ratio = this.debtToIncomeRatio;
                if (ratio < 21) {
                    return 'Excellent - low debt burden';
                } else if (ratio < 36) {
                    return 'Acceptable - manageable debt';
                } else if (ratio < 50) {
                    return 'Caution - may limit borrowing ability';
                }else if (ratio >= 50) {
                    return 'Risky - high debt load';
                } else {
                    return '';
                }
            }
        }
    }
</script>
