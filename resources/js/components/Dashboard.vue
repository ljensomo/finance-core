<template>
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="text-muted small fw-bold text-uppercase tracking-wider">
                <i class="fa-solid fa-calendar-day me-1"></i>
                As of: <span class="text-dark">{{ formattedDate }}</span>
            </div>

            <span
                class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3"
            >
                <i class="fa-solid fa-circle fa-2xs me-1 pulse"></i> Real-time
            </span>
        </div>
        <div class="finance-overview">
            <!-- Financial Summary -->
            <div class="row my-3">
                <div class="col-md-4">
                    <div class="card border-1 shadow-sm h-100 rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div
                                    class="stats-icon bg-primary-subtle text-primary rounded-circle me-3"
                                >
                                    <i class="fa-solid fa-scale-balanced"></i>
                                </div>
                                <span
                                    class="text-muted fw-bold small text-uppercase"
                                    >Net Income</span
                                >
                            </div>
                            <h2
                                class="mb-0 fw-bold"
                                :class="
                                    financeSummary.net >= 0
                                        ? 'text-dark'
                                        : 'text-danger'
                                "
                            >
                                {{ formatPeso(financeSummary.net) }}
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-1 shadow-sm h-100 rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div
                                    class="stats-icon bg-success-subtle text-success rounded-circle me-3"
                                >
                                    <i class="fa-solid fa-arrow-trend-down"></i>
                                </div>
                                <span
                                    class="text-muted fw-bold small text-uppercase"
                                    >Total Income</span
                                >
                            </div>
                            <h2 class="mb-0 fw-bold text-success">
                                {{ formatPeso(financeSummary.income) }}
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-1 shadow-sm h-100 rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div
                                    class="stats-icon bg-danger-subtle text-danger rounded-circle me-3"
                                >
                                    <i class="fa-solid fa-arrow-trend-up"></i>
                                </div>
                                <span
                                    class="text-muted fw-bold small text-uppercase"
                                    >Total Expenses</span
                                >
                            </div>
                            <h2 class="mb-0 fw-bold text-danger">
                                {{ formatPeso(financeSummary.expenses) }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Ratios -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-1 shadow-sm rounded-4 h-100 bg-success-light-subtle">
                        <div class="card-body p-3">
                            <p class="text-success small fw-bold text-uppercase mb-1">Avg. Monthly In</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="fw-bold text-success mb-0">{{ formatPeso(monthlyIncome) }}</h4>
                                <i class="fa-solid fa-arrow-trend-down text-success opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-1 shadow-sm rounded-4 h-100 bg-danger-light-subtle">
                        <div class="card-body p-3">
                            <p class="text-danger small fw-bold text-uppercase mb-1">Avg. Monthly Out</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="fw-bold text-danger mb-0">{{ formatPeso(monthlyExpenses) }}</h4>
                                <i class="fa-solid fa-arrow-trend-up text-danger opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-1 shadow-sm rounded-4 h-100">
                        <div class="card-body p-3">
                            <p class="text-muted small fw-bold text-uppercase mb-1">Savings Rate</p>
                            <h4 class="fw-bold text-primary mb-2">{{ savingsRate }}%</h4>
                            <div class="progress" style="height: 4px">
                                <div class="progress-bar bg-primary" :style="{ width: savingsRate + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-1 shadow-sm rounded-4 h-100">
                        <div class="card-body p-3">
                            <p class="text-muted small fw-bold text-uppercase mb-1">DTI Ratio</p>
                            <h4 class="fw-bold mb-2" :class="debtToIncomeRatio > 40 ? 'text-danger' : 'text-warning'">{{ debtToIncomeRatio }}%</h4>
                            <div class="progress" style="height: 4px">
                                <div class="progress-bar" :class="debtToIncomeRatio > 40 ? 'bg-danger' : 'bg-warning'" :style="{ width: debtToIncomeRatio + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-1 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="stats-icon bg-primary-subtle text-primary rounded-3 me-3">
                                <i class="fa-solid fa-chart-area"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Income & Expenses Report</h5>
                        </div>
                        <div class="chart-container" style="position: relative; height:500px;">
                            <canvas id="incomeVsExpenseChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card border-1 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="stats-icon bg-info-subtle text-info rounded-3 me-3">
                                <i class="fa-solid fa-chart-pie"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Overall Category Spending</h5>
                        </div>
                        <div class="chart-container" style="position: relative; height:300px;">
                            <canvas id="spendingChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-1 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-secondary-subtle text-secondary rounded-3 me-3">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <h5 class="card-title mb-0 fw-bold">Recent</h5>
                            </div>
                            <a href="/transactions" class="btn btn-sm btn-link text-decoration-none p-0">View All</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle custom-table">
                                <thead class="text-muted small text-uppercase tracking-wider">
                                    <tr>
                                        <th class="border-0">Source</th>
                                        <th class="border-0 text-end">Amount</th>
                                        <th class="border-0 text-end px-0"></th> </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="transaction in recentTransactions" :key="transaction.id">
                                        <td class="border-bottom-0">
                                            <div class="fw-bold mb-0 text-dark">
                                                {{ transaction.category ? transaction.category.name : "Uncategorized" }}
                                            </div>
                                            <div class="text-muted extra-small mt-1">{{ transaction.date }}</div>
                                        </td>
                                        <td class="text-end border-bottom-0">
                                            <span :class="transaction.type == 1 ? 'text-success' : 'text-danger'" class="fw-bold">
                                                {{ formatPeso(transaction.amount) }}
                                            </span>
                                        </td>
                                        <td class="text-end border-bottom-0">
                                            <i v-if="transaction.type == 1" class="fa-solid fa-circle-arrow-down text-success opacity-50"></i>
                                            <i v-else class="fa-solid fa-circle-arrow-up text-danger opacity-50"></i>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {
    Chart,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    DoughnutController,
    LineController,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement,
    Filler,
} from "chart.js";
import { Helpers } from "../methods/helpers.js";

Chart.register(
    Title,
    Tooltip,
    Legend,
    ArcElement,
    DoughnutController,
    LineController,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement,
    Filler
);

export default {
    mixins: [Helpers],
    mounted() {
        this.fetchFinanceSummary();
        this.fetchMonthlyIncome();
        this.fetchMonthlyExpenses();
        this.fetchRecentTransactions();
        this.fetchMonthlyIncomeExpenses();
        this.fetchSpendingCategories();
    },
    data() {
        return {
            recentTransactions: [],
            financeSummary: {
                income: 0,
                expenses: 0,
                net: 0,
                savings: 0,
                debt_payments: 0,
            },
            savingsRate: 0,
            debtToIncomeRatio: 0,
            monthlyIncome: 0,
            monthlyExpenses: 0,
            chart: null,
            chartData: {
                labels: [],
                datasets: [{}],
            },
            chartOptions: {
                responsive: true,
                plugins: {
                    legend: {
                        position: "right",
                        labels: {
                            generateLabels: () => [],
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: () => "",
                        },
                    },
                    title: {
                        display: true,
                        text: "Spending Categories",
                    },
                },
                maintainAspectRatio: false,
            },
        };
    },
    methods: {
        fetchFinanceSummary() {
            axios
                .get("/api/dashboard/finance-overview")
                .then((response) => {
                    this.financeSummary = response.data;
                    this.computeSavingsRate();
                    this.computeDebtToIncomeRatio();
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        fetchRecentTransactions() {
            axios
                .get("/api/dashboard/recent-transactions")
                .then((response) => {
                    this.recentTransactions = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        fetchMonthlyIncome() {
            axios
                .get("/api/dashboard/monthly-income")
                .then((response) => {
                    this.monthlyIncome = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        fetchMonthlyExpenses() {
            axios
                .get("/api/dashboard/monthly-expenses")
                .then((response) => {
                    this.monthlyExpenses = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        fetchSpendingCategories() {
            axios
                .get("/api/dashboard/spending-categories")
                .then((response) => {
                    const categories = response.data || {};
                    
                    // 1. Process and Sort Data
                    const categoriesArray = Object.entries(categories)
                        .map(([name, details]) => ({
                            name,
                            value: Number(details.value) || 0, // Ensure it's a number
                            color: details.color
                        }))
                        .sort((a, b) => b.value - a.value);

                    const labels = categoriesArray.map(cat => cat.name);
                    const values = categoriesArray.map(cat => cat.value);
                    const colors = categoriesArray.map(cat => cat.color);
                    
                    // 2. Calculate Total (Used for percentages)
                    const totalSpending = values.reduce((acc, curr) => acc + curr, 0);

                    // 3. Update reactive data
                    this.chartData.labels = labels;
                    this.chartData.datasets[0].data = values;
                    this.chartData.datasets[0].backgroundColor = colors;

                    const pesoFormatter = this.formatPeso;
                    const ctx = document.getElementById("spendingChart").getContext("2d");

                    if (this.chart) {
                        this.chart.destroy();
                    }

                    this.chart = new Chart(ctx, {
                        type: "doughnut",
                        data: this.chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: (context) => {
                                            const val = context.parsed;
                                            // Guard against division by zero
                                            const percent = totalSpending > 0 
                                                ? ((val / totalSpending) * 100).toFixed(1) 
                                                : 0;
                                            return ` ${context.label}: ${pesoFormatter(val)} (${percent}%)`;
                                        }
                                    }
                                },
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        usePointStyle: true,
                                        generateLabels: (chart) => {
                                            const data = chart.data;
                                            return data.labels.map((label, i) => {
                                                const val = data.datasets[0].data[i];
                                                // Guard against division by zero
                                                const percent = totalSpending > 0 
                                                    ? ((val / totalSpending) * 100).toFixed(0) 
                                                    : 0;
                                                    
                                                return {
                                                    text: `${label} (${percent}%)`,
                                                    fillStyle: data.datasets[0].backgroundColor[i],
                                                    hidden: false,
                                                    index: i
                                                };
                                            });
                                        }
                                    }
                                }
                            }
                        }
                    });
                })
                .catch((error) => {
                    console.error("Error fetching spending categories:", error);
                });
        },
        fetchMonthlyIncomeExpenses() {
            axios
                .get("/api/reports/monthly-income-expenses")
                .then((response) => {
                    const data = response.data;
                    this.renderIncomeVsExpenseChart(data);
                })
                .catch((error) => {
                    console.error(
                        "Error fetching monthly income and expenses:",
                        error
                    );
                });
        },
        renderIncomeVsExpenseChart(data) {
            const ctx = document.getElementById("incomeVsExpenseChart").getContext("2d");
            
            // Create Gradients
            const incomeGradient = ctx.createLinearGradient(0, 0, 0, 400);
            incomeGradient.addColorStop(0, "rgba(25, 135, 84, 0.3)");
            incomeGradient.addColorStop(1, "rgba(25, 135, 84, 0)");

            const expenseGradient = ctx.createLinearGradient(0, 0, 0, 400);
            expenseGradient.addColorStop(0, "rgba(220, 53, 69, 0.3)");
            expenseGradient.addColorStop(1, "rgba(220, 53, 69, 0)");

            // Destroy existing chart to prevent memory leaks/glitches
            if (this.incomeExpenseChart) {
                this.incomeExpenseChart.destroy();
            }

            this.incomeExpenseChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: "Income",
                            data: data.income,
                            borderColor: "#198754",
                            backgroundColor: incomeGradient,
                            fill: true,
                            tension: 0.4, // Smooth curves
                            pointRadius: 2,
                            pointHoverRadius: 6,
                            borderWidth: 3
                        },
                        {
                            label: "Expenses",
                            data: data.expense,
                            borderColor: "#dc3545",
                            backgroundColor: expenseGradient,
                            fill: true,
                            tension: 0.4, // Smooth curves
                            pointRadius: 2,
                            pointHoverRadius: 6,
                            borderWidth: 3
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index', // Shows both values when hovering over the month
                    },
                    plugins: {
                        legend: {
                            position: "top",
                            align: "end",
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { weight: 'bold' }
                            }
                        },
                        title: { display: false }, // Use your HTML card header instead
                        tooltip: {
                            padding: 12,
                            backgroundColor: 'rgba(30, 41, 59, 0.9)',
                            titleFont: { size: 14 },
                            bodyFont: { size: 14 },
                            callbacks: {
                                label: (context) => {
                                    return ` ${context.dataset.label}: ${this.formatPeso(context.parsed.y)}`;
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { weight: 'bold' } }
                        },
                        y: {
                            beginAtZero: true,
                            border: { dash: [5, 5] }, // Dotted grid lines
                            grid: { color: '#e2e8f0' },
                            ticks: {
                                callback: (value) => this.formatPeso(value),
                                stepSize: 5000 // Adjust based on your average values
                            },
                        },
                    },
                },
            });
        },
        computeSavingsRate() {
            this.savingsRate = (
                (this.financeSummary.savings / this.financeSummary.income) *
                100
            ).toFixed(1);
        },
        computeDebtToIncomeRatio() {
            this.debtToIncomeRatio = (
                (this.financeSummary.debt_payments /
                    this.financeSummary.income) *
                100
            ).toFixed(1);
        },
    },
    computed: {
        savingsRateDescription() {
            const rate = this.savingsRate;
            if (rate >= 20) {
                return "Excellent savings rate!";
            } else if (rate >= 10) {
                return "Good savings rate.";
            } else if (rate >= 5) {
                return "Average savings rate.";
            } else {
                return "Consider improving your savings.";
            }
        },
        debtToIncomeRatioDescription() {
            const ratio = this.debtToIncomeRatio;
            if (ratio < 21) {
                return "Excellent - low debt burden";
            } else if (ratio < 36) {
                return "Acceptable - manageable debt";
            } else if (ratio < 50) {
                return "Caution - may limit borrowing ability";
            } else if (ratio >= 50) {
                return "Risky - high debt load";
            } else {
                return "";
            }
        },
        formattedDate() {
            return new Date().toLocaleDateString("en-PH", {
                month: "short",
                day: "numeric",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            });
        },
    },
};
</script>

<style scoped>
/* Modern Card Styling */
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-5px); /* Subtle lift effect on hover */
}

.rounded-4 {
    border-radius: 1rem !important;
}

/* Stats Icon Styling */
.stats-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

/* Color Overrides (if using older Bootstrap) */
.bg-primary-subtle {
    background-color: #e7f1ff !important;
}
.bg-success-subtle {
    background-color: #e6fcf5 !important;
}
.bg-danger-subtle {
    background-color: #fff5f5 !important;
}

/* Font size adjustment for the amount */
h2 {
    letter-spacing: -0.5px;
}
</style>