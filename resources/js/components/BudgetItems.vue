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
                    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-muted text-uppercase small mb-0">Budget vs. Actual Performance</h6>
                        <!-- Switch-style Toggle Container -->
                        <!-- Primary Subtle Switch Container -->
                        <div class="bg-primary-subtle rounded-pill p-1 d-inline-flex align-items-center" role="group" aria-label="Chart Scale Selector">
                            <button 
                                type="button" 
                                :class="['btn', 'btn-sm', 'rounded-pill', 'px-3', chartScaleType === 'linear' ? 'btn-primary shadow-sm fw-semibold' : 'btn-link text-primary text-decoration-none']"
                                @click="setChartScale('linear')"
                            >
                                <i class="fa-solid fa-chart-simple me-1"></i>Linear
                            </button>
                            <button 
                                type="button" 
                                :class="['btn', 'btn-sm', 'rounded-pill', 'px-3', chartScaleType === 'logarithmic' ? 'btn-primary shadow-sm fw-semibold' : 'btn-link text-primary text-decoration-none']"
                                @click="setChartScale('logarithmic')"
                            >
                                <i class="fa-solid fa-arrow-up-right-dots me-1"></i>Logarithmic
                            </button>
                        </div>
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
                
                <!-- Total Budget Amount Card -->
                <div class="card border-1 shadow-sm rounded-3 flex-fill">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <!-- Left Content -->
                        <div class="ms-3">
                            <span class="fw-bold text-uppercase small text-muted d-block mb-1">Total Budget Amount</span>
                            <h3 class="fw-bold text-dark mb-1">{{ formatPeso(animatedTotalBudget) }}</h3>

                            <Transition name="fade-slide" mode="out-in">
                                <!-- Skeleton Loader (Shows while loading OR before trend is calculated) -->
                                <div v-if="isChartLoading || activeAnimations > 0 || !budgetTrend" class="placeholder-glow mt-1">
                                    <span class="placeholder col-12 rounded-pill bg-secondary bg-opacity-25 py-2"></span>
                                </div>

                                <!-- Actual Content (Only renders once fully calculated) -->
                                <span v-else :class="['badge', budgetTrend.isPositive ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger', 'rounded-pill px-2 py-1']">
                                    <i :class="[budgetTrend.iconClass, 'me-1']"></i>{{ budgetTrend.text }}
                                </span>
                            </Transition>
                        </div>

                        <!-- Right Icon Container (Font Awesome + Pure Bootstrap) -->
                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 82px; height: 82px;">
                            <i class="fa-solid fa-wallet fs-4"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Expense Card -->
                <div class="card border-1 shadow-sm rounded-3 flex-fill">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <!-- Left Content -->
                        <div class="ms-3">
                            <span class="fw-bold text-uppercase small text-muted d-block mb-1">Total Expenses this month</span>
                            <h3 class="fw-bold text-dark mb-1">{{ formatPeso(animatedTotalExpenses) }}</h3>
                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1">
                                <i class="fa-solid fa-arrow-trend-up me-1"></i>+5% from last month
                            </span>
                        </div>

                        <!-- Right Icon Container (Font Awesome + Pure Bootstrap) -->
                        <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 82px; height: 82px;">
                            <i class="fa-solid fa-credit-card fs-4"></i>
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
                        small
                        class="align-middle border-top"
                        striped
                        thead-class="table-light text-uppercase small fw-bold"
                    >

                        <!-- Custom Cell Template for Budget Amount -->
                        <template #cell(amount)="{ item }">
                            <div class="d-inline-block px-3 py-1 rounded-pill fw-semibold text-end font-monospace bg-primary bg-opacity-10 text-primary">
                                {{ formatPeso(item.amount ?? 0) }}
                            </div>
                        </template>

                        <!-- Category Name -->
                        <template #cell(category.name)="row">
                            <div class="d-flex align-items-center" v-if="row.item.category">
                                <!-- Dynamic Category Icon Circle -->
                                <div 
                                    class="category-icon-sm me-2 d-flex align-items-center justify-content-center rounded-circle text-white shadow-sm flex-shrink-0"
                                    :style="{ 
                                        backgroundColor: row.item.category.color || '#6c757d', 
                                        width: '32px', 
                                        height: '32px' 
                                    }"
                                >
                                    <i :class="row.item.category.icon || 'fa-solid fa-tag'" class="small"></i>
                                </div>
                                
                                <!-- Category Name -->
                                <span class="fw-medium text-dark">{{ row.item.category.name }}</span>
                            </div>

                            <!-- Fallback if Transaction has no assigned category -->
                            <span v-else class="text-muted fst-italic">Uncategorized</span>
                        </template>                        

                        <!-- Custom Cell Template for Tag -->
                        <template #cell(tag)="{ value }">
                            <span 
                                v-if="value" 
                                class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fw-medium font-monospace px-2.5 py-1"
                            >
                                <i class="fa-solid fa-hashtag me-1 opacity-75"></i>{{ value }}
                            </span>
                            <span v-else class="text-muted opacity-50 small">&mdash;</span>
                        </template>

                        <!-- Custom Cell Template for Description -->
                        <template #cell(description)="{ value }">
                            <div v-if="value" class="description-content text-muted" v-html="value"></div>
                            <span v-else class="text-muted fst-italic small">No description provided</span>
                        </template>

                        <template #cell(actions)="row">
                            <div class="d-flex gap-1">
                                <BButton 
                                    size="sm" 
                                    variant="light" 
                                    class="btn-icon rounded-circle bg-warning-subtle border-warning-subtle text-warning-emphasis shadow-sm px-2 py-1" 
                                    @click="edit(row.item.id)"
                                    title="Edit Item"
                                >
                                    <i class="fa-solid fa-pen-to-square small"></i>
                                </BButton>

                                <BButton 
                                    size="sm" 
                                    variant="danger" 
                                    class="btn-icon rounded-circle bg-danger-subtle border-danger-subtle text-danger-emphasis shadow-sm px-2 py-1" 
                                    @click="remove(row.item.id)"
                                    title="Delete Item"
                                >
                                    <i class="fa-solid fa-trash small"></i>
                                </BButton>
                            </div>
                        </template>
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
                                <i class="fa-solid fa-font small"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control border-start-0 shadow-none" 
                                id="item_title" 
                                placeholder="e.g. Office Supplies, Marketing Campaign" 
                                v-model="form.item_name" 
                                required
                            >
                        </div>
                    </div>

                    <!-- Category Field -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark mb-1">
                            Category <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex flex-wrap gap-2">
                            <button 
                                type="button"
                                v-for="cat in categories" 
                                :key="cat.id || cat"
                                class="btn btn-sm rounded-pill d-flex align-items-center gap-1.5 transition-all"
                                :class="form.category_id === (cat.id || cat) ? 'btn-primary shadow-sm' : 'btn-outline-secondary border-opacity-50'"
                                @click="form.category_id = (cat.id || cat)"
                            >
                                <i v-if="cat.icon" :class="cat.icon" class="small"></i>
                                <span>{{ cat.name || cat }}</span>
                            </button>
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
                                v-model="form.amount" 
                                required
                            >
                        </div>
                    </div>

                    <!-- Tag Field (Readonly - Inline Badge) -->
                    <div class="mb-3">
                        <label for="item_tag" class="form-label small fw-semibold text-dark mb-1">
                            Tag <span class="text-muted fw-normal">(Auto-generated)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="fa-solid fa-tag small"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control border-start-0 border-end-0 shadow-none bg-light text-muted" 
                                id="item_tag" 
                                placeholder="auto_generated_tag" 
                                v-model="form.tag"
                                readonly
                            >
                            <!-- Inline Tag Badge Container -->
                            <span class="input-group-text bg-light border-start-0 ps-0">
                                <span v-if="form.tag" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                    <i class="fa-solid fa-hashtag me-1"></i>{{ form.tag }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-dark mb-2">
                            Description <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        
                        <div class="wysiwyg-wrapper border rounded-3 overflow-hidden bg-white">
                            <!-- Toolbar -->
                            <div class="wysiwyg-toolbar bg-light border-bottom p-1.5 d-flex gap-1 flex-wrap align-items-center">
                                <button type="button" class="btn btn-sm btn-light border-0 px-2 py-1 text-secondary" @click="formatText('bold')" title="Bold">
                                    <i class="fa-solid fa-bold"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light border-0 px-2 py-1 text-secondary" @click="formatText('italic')" title="Italic">
                                    <i class="fa-solid fa-italic"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light border-0 px-2 py-1 text-secondary" @click="formatText('underline')" title="Underline">
                                    <i class="fa-solid fa-underline"></i>
                                </button>
                                <div class="vr my-1 opacity-25"></div>
                                <button type="button" class="btn btn-sm btn-light border-0 px-2 py-1 text-secondary" @click="formatText('insertUnorderedList')" title="Bullet List">
                                    <i class="fa-solid fa-list-ul"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light border-0 px-2 py-1 text-secondary" @click="formatText('insertOrderedList')" title="Numbered List">
                                    <i class="fa-solid fa-list-ol"></i>
                                </button>
                            </div>
                            
                            <!-- Editable Content Area -->
                            <div 
                                class="wysiwyg-editor p-3 text-dark fs-7" 
                                contenteditable="true" 
                                style="min-height: 90px; outline: none; overflow-y: auto;"
                                ref="wysiwygEditor"
                                @input="updateDescription"
                            ></div>
                        </div>
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
</template>
<script>
    import DataTable from './Shared/DataTable.vue';
    import ModalForm from './Shared/ModalForm.vue';
    import {
            Chart,Title,Tooltip,Legend,ArcElement,DoughnutController,LineController,LogarithmicScale,LineElement,BarElement,
        } from "chart.js";
    import { ref } from 'vue';
    import { markRaw } from 'vue';

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
                activeAnimations: 0,
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
                lastMonthTotalBudget: 0,
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
                    budget_id:this.$route.query.id,
                    id: null,
                    item_name: null,
                    category_id: null,
                    description: null,
                    amount: null
                },
                perPage: ref(10),
                currentPage: ref(1),
                rows: ref(0),
                filter: ref(''),
                chartScaleType: 'linear', // Set Linear as default
                chartInstance: null,
            }
        },
        watch:{
            totalBudget(newValue, oldValue){
                this.animateCount('animatedTotalBudget', oldValue || 0, newValue, 500);
            },
            totalActual(newValue, oldValue){
                this.animateCount('animatedTotalExpenses', oldValue || 0, newValue, 500);
            },
            'form.item_name': function (newTitle) {
                if (newTitle) {
                    this.form.tag = newTitle
                    .trim()
                    .toLowerCase()
                    .replace(/[\s\W]+/g, '_')
                    .replace(/^_+|_+$/g, '');
                } else {
                    this.form.tag = '';
                }
            },
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
            budgetTrend() {
                // 1. Guard against active loading or animation states
                if (this.isChartLoading || this.activeAnimations > 0) {
                    return null;
                }

                const current = Number(this.totalBudget) || 0;
                const previous = Number(this.lastMonthTotalBudget) || 0;

                // 2. Handle zero baseline gracefully without assuming a false +100% surge
                if (previous === 0) {
                    if (current === 0) {
                        return {
                            isPositive: true,
                            iconClass: 'fa-solid fa-minus',
                            text: '0% from last month'
                        };
                    }
                    // If previous is truly 0 in database and current > 0, show "N/A" or new budget indicator
                    return {
                        isPositive: true,
                        iconClass: 'fa-solid fa-arrow-trend-up',
                        text: 'New this month' // Prevents misleading "+100%" flash
                    };
                }

                // 3. Standard percentage change calculation
                const percentageChange = ((current - previous) / Math.abs(previous)) * 100;
                const isPositive = percentageChange >= 0;
                const formattedPercentage = Math.abs(percentageChange).toFixed(0);

                return {
                    isPositive: isPositive,
                    iconClass: isPositive ? 'fa-solid fa-arrow-trend-up' : 'fa-solid fa-arrow-trend-down',
                    text: `${isPositive ? '+' : '-'}${formattedPercentage}% from last month`
                };
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

                    this.getLastMonthBudget();
                    
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
            getLastMonthBudget(){
                this.fetchItem({
                    url: `/budgets/last-month`,
                    callback: (response) => {
                        this.lastMonthTotalBudget = response.total_budget;
                    }
                })
            },
            setChartScale(scaleType) {
                if (this.chartScaleType === scaleType) return;
                
                this.chartScaleType = scaleType;

                if (this.chartInstance) {
                    this.chartInstance.options.scales.y.type = scaleType;
                    
                    this.chartInstance.options.scales.y.ticks.callback = (value) => {
                        if (scaleType === 'logarithmic') {
                            if (value === 0) return '₱0';
                            if (Math.log10(value) % 1 === 0 || value === 1) {
                                return '₱' + value.toLocaleString();
                            }
                            return null;
                        }
                        return '₱' + value.toLocaleString();
                    };

                    this.chartInstance.update();
                }
            },
            renderChart(data) {
                const canvas = document.getElementById('budgetChart');
                if (!canvas) return;

                const ctx = canvas.getContext('2d');

                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }

                // Wrap the Chart instance with markRaw
                this.chartInstance = markRaw(new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Budget',
                                data: data.budgetValues,
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
                                backgroundColor: '#0f172a',
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
                                    maxRotation: 25,
                                    minRotation: 0,
                                    callback: function(val) {
                                        let label = this.getLabelForValue(val);
                                        return label.length > 15 ? label.substring(0, 12) + '...' : label;
                                    }
                                }
                            },
                            y: {
                                type: this.chartScaleType,
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    color: '#64748b',
                                    callback: (value) => {
                                        if (this.chartScaleType === 'logarithmic') {
                                            if (value === 0) return '₱0';
                                            if (Math.log10(value) % 1 === 0 || value === 1) {
                                                return '₱' + value.toLocaleString();
                                            }
                                            return null;
                                        }
                                        return '₱' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                }));
            },
            animateCount(key, start, end, duration = 1000) {
                this.activeAnimations++;
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
                        this.activeAnimations = Math.max(0, this.activeAnimations - 1);
                    }
                };
                
                requestAnimationFrame(step);
            },
            add(){
                this.isEditing = false;
                this.form = {
                    budget_id: this.$route.query.id,
                    id: null,
                    item_name: null,
                    category_id: null,
                    description: null,
                    amount: null
                }
                if (this.$refs.wysiwygEditor) {
                    this.$refs.wysiwygEditor.innerHTML = null;
                }
                this.openModal('budgetCanvas');
            },
            edit(id){
                this.fetchItem({
                    url: `/api/budget-items/${id}`,
                    errorMessage: 'Failed to retrieve item details.',
                    callback: (response) => {
                        // set form data
                        this.form = response;
                        if (this.$refs.wysiwygEditor) {
                           this.$refs.wysiwygEditor.innerHTML = this.form.description;
                        }


                        this.isEditing = true;
                        this.openModal('budgetCanvas');
                    }
                });
            },
            submitForm(e){
                e.preventDefault();

                this.saveItem({
                    url: this.isEditing ? `/api/budget-items/${this.form.id}` : '/api/budget-items',
                    method: this.isEditing ? 'put' : 'post',
                    data: this.form,
                    successMessage: this.isEditing ? 'Item updated successfully!' : 'Item saved successfully!',
                    errorMessage: this.isEditing ? 'Failed to update item.' : 'Failed to save item.',
                    callback: () => {
                        e.target.reset();
                        this.loadBudgets();
                        this.closeModal('budgetCanvas');

                        this.fetchBudgetItemComparison();
                    }
                })
            },
            remove(id){
                this.deleteItem({
                    url: `/api/budget-items/${id}`,
                    successMessage: 'Item deleted successfully.',
                    errorMessage: 'Failed to delete item.',
                    callback: () => {
                        this.loadBudgets();
                        this.fetchBudgetItemComparison();
                    }
                })
            },
            // WYSIWYG helper functions
            formatText(command) {
                document.execCommand(command, false, null);
                this.updateDescription();
            },
            updateDescription() {
                this.form.description = this.$refs.wysiwygEditor.innerHTML;
            },
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

    /* Fade and subtle slide up transition */
    .fade-slide-enter-active,
    .fade-slide-leave-active {
        transition: opacity 0.25s ease, transform 0.25s ease;
    }

    .fade-slide-enter-from {
        opacity: 0;
        transform: translateY(4px); /* Slightly slides up into place */
    }

    .fade-slide-leave-to {
        opacity: 0;
        transform: translateY(-4px); /* Gently drifts up when leaving */
    }
</style>
