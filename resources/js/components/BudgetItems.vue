<template>
    <!-- Page Content -->
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
            <h4 class="mb-1 text-primary fw-bold">
                <i class="fa-solid fa-calculator me-2"></i>
                <Transition name="fade" mode="out-in">
                    <span v-if="isDetailLoading" key="skeleton" class="placeholder-glow d-inline-block align-middle" style="width: 70%;">
                        <span class="placeholder col-12 rounded bg-dark-subtle opacity-100"></span>
                    </span>
                    <span v-else key="content" class="d-inline-block align-middle">
                        {{ budget.budget_name }}
                    </span>
                </Transition>
            </h4>
                <nav aria-label="breadcrumb">
                    <small class="text-muted">Manage your monthly budget allocations and track expenses.</small>
                </nav>
            </div>
            <div>
                <a href="/budgets" class="btn btn-sm btn-outline-secondary rounded-pill px-4 me-2 fw-semibold">
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
                        <div style="height: 300px;" class="position-relative">
                            
                            <!-- Skeleton Loader Overlay -->
                            <Transition name="fade">
                                <div 
                                    v-if="isChartLoading" 
                                    class="position-absolute top-0 start-0 w-100 h-100 placeholder-glow d-flex flex-column justify-content-end p-3 bg-white rounded-3 z-3"
                                >
                                    <!-- Simulated Chart Bars -->
                                    <div class="d-flex align-items-end justify-content-between h-100 w-100 gap-3 pb-2 border-bottom">
                                        <span class="placeholder col rounded bg-secondary-subtle opacity-50" style="height: 40%;"></span>
                                        <span class="placeholder col rounded bg-secondary-subtle opacity-50" style="height: 75%;"></span>
                                        <span class="placeholder col rounded bg-secondary-subtle opacity-50" style="height: 55%;"></span>
                                        <span class="placeholder col rounded bg-secondary-subtle opacity-50" style="height: 90%;"></span>
                                        <span class="placeholder col rounded bg-secondary-subtle opacity-50" style="height: 35%;"></span>
                                        <span class="placeholder col rounded bg-secondary-subtle opacity-50" style="height: 65%;"></span>
                                    </div>
                                    <!-- Simulated X-Axis Labels -->
                                    <div class="d-flex justify-content-between w-100 pt-2 gap-2">
                                        <span class="placeholder col rounded bg-secondary-subtle opacity-50 py-1"></span>
                                        <span class="placeholder col rounded bg-secondary-subtle opacity-50 py-1"></span>
                                        <span class="placeholder col rounded bg-secondary-subtle opacity-50 py-1"></span>
                                        <span class="placeholder col rounded bg-secondary-subtle opacity-50 py-1"></span>
                                        <span class="placeholder col rounded bg-secondary-subtle opacity-50 py-1"></span>
                                        <span class="placeholder col rounded bg-secondary-subtle opacity-50 py-1"></span>
                                    </div>
                                </div>
                            </Transition>

                            <!-- Always present in DOM for Chart.js initialization -->
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
                            <h3 class="fw-bold text-emerald mb-1 mt-1">{{ formatPeso(animatedTotalBudget) }}</h3>
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
                            <h3 class="fw-bold text-rose mb-1 mt-1">{{ formatPeso(animatedTotalExpenses) }}</h3>
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
        <div class="card shadow-sm border border-secondary-subtle rounded-4 overflow-hidden">
            <div class="card-body">
                <div class="row g-3 mb-4 align-items-center">
                    <div class="col-md-8 d-flex gap-2">
                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" @click="add()">
                            <i class="fa-solid fa-plus me-2"></i>Add Item
                        </button>
                    </div>
                    <div class="col-md-4 d-flex gap-2 align-items-center">
                        <!-- Search Input Group -->
                        <div class="input-group input-group-sm rounded-pill overflow-hidden shadow-sm">
                            <!-- Icon Prefix (Rounded Start) -->
                            <span class="input-group-text bg-light border-secondary-subtle border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="fa-solid fa-magnifying-glass small"></i>
                            </span>

                            <!-- Search Input (Rounded End) -->
                            <BFormInput
                                v-model="filter"
                                placeholder="Type to Search..."
                                size="sm"
                                class="border-secondary-subtle border-start-0 shadow-none fw-medium text-secondary rounded-end-pill pe-3"
                            />
                        </div>
                    </div>
                </div>
                <div class="table-responsive rounded-3 overflow-hidden border border-secondary-subtle">
                    <BTable
                        :items="budgets"
                        :fields="fields"
                        :filter="filter"
                        show-empty
                        hover
                        class="align-middle border-top"
                        thead-class="table-light text-uppercase small fw-bold"
                    >
                    </BTable>
                    <div class="d-flex justify-content-between align-items-center mb-0 py-3 px-3">
                        <p class="mb-0 text-muted small fw-medium">
                            Showing {{ Number(startRow).toLocaleString() }}–{{ Number(endRow).toLocaleString() }} 
                                of {{ Number(budgets.length).toLocaleString() }} rows
                        </p>
                        <BPagination
                            v-model="currentPage"
                            :total-rows="budgets.length"
                            :per-page="perPage"
                            align="end"
                            size="sm"
                            class="mb-0 custom-rounded-pagination"
                            first-text="⏮"
                            prev-text="Prev"
                            next-text="Next"
                            last-text="⏭"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Canvas -->
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="budgetCanvas" style="width: 500px;">
        <!-- Drawer Header -->
        <div class="offcanvas-header border-bottom py-3 px-4 bg-body-tertiary">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="fa-solid fa-pen-to-square fs-5"></i>
                </div>
                <div>
                    <h5 class="offcanvas-title fw-bold text-dark mb-0" id="budgetCanvasLabel">
                        {{ form.id ? 'Edit' : 'Create' }} Item
                    </h5>
                    <p class="text-muted small mb-0">Fill in the details below to save your budget item.</p>
                </div>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <!-- Form & Body Structure -->
        <form @submit.prevent="submitForm" novalidate class="d-flex flex-column h-100 mb-0 overflow-hidden">
            
            <!-- Scrollable Body Container -->
            <div class="offcanvas-body p-4 flex-grow-1" style="overflow-y: auto; min-height: 0;">
                
                <!-- Section 1: Basic Details -->
                <div class="mb-4">
                    <label class="form-label text-uppercase text-secondary fw-bold fs-7 mb-2">Item Information</label>
                    
                    <!-- Title Field -->
                    <div class="mb-3">
                        <label for="item_title" class="form-label small fw-semibold text-dark mb-1">
                            Title <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="fa-solid fa-heading small"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control border-start-0 shadow-none" 
                                id="item_title" 
                                placeholder="e.g. Office Supplies, Marketing Campaign" 
                                v-model="form.title" 
                                required
                            >
                        </div>
                    </div>

                    <!-- Category Field -->
                    <div class="mb-3">
                        <label for="item_category" class="form-label small fw-semibold text-dark mb-1">
                            Category <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="fa-solid fa-layer-group small"></i>
                            </span>
                            <select 
    class="form-select border-start-0 shadow-none text-secondary" 
    id="item_category" 
    v-model="form.category" 
    required
