<template>
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 text-primary fw-bold">
                    <i class="fa-solid fa-calculator me-2"></i>{{ budget.budget_name }}
                </h4>
                <nav aria-label="breadcrumb">
                    <small class="text-muted">Manage your monthly budget allocations and track expenses.</small>
                </nav>
            </div>
            <div>
                <a href="/budgets" class="btn btn-outline-secondary me-2">
                    <i class="fa-solid fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>

        <!-- GRAPH & DASHBOARD SECTION -->
        <div class="row mb-4 g-3">
            <!-- Chart Column -->
            <div class="col-12 col-lg-8">
                <div class="card border-1 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h6 class="fw-bold text-muted text-uppercase small mb-0">Budget vs. Actual Performance</h6>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div style="height: 300px;">
                            <canvas id="budgetChart"></canvas> 
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Column -->
            <div class="col-12 col-lg-4 d-flex flex-column gap-3">
                
                <!-- Total Amount Card -->
                <div class="card border-1 shadow-sm rounded-3 flex-fill stat-card-success">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <span class="fw-bold text-uppercase small text-muted">Total Budget Amount</span>
                            <h3 class="fw-bold text-emerald mb-1 mt-1">{{ formatPeso(totalBudget) }}</h3>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">+12% from last month</span>
                        </div>
                        <div class="stat-icon bg-emerald-subtle text-emerald rounded-circle">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Expense Card -->
                <div class="card border-1 shadow-sm rounded-3 flex-fill stat-card-danger">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <span class="fw-bold text-uppercase small text-muted">Total Expenses this month</span>
                            <h3 class="fw-bold text-rose mb-1 mt-1">{{ formatPeso(totalActual) }}</h3>
                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1">+5% from last month</span>
                        </div>
                        <div class="stat-icon bg-rose-subtle text-rose rounded-circle">
                            <i class="bi bi-credit-card fs-4"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- DATA TABLE SECTION -->
        <div class="card shadow-sm">
            <div class="card-body border-1 px-4 pb-4">
                <DataTable
                    :items="budgets"
                    :fields="fields"
                    :utilityUrl="utilityUrl"
                    :module="module"
                    :formatters="formatters"
                    @select-item="selectedItem = $event"
                    @reload-table="loadBudgets"
                    @addFunction="resetSelection"
                    @isEditing="isEdit"
                />
            </div>
        </div>
    </div>

    <ModalForm
        :module="module"
        :formFields="formFields"
        :utilityUrl="utilityUrl"
        :selected-item="selectedItem"
        @reload-table="loadBudgets"
    >
    </ModalForm>
</template>
<script>
    import DataTable from './Shared/DataTable.vue';
    import ModalForm from './Shared/ModalForm.vue';
    import {
            Chart,Title,Tooltip,Legend,ArcElement,DoughnutController,LineController,LineElement,BarElement,
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
        components: { DataTable, ModalForm },
        data() {
            return {
                budgetId: this.$route.query.id || null,
                module: 'budget-item',
                utilityUrl: '/api/budget-items',
                selectedItem: null,
                budget: {},
                budgets: [],
                categories: [],
                categoryOptions: [],
                isEdit: false,
                tags: [],
                totalBudget: 0,
                totalActual: 0,
                fields: [
                    { key: 'item_name', label: 'Title', sortable: true },
                    { key: 'category.name', label: 'Category', sortable: true },
                    // { key: 'sub_category', label: 'Sub Category', sortable: true },
                    { key: 'description', label: 'Description', sortable: true },
                    { key: 'amount', label: 'Budget Amount', sortable: true, class: 'text-end' },
                    { key: 'tag', label: 'Tag' },
                    { key: 'actions', label: 'Actions' },
                ],
                formFields: [
                    { key: 'id', label: 'ID', type: 'input', hidden: true, inputType: "text" },
                    { key: 'budget_id', label: 'Budget ID', type: 'defaultInput', hidden: true, required: true, inputType: "text", value: this.$route.query.id },
                    { key: 'item_name', label: 'Item Name', type: 'input', required: true, inputType: "text" },
                    { key: 'category_id', label: 'Category', type: 'select', required: true, options: [] },
                    // { key: 'sub_category_id', label: 'Sub Category', type: 'select', options: [] },
                    { key: 'amount', label: 'Amount', type: 'input', required: true, inputType:"number" },
                    { key: 'description', label: 'Description', type: 'textarea', required: true, inputType: "text" },
                    { key: 'tag', label: 'Tag', type: 'input', required: false, inputType: "text" },
                ],
                formatters: {
                    amount: (val) => ({ 
                        value: this.formatPeso(val), 
                        class: 'font-monospace text-secondary' 
                    }),
                    tag: (val) => ({
                        value: val ? '#'+val : '-',
                        class: 'font-monospace text-secondary fst-italic'
                    })
                }
            }
        },
        methods: {
            async loadBudgets(){
                let listUrl = '/budget-items/'+this.$route.query.id;
                this.budgets = await this.fetchRecords({ url: listUrl });
            },
            resetSelection(){
                this.selectedItem = {};
            },
            async loadCategories() {
                this.categories = await this.fetchRecords({
                    url: '/api/categories',
                });
            },
            setCategoryOptions(){
                this.categoryOptions = this.buildOptions(this.categories, "name", "id");
            },
            fetchBudgetItemComparison(){
                axios.get(`/budgets/comparison/${this.budgetId}`).then(response => {
                    this.tags = response.data;

                    // Calculate Totals
                    this.totalBudget = this.tags.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
                    this.totalActual = this.tags.reduce((sum, item) => sum + (parseFloat(item.actual) || 0), 0);
                    
                    const chartData = {
                        labels: this.tags.map(item => item.item_name),
                        budgetValues: this.tags.map(item => item.amount),
                        actualValues: this.tags.map(item => item.actual || 0),
                    };
                    this.renderChart(chartData);
                }).catch(error => {
                    console.error('Error fetching tags:', error);
                });
            },
            renderChart(data) {
                const canvas = document.getElementById('budgetChart');
                if (!canvas) return;

                const ctx = canvas.getContext('2d');

                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }

                this.chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Budget',
                                data: data.budgetValues,
                                backgroundColor: '#e2e8f0', // Neutral Gray
                                borderRadius: 6,
                                barPercentage: 0.8,
                            },
                            {
                                label: 'Actual',
                                data: data.actualValues,
                                backgroundColor: (context) => {
                                    const index = context.dataIndex;
                                    const budget = parseFloat(data.budgetValues[index]) || 0;
                                    const actual = parseFloat(data.actualValues[index]) || 0;
                                    return actual > budget ? '#ef4444' : '#3b82f6';
                                },
                                borderRadius: 6,
                                barPercentage: 0.8,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { usePointStyle: true, padding: 20 }
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 12,
                                callbacks: {
                                    // 4. CURRENCY FORMATTING: Shows ₱ instead of just numbers
                                    label: (context) => {
                                        let val = context.parsed.y || 0;
                                        return `${context.dataset.label}: ₱${val.toLocaleString()}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false }, // Clean look
                                ticks: { color: '#64748b' }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    color: '#64748b',
                                    callback: (value) => '₱' + value.toLocaleString()
                                }
                            }
                        }
                    }
                });
            },
         },
        async mounted(){
            this.budget = await this.fetchItem({ url: '/api/budgets/'+this.$route.query.id });
            this.fetchBudgetItemComparison();
            this.loadBudgets();
            await this.loadCategories();
            this.setCategoryOptions(); // format options
            this.formFields[3].options = this.categoryOptions; // add options to category select
        }
    }
</script>
