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
            <div class="col-sm-8">
                <div class="card mt-3">
                    <div class="card-header">Monthly Dashboard</div>
                    <div class="card-body" style="height: 600px;">
                        <canvas id="monthlyDashboard"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
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
                        Total Income for {{ monthYear }} 
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
                })
                    .then(response => {
                        const data = response.data;
                        this.monthIncome = data.income;
                        this.monthExpense = data.expense;
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
                
            }
        },
        mounted(){
            this.monthYear = this.getCurrentMonthYear();
            this.fetchMonthSummary(this.monthYear);
            this.fetchChartData(this.monthYear);
        }
    }
</script>