>
    <option value="" disabled selected>Select a category...</option>
    <option 
        v-for="category in categories" 
        :key="category.id || category" 
        :value="category.value || category"
    >
        {{ category.name }}
    </option>
</select>
                        </div>
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25" />

                <!-- Section 2: Budget Allocation -->
                <div class="mb-4">
                    <label class="form-label text-uppercase text-secondary fw-bold fs-7 mb-2">Allocation & Metadata</label>

                    <!-- Budget Amount Field with Currency Prefix -->
                    <div class="mb-3">
                        <label for="item_budget_amount" class="form-label small fw-semibold text-dark mb-1">
                            Budget Amount <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-dark border-end-0">₱</span>
                            <input 
                                type="number" 
                                step="0.01" 
                                class="form-control border-start-0 font-monospace fs-6 shadow-none" 
                                id="item_budget_amount" 
                                placeholder="0.00" 
                                v-model="form.budget_amount" 
                                required
                            >
                        </div>
                    </div>

                    <!-- Tag Field -->
                    <div class="mb-3">
                        <label for="item_tag" class="form-label small fw-semibold text-dark mb-1">
                            Tag <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="fa-solid fa-tag small"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control border-start-0 shadow-none" 
                                id="item_tag" 
                                placeholder="e.g. Monthly, Priority, Q3" 
                                v-model="form.tag"
                            >
                        </div>
                        <!-- Live Tag Preview Badge -->
                        <div v-if="form.tag" class="mt-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                <i class="fa-solid fa-hashtag me-1"></i>{{ form.tag }}
                            </span>
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div>
                        <label for="item_description" class="form-label small fw-semibold text-dark mb-1">
                            Description <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <textarea 
                            class="form-control shadow-none p-2.5" 
                            id="item_description" 
                            rows="3" 
                            placeholder="Add notes, justification, or additional details..." 
                            v-model="form.description"
                        ></textarea>
                    </div>
                </div>

            </div>

            <!-- Fixed Footer Actions -->
            <div class="offcanvas-footer p-3 border-top bg-body-tertiary d-flex align-items-center justify-content-end gap-2 flex-shrink-0">
                <button 
                    type="button" 
                    class="btn btn-outline-secondary rounded-pill px-4 fw-semibold border-0" 
                    data-bs-dismiss="offcanvas"
                >
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ form.id ? 'Update Item' : 'Save Item' }}</span>
                </button>
            </div>
        </form>
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
            Chart,Title,Tooltip,Legend,ArcElement,DoughnutController,LineController,LogarithmicScale,LineElement,BarElement,
        } from "chart.js";
    import { ref } from 'vue';

    Chart.register(
        Title,
        Tooltip,
        Legend,
        ArcElement,
        DoughnutController,
        LineController,
        LogarithmicScale,
        LineElement,
        BarElement
    );

    export default {
        components: { DataTable, ModalForm },
        data() {
            return {
                isDetailLoading: true,
                isChartLoading: true,
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
                animatedTotalBudget: 0,
                totalActual: 0,
                animatedTotalExpenses: 0,
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
                    { key: 'item_name', label: 'Item Name', type: 'input', required: true, inputType: "text", placeholder: 'e.g. Groceries, Rent, Utilities' },
                    { key: 'category_id', label: 'Category', type: 'select', required: true, options: [], placeholder: 'Select a category' },
                    // { key: 'sub_category_id', label: 'Sub Category', type: 'select', options: [] },
                    { key: 'amount', label: 'Amount', type: 'input', required: true, inputType:"number", placeholder: 'e.g. 1000, 500, 250' },
                    { key: 'description', label: 'Description', type: 'textarea', required: true, inputType: "text", placeholder: 'e.g. Monthly grocery expenses, Rent for apartment, Utility bills' },
                    { key: 'tag', label: 'Tag', type: 'input', required: false, inputType: "text", placeholder: 'e.g. #groceries, #rent, #utilities' },
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
                },
                form:{
                    id: null,
                    title: null,
                    category: null,
                    description: null,
                    amount: null
                },
                perPage: ref(10),
                currentPage: ref(1),
                rows: ref(0),
                filter: ref(''),
            }
        },
        watch:{
            totalBudget(newValue, oldValue){
                this.animateCount('animatedTotalBudget', oldValue || 0, newValue, 1500);
            },
            totalActual(newValue, oldValue){
                this.animateCount('animatedTotalExpenses', oldValue || 0, newValue, 1500);
            }
        },
        computed: {
            paginatedItems() {
                const start = (this.currentPage - 1) * this.perPage.value;
                const end = start + this.perPage.value;
                return this.budgets.slice(start, end);
            },
            startRow() {
                return this.budgets.length === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
            },
            endRow() {
                return Math.min(this.currentPage * this.perPage, this.budgets.length)
            },
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
                    this.isChartLoading = false;
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
                                // Bootstrap 5 $gray-300 / subtle border gray
                                backgroundColor: '#e9ecef',
                                borderColor: '#ced4da',
                                borderWidth: 1,
                                borderRadius: 6,
                                barPercentage: 0.8,
                                minBarLength: 6,
                            },
                            {
                                label: 'Actual',
                                data: data.actualValues,
                                // Subtle Bootstrap 5 Danger (#f8d7da) vs Primary/Info (#cff4fc or #cfe2ff)
                                backgroundColor: (context) => {
                                    const index = context.dataIndex;
                                    const budget = parseFloat(data.budgetValues[index]) || 0;
                                    const actual = parseFloat(data.actualValues[index]) || 0;
                                    return actual > budget ? 'rgba(248, 215, 218, 0.85)' : 'rgba(207, 226, 255, 0.85)';
                                },
                                borderColor: (context) => {
                                    const index = context.dataIndex;
                                    const budget = parseFloat(data.budgetValues[index]) || 0;
                                    const actual = parseFloat(data.actualValues[index]) || 0;
                                    return actual > budget ? '#f5c2c7' : '#9ec5fe';
                                },
                                borderWidth: 1,
                                borderRadius: 6,
                                barPercentage: 0.8,
                                minBarLength: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 600,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { usePointStyle: true, padding: 20 }
                            },
                            tooltip: {
                                backgroundColor: '#0f172a', // Solid Slate 900
                                titleColor: '#ffffff',
                                bodyColor: '#f8fafc',
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: (context) => {
                                        let val = context.parsed.y || 0;
                                        return `${context.dataset.label}: ₱${val.toLocaleString()}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    maxRotation: 25, // Soft angle instead of steep tilt
                                    minRotation: 0,
                                    callback: function(val, index) {
                                        let label = this.getLabelForValue(val);
                                        return label.length > 15 ? label.substring(0, 12) + '...' : label;
                                    }
                                }
                            },
                            y: {
                                type: 'logarithmic', // <--- Key change: logarithmic scale
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    color: '#64748b',
                                    callback: (value) => {
                                        // Filter log ticks to keep numbers readable (1, 10, 100, 1k, 10k, 100k)
                                        if (value === 0) return '₱0';
                                        if (Math.log10(value) % 1 === 0 || value === 1) {
                                            return '₱' + value.toLocaleString();
                                        }
                                        return null;
                                    }
                                }
                            }
                        }
                    }
                });
            },
            animateCount(key, start, end, duration = 1000) {
                const startTime = performance.now();
                
                const step = (currentTime) => {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    // Ease-out cubic formula for smooth deceleration
                    const easeOutProgress = 1 - Math.pow(1 - progress, 3);
                    
                    this[key] = start + (end - start) * easeOutProgress;
                    
                    if (progress < 1) {
                        requestAnimationFrame(step);
                    } else {
                        this[key] = end; // Ensure exact final value
                    }
                };
                
                requestAnimationFrame(step);
            },
            add(){
                this.isEditing = false;
                this.form = {
                    id: null,
                    title: null,
                    category: null,
                    description: null,
                    amount: null
                }
                this.openModal('budgetCanvas');
            }
        },
        async mounted(){
            this.budget = await this.fetchItem({ url: '/api/budgets/'+this.$route.query.id });
            this.isDetailLoading = false;

            this.fetchBudgetItemComparison();
            this.loadBudgets();
            await this.loadCategories();
            this.setCategoryOptions(); // format options
            this.formFields[3].options = this.categoryOptions; // add options to category select
        }
    }
</script>

<style scoped>
    /* Vue Transition Fade Effect */
    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.35s ease-in-out;
    }

    .fade-enter-from,
    .fade-leave-to {
        opacity: 0;
    }
</style>
