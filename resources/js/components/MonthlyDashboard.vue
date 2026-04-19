<template>
    <div class="container-fluid bg-light">
        <div class="row align-items-center mb-3">
            <div class="col-md-6 mb-3 mb-md-0">
                <h3 class="fw-bold mb-0 text-dark">Monthly Review</h3>
                <p class="text-muted mb-0">
                    Analysis for
                    <span class="text-primary fw-bold">{{
                        formattedMonth
                    }}</span>
                </p>
            </div>
            <!-- Month Input-->
            <div class="col-md-6">
                <div
                    class="card border-1 shadow-sm rounded-pill overflow-hidden"
                >
                    <div class="card-body py-1 px-3">
                        <form
                            @submit.prevent="reloadDashboard"
                            class="row g-2 align-items-center"
                        >
                            <div class="col-auto">
                                <i
                                    class="fa-solid fa-calendar-check text-primary ms-2"
                                ></i>
                            </div>
                            <div
                                class="col text-muted small fw-bold text-uppercase"
                            >
                                Period:
                            </div>
                            <div class="col-auto">
                                <input
                                    type="month"
                                    v-model="monthYear"
                                    class="form-control form-control-sm border-0 bg-transparent fw-bold text-primary"
                                />
                            </div>
                            <div class="col-auto">
                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm hover-up"
                                >
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Charts -->
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-1 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div
                            class="d-flex justify-content-between align-items-center mb-4"
                        >
                            <h5 class="fw-bold mb-0">Cash Flow Analysis</h5>
                            <div class="dropdown">
                                <button
                                    class="btn btn-sm btn-light rounded-circle"
                                    type="button"
                                >
                                    <i
                                        class="fa-solid fa-ellipsis-v text-muted"
                                    ></i>
                                </button>
                            </div>
                        </div>
                        <div class="chart-wrapper">
                            <canvas
                                id="monthlyDashboard"
                                style="position: relative; height: 500px"
                            ></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-lg rounded-4 bg-dark text-white stats-card-gradient">
                            <div class="card-body p-4 animate-fade-up">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small text-uppercase fw-bold opacity-75">Budget Remaining</span>
                                    <i class="fa-solid fa-wallet opacity-50"></i>
                                </div>

                                <div v-if="totalLimit > 0">
                                    <h1 class="fw-bold mb-3 display-6 counter-value">
                                        {{ formatPeso(budgetRemaining) }}
                                    </h1>

                                    <div class="progress bg-white bg-opacity-10 mb-2" style="height: 10px">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                            :style="{
                                                width: budgetUsedPercent + '%',
                                                transition: 'width 1.5s ease-in-out' 
                                            }"
                                        ></div>
                                    </div>

                                    <div class="d-flex justify-content-between extra-small opacity-75 fw-bold">
                                        <span>USED: {{ budgetUsedPercent }}%</span>
                                        <span>LIMIT: {{ formatPeso(totalLimit) }}</span>
                                    </div>
                                </div>
                                <div v-else class="py-4 text-center opacity-75">
                                    <i class="fa-solid fa-circle-info fa-2x mb-2 opacity-50"></i>
                                    <p class="mb-0 fw-medium">No budget setup for this month</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Top Spending Categories-->
                    <div class="col-12">
                        <div class="card border-1 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4 text-uppercase small text-muted tracking-wider">Top Spending Categories</h6>
                                <div v-for="(row, index) in topSpendingCategories" :key="row.category" class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold small text-dark">{{ index + 1 }}. {{ row.category }}</span>
                                        <span class="fw-bold text-primary">{{ formatPeso(row.total_spent) }}</span>
                                    </div>
                                    <div class="progress bg-light" style="height: 6px; overflow: hidden;">
                                        <div class="progress-bar rounded-pill"
                                            :class="row.percentage > 50 ? 'bg-danger' : 'bg-primary'"
                                            :style="{
                                                width: (row.animatedWidth || 0) + '%',
                                                transition: 'width 1.5s cubic-bezier(0.22, 1, 0.36, 1)'
                                            }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Savings & DTI-->
                    <div class="col-6">
                        <div
                            class="card border-0 shadow-sm rounded-4 bg-white border-start border-primary border-4"
                        >
                            <div class="card-body p-3 text-center">
                                <div
                                    class="extra-small fw-bold text-uppercase text-muted mb-1"
                                >
                                    Savings Rate
                                </div>
                                <h3 class="fw-bold text-primary mb-0">
                                    {{ savingsRate }}%
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div
                            class="card border-0 shadow-sm rounded-4 bg-white border-start border-info border-4"
                        >
                            <div class="card-body p-3">
                                <div
                                    class="extra-small fw-bold text-uppercase text-muted mb-1"
                                >
                                    DTI Ratio
                                </div>
                                <h3 class="fw-bold text-info mb-0">
                                    {{ debtToIncomeRatio }}%
                                </h3>
                            </div>
                        </div>
                    </div>
                    <!-- Insights & Recommendations-->
                    <div class="col-12">
                        <div class="card border-1 shadow-sm rounded-4 bg-gradient-insight">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-2 text-primary">
                                    <i class="fa-solid fa-lightbulb me-2"></i>
                                    <h6 class="fw-bold mb-0 text-uppercase small">
                                        Insights & Recommendations
                                    </h6>
                                </div>
                                <p class="mb-0 text-dark small leading-relaxed">
                                    {{ savingsRateDescription }} Based on your
                                    spending, your DTI is currently
                                    <span
                                        class="badge"
                                        :class="
                                            debtToIncomeRatio > 40
                                                ? 'bg-danger-subtle text-danger'
                                                : 'bg-success-subtle text-success'
                                        "
                                    >
                                        {{ debtToIncomeRatioDescription }}
                                    </span>
                                </p>
                            </div>
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
    BarElement,
} from "chart.js";

Chart.register(
    Title,
    Tooltip,
    Legend,
    ArcElement,
    DoughnutController,
    LineController,
    LineElement,
    BarElement
);

export default {
    data() {
        return {
            monthYear: "",
            monthIncome: 0,
            monthExpense: 0,
            monthSavings: 0,
            monthDebtPayments: 0,
            savingsRate: 0,
            debtToIncomeRatio: 0,
            chart: null,
            totalLimit: 0,
            budgetRemaining: 0,
            budgetUsedPercent: 0,
            topSpendingCategories: [],
        };0
    },
    methods: {
        getCurrentMonthYear() {
            const today = new Date();
            const month = String(today.getMonth() + 1).padStart(2, "0");
            const year = today.getFullYear();
            return `${year}-${month}`;
        },
        fetchChartData(monthYear) {
            axios
                .get("/api/monthly-dashboard/data", {
                    params: {
                        monthYear: monthYear,
                    },
                })
                .then((response) => {
                    const data = response.data;
                    this.renderChart(data);
                });
        },
        fetchMonthSummary(monthYear) {
            axios
                .get("/api/monthly-dashboard/summary", {
                    params: {
                        monthYear: monthYear,
                    },
                })
                .then((response) => {
                    const data = response.data;
                    this.monthIncome = data.income;
                    this.monthExpense = data.expense;
                    this.monthSavings = data.savings;
                    this.monthDebtPayments = data.debt_payments;
                    this.computeSavingsRate();
                    this.computeDebtToIncomeRatio();
                });
        },
        fetchBudgetStatus(monthYear) {
            axios
                .get("/api/monthly-dashboard/budget-status", {
                    params: {
                        monthYear: monthYear,
                    },
                })
                .then((response) => {
                    const data = response.data;
                    this.totalLimit = data.total_limit;
                    this.budgetUsedPercent = data.used_percent;
                    this.budgetRemaining = data.remaining_budget;
                });
        },
        fetchTopSpendingCategories(monthYear) {
            axios
                .get("/api/monthly-dashboard/top-spending-categories", {
                    params: { monthYear: monthYear },
                })
                .then((response) => {
                    // 1. Initialize data with animatedWidth at 0
                    this.topSpendingCategories = response.data.map(item => ({
                        ...item,
                        animatedWidth: 0
                    }));

                    // 2. Wait for the DOM to "see" the 0 width, then update to the real percentage
                    // This triggers the CSS 'transition: width' we added to your template
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.topSpendingCategories = this.topSpendingCategories.map(item => ({
                                ...item,
                                animatedWidth: item.percentage
                            }));
                        }, 50); // A tiny delay is enough to trigger the transition
                    });
                })
                .catch(error => console.error("Error fetching top categories:", error));
        },
        reloadDashboard() {
            this.fetchChartData(this.monthYear);
            this.fetchMonthSummary(this.monthYear);
            this.fetchBudgetStatus(this.monthYear);
            this.fetchTopSpendingCategories(this.monthYear);
        },
        renderChart(data) {
            const ctx = document
                .getElementById("monthlyDashboard")
                .getContext("2d");

            if (this.chart) {
                this.chart.destroy();
            }

            this.chart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: "Expenses",
                            data: data.values,
                            backgroundColor: data.colors, // Assuming unique colors per category
                            borderRadius: 8, // Makes the bars look modern
                            borderSkipped: false,
                            barThickness: 25, // Keeps bars consistent
                        },
                    ],
                },
                options: {
                    indexAxis: "y", // <--- Makes it horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }, // Hide legend since labels are on the Y-axis
                        tooltip: {
                            backgroundColor: "#1e293b", // Dark theme tooltip
                            padding: 12,
                            bodyFont: { size: 14 },
                            callbacks: {
                                label: (context) =>
                                    ` Total: ${this.formatPeso(
                                        context.parsed.x
                                    )}`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: "#f1f3f5" }, // Very subtle lines
                            ticks: {
                                callback: (value) => this.formatPeso(value),
                                font: { size: 11 },
                            },
                        },
                        y: {
                            grid: { display: false }, // Clean look: remove horizontal lines
                            ticks: {
                                font: { weight: "bold", size: 12 },
                                color: "#475569",
                            },
                        },
                    },
                    animation: {
                        duration: 1500,
                        easing: "easeOutQuart",
                    },
                },
            });
        },
        computeSavingsRate() {
            if (this.monthIncome === 0) {
                this.savingsRate = 0;
            } else {
                this.savingsRate = (
                    (this.monthSavings / this.monthIncome) *
                    100
                ).toFixed(1);
            }
        },
        computeDebtToIncomeRatio() {
            if (this.monthIncome === 0) {
                this.debtToIncomeRatio = 0;
            } else {
                this.debtToIncomeRatio = (
                    (this.monthDebtPayments / this.monthIncome) *
                    100
                ).toFixed(1);
            }
        },
    },
    mounted() {
        this.monthYear = this.getCurrentMonthYear();
        this.fetchMonthSummary(this.monthYear);
        this.fetchChartData(this.monthYear);
        this.fetchBudgetStatus(this.monthYear);
        this.fetchTopSpendingCategories(this.monthYear);
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
    },
};
</script>

<style scoped>
.rounded-4 {
    border-radius: 1.25rem !important;
}
.extra-small {
    font-size: 0.7rem;
}
.italic {
    font-style: italic;
}

/* Custom light tints */
.bg-primary-subtle {
    background-color: #e7f1ff !important;
}
.bg-info-subtle {
    background-color: #e0f7fa !important;
}

/* Smooth hover for list items */
.card-body .d-flex {
    transition: opacity 0.2s;
}
.card-body .d-flex:hover {
    opacity: 0.8;
}
/* Fix for Month Input appearance */
input[type="month"]::-webkit-inner-spin-button,
input[type="month"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
}
/* Keyframes for the entrance */
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-up {
    animation: fadeUp 0.8s ease-out forwards;
}

/* Optional: Make the number pop slightly when it changes */
.counter-value {
    transition: all 0.3s ease;
}

.extra-small {
    font-size: 0.75rem;
}
</style>