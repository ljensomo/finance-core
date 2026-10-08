<template>
    <!-- Page Content -->
    <div class="container-fluid">
        <div class="row g-3 mb-3">
            <!-- Expenses Card (This Month + Overall) -->
            <div class="col-md-6 col-lg-3">
                <div class="card border border-secondary-subtle rounded-4 shadow-sm h-100">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-medium text-uppercase">Total Expense</span>
                                <div class="icon-box bg-danger-subtle text-danger px-2 py-1 rounded-circle">
                                    <i class="fa-solid fa-arrow-trend-down small"></i>
                                </div>
                            </div>

                            <transition name="fade-metric" mode="out-in">
                                <!-- Main Metric (Current Month) -->
                                <div v-if="isMetricsLoading" class="placeholder-glow my-1">
                                    <span class="placeholder col-8 bg-secondary rounded py-3"></span>
                                </div>
                                <h3 v-else class="fw-bold mb-1 font-monospace text-danger">
                                    {{ formatPeso(animatedCurrentMonthExpense) }}
                                </h3>
                            </transition>

                            <p class="text-muted small mb-0">This month</p>
                        </div>

                        <!-- Secondary Metric (Overall) -->
                        <div class="border-top pt-2 mt-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted extra-small">Overall Expenses:</span>

                            <transition name="fade-metric" mode="out-in">
                                <div v-if="isMetricsLoading" class="placeholder-glow w-25 text-end">
                                    <span class="placeholder col-12 bg-secondary rounded"></span>
                                </div>
                                <span v-else class="fw-semibold font-monospace text-secondary small">
                                    {{ formatPeso(animatedTotalExpense) }}
                                </span>
                            </transition>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Income Card (This Month + Overall) -->
            <div class="col-md-6 col-lg-3">
                <div class="card border border-secondary-subtle rounded-4 shadow-sm h-100">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-medium text-uppercase">Total Income</span>
                                <div class="icon-box bg-success-subtle text-success px-2 py-1 rounded-circle">
                                    <i class="fa-solid fa-arrow-trend-up small"></i>
                                </div>
                            </div>
                            <!-- Main Metric (Current Month) -->
                            <transition name="fade-metric" mode="out-in">
                                <div v-if="isMetricsLoading" class="placeholder-glow my-1">
                                    <span class="placeholder col-8 bg-secondary rounded py-3"></span>
                                </div>
                                <h3 v-else class="fw-bold mb-1 font-monospace text-success">
                                    {{ formatPeso(animatedCurrentMonthIncome) }}
                                </h3>
                            </transition>
                            <p class="text-muted small mb-0">This month</p>
                        </div>

                        <!-- Secondary Metric (Overall) -->
                        <div class="border-top pt-2 mt-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted extra-small">Overall Income:</span>
                            <transition name="fade-metric" mode="out-in">
                                <div v-if="isMetricsLoading" class="placeholder-glow w-25 text-end">
                                    <span class="placeholder col-12 bg-secondary rounded"></span>
                                </div>
                                <span v-else class="fw-semibold font-monospace text-secondary small">
                                    {{ formatPeso(animatedTotalIncome) }}
                                </span>
                            </transition>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Savings / Cash Flow Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card border border-secondary-subtle rounded-4 shadow-sm h-100">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-medium text-uppercase">Net Balance</span>
                                <div class="icon-box bg-primary-subtle text-primary px-2 py-1 rounded-circle">
                                    <i class="fa-solid fa-wallet small"></i>
                                </div>
                            </div>
                            <!-- Main Metric (Current Month Net) -->
                            <transition name="fade-metric" mode="out-in">
                                <div v-if="isMetricsLoading" class="placeholder-glow my-1">
                                    <span class="placeholder col-8 bg-secondary rounded py-3"></span>
                                </div>
                                <h3 v-else class="fw-bold mb-1 font-monospace" :class="(animatedCurrentMonthIncome - animatedCurrentMonthExpense) >= 0 ? 'text-primary' : 'text-danger'">
                                    {{ formatPeso(animatedCurrentMonthIncome - animatedCurrentMonthExpense) }}
                                </h3>
                            </transition>
                            <p class="text-muted small mb-0">This month net</p>
                        </div>

                        <!-- Secondary Metric (Overall Net) -->
                        <div class="border-top pt-2 mt-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted extra-small">Overall Balance:</span>
                            <transition name="fade-metric" mode="out-in">
                                <div v-if="isMetricsLoading" class="placeholder-glow w-25 text-end">
                                    <span class="placeholder col-12 bg-secondary rounded"></span>
                                </div>
                                <span v-else class="fw-semibold font-monospace text-secondary small">
                                    {{ formatPeso(animatedTotalIncome - animatedTotalExpense) }}
                                </span>
                            </transition>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graph Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card border border-secondary-subtle rounded-4 shadow-sm h-100">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-medium text-uppercase">Expense Trend</span>
                            <i class="fa-solid fa-chart-line text-primary small"></i>
                        </div>
                        
                        <!-- Graph Placeholder Container -->
                        <div class="d-flex align-items-center justify-content-center flex-grow-1 py-2">
                            <!-- Chart Component Goes Here -->
                            <div class="text-center text-muted">
                                <i class="fa-solid fa-chart-column fa-xl text-primary-subtle mb-1"></i>
                                <span class="d-block extra-small text-secondary">Mini Graph</span>
                            </div>
                        </div>

                        <div class="border-top pt-2 mt-1 d-flex justify-content-between align-items-center">
                            <span class="text-muted extra-small">Last 6 Months</span>
                            <span class="badge bg-light text-secondary border extra-small">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm border border-secondary-subtle rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary-subtle text-primary me-3 px-3 py-2 rounded-pill">
                            <i class="fa-solid fa-file-invoice"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Transactions</h5>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3 mb-4 align-items-center">
                            <div class="col-md-6 d-flex gap-2">
                                <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" @click="add('transactionModal')"><i class="fa-solid fa-plus me-2"></i>Add Transaction</button>
                                <button 
                                    class="btn btn-sm bg-primary-subtle text-primary border border-primary-subtle fw-medium rounded-pill px-3 shadow-sm btn-disabled-soft" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#importModal"
                                    disabled
                                >
                                    <i class="fa-solid fa-file-import me-2"></i>Import Transactions
                                </button>
                                <button 
                                    class="btn btn-sm bg-primary-subtle text-primary border border-primary-subtle fw-medium rounded-pill px-3 shadow-sm" 
                                    @click="syncTransactions"
                                >
                                    <i class="fa-solid fa-arrows-rotate me-2"></i>Sync Google Sheet Transactions
                                </button>
                            </div>
                            <div class="col-md-6 d-flex gap-2 align-items-center">
                                <!-- Category Select -->
                                <div class="input-group input-group-sm filter-dropdown-group rounded-pill overflow-hidden shadow-sm">
                                    <!-- Icon Prefix (Rounded Start) -->
                                    <span class="input-group-text bg-light border-secondary-subtle text-muted rounded-start-pill ps-3">
                                        <i class="fa-solid fa-filter small"></i>
                                    </span>

                                    <!-- Select Element (Rounded End) -->
                                    <BFormSelect 
                                        v-model="selectedBudget" 
                                        size="sm" 
                                        class="border-secondary-subtle border-start-0 shadow-none fw-medium text-secondary rounded-end-pill pe-4"
                                    >
                                        <option value="">All Budgets</option>
                                        <option v-for="budget in budgets" :key="budget.id" :value="budget.id">
                                            {{ budget.budget_name }}
                                        </option>
                                    </BFormSelect>
                                </div>

                                <!-- Category Select -->
                                <div class="input-group input-group-sm filter-dropdown-group rounded-pill overflow-hidden shadow-sm">
                                    <!-- Icon Prefix (Rounded Start) -->
                                    <span class="input-group-text bg-light border-secondary-subtle text-muted rounded-start-pill ps-3">
                                        <i class="fa-solid fa-filter small"></i>
                                    </span>
                                    <BFormSelect 
                                        v-model="selectedCategory" 
                                        size="sm" 
                                        class="border-secondary-subtle border-start-0 shadow-none fw-medium text-secondary rounded-end-pill pe-4"
                                    >
                                        <option value="">All Categories</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </BFormSelect>
                                </div>

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
                                :items="transactions"
                                :fields="fields"
                                :per-page="perPage"
                                :current-page="currentPage"
                                :filter="filter"
                                :busy="isTableLoading"
                                striped
                                small
                                hover
                                class="align-middle border-top"
                                thead-class="table-light text-uppercase small fw-bold"
                            >
                                <template #table-busy>
                                    <div class="text-center text-primary my-4 py-3">
                                        <!-- Standard HTML Bootstrap Spinner fallback -->
                                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                        <span class="fw-medium text-secondary">Loading transactions...</span>
                                    </div>
                                </template>
                                <template #cell(type)="row">
                                    <span v-if="row.item.type == 1" class="badge rounded-pill bg-success-subtle text-success px-3">
                                    <i class="fa-solid fa-circle-arrow-down me-1"></i> Income
                                    </span>
                                    <span v-else class="badge rounded-pill bg-danger-subtle text-danger px-3">
                                    <i class="fa-solid fa-circle-arrow-up me-1"></i> Expense
                                    </span>
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

                                <!-- Budget Source -->
                                <template #cell(budget.budget_name)="row">
                                    <!-- Income Transaction (No budget allocation applies) -->
                                    <template v-if="row.item.type === 1 || row.item.category?.type === 1">
                                        <span class="badge rounded-pill px-3 py-1 fw-medium bg-success-subtle text-success border border-success-subtle">
                                            <i class="fa-solid fa-hand-holding-dollar me-1"></i>
                                            Income Source
                                        </span>
                                    </template>

                                    <!-- Expense Transaction (Budget validation & display) -->
                                    <template v-else>
                                        <span :class="[
                                            'badge rounded-pill px-3 py-1 fw-medium',
                                            row.item.budget 
                                                ? 'bg-primary-subtle text-primary border border-primary-subtle' 
                                                : 'bg-light text-muted border'
                                        ]">
                                            <i class="fa-solid" :class="row.item.budget ? 'fa-wallet me-1' : 'fa-circle-question me-1'"></i>
                                            {{ row.item.budget ? row.item.budget.budget_name : 'Unallocated' }}
                                        </span>
                                    </template>
                                </template>

                                <template #cell(budget_item.tag)="row">
                                    <span :class="[
                                        'badge rounded-pill px-3 py-1 fw-normal',
                                        row.item.budget_item ? 'bg-secondary-subtle text-secondary border border-secondary-subtle font-monospace fst-italic' : 'text-muted small opacity-50'
                                    ]">
                                        {{ row.item.budget_item ? '#' + row.item.budget_item.tag : '—' }}
                                    </span>
                                </template>

                                <template #cell(amount)="row">
                                    <div 
                                        class="px-2 py-1 rounded fw-semibold text-end font-monospace"
                                        :class="row.item.type === 1 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis'"
                                    >
                                        <span :class="row.item.type == 1 ? 'text-success' : 'text-danger'" class="font-monospace fw-semibold">
                                            {{ row.item.type == 1 ? '+' : '-' }}{{ formatPeso(row.item.amount) }}
                                        </span>
                                    </div>
                                </template>

                                <template #cell(actions)="row">
                                    <div class="d-flex gap-1">
                                        <BButton 
                                            size="sm" 
                                            variant="light" 
                                            class="btn-icon rounded-circle bg-warning-subtle border-warning-subtle text-warning-emphasis shadow-sm px-2 py-1" 
                                            @click="fetchTransaction(row.item.id)"
                                            title="Edit Transaction"
                                        >
                                            <i class="fa-solid fa-pen-to-square small"></i>
                                        </BButton>

                                        <BButton 
                                            size="sm" 
                                            variant="danger" 
                                            class="btn-icon rounded-circle bg-danger-subtle border-danger-subtle text-danger-emphasis shadow-sm px-2 py-1" 
                                            @click="deleteTransaction(row.item.id)"
                                            title="Delete Transaction"
                                        >
                                            <i class="fa-solid fa-trash small"></i>
                                        </BButton>
                                    </div>
                                </template>
                            </BTable>

                            <div class="d-flex justify-content-between align-items-center mb-0 py-3 px-3">
                                <p class="mb-0 text-muted small fw-medium">
                                    Showing {{ Number(startRow).toLocaleString() }}–{{ Number(endRow).toLocaleString() }} 
                                        of {{ Number(transactions.length).toLocaleString() }} rows
                                </p>
                                <BPagination
                                    v-model="currentPage"
                                    :total-rows="transactions.length"
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
        </div>
    </div>

    <!-- Transaction Canvas -->
    <div class="offcanvas offcanvas-end border-0 shadow" tabindex="-1" id="transactionModal" style="width: 550px;">
        
        <!-- Drawer Header -->
        <div class="offcanvas-header border-bottom py-3 px-4 flex-shrink-0">
            <h5 class="offcanvas-title fw-bolder d-flex align-items-center mb-0" id="transactionModalLabel">
                <i class="fa-solid fa-money-bill-transfer me-3 fs-4" 
                :class="form.type == '1' ? 'text-success' : 'text-danger'"></i>
                <span class="text-dark">{{ form.id ? 'Edit' : 'New' }} Transaction</span>
            </h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <!-- Form & Body Structure -->
        <form @submit.prevent="submitForm" novalidate class="d-flex flex-column h-100 mb-0 overflow-hidden">
            
            <!-- Scrollable Body Container -->
            <div class="offcanvas-body p-4 flex-grow-1" style="overflow-y: auto; min-height: 0;">
                
                <!-- Type Switcher -->
                <div class="mb-4">
                    <div class="btn-group w-100 rounded-pill overflow-hidden border p-1 bg-light" role="group" aria-label="Transaction Type">
                        <input type="radio" checked class="btn-check" name="type" id="expense" value="2" v-model="form.type" @change="loadCategories" required>
                        <label 
                            class="btn border-0 py-3 fw-bold d-flex align-items-center justify-content-center text-danger rounded-pill" 
                            :class="form.type == 2 ? 'bg-danger-subtle border border-danger-subtle shadow-sm' : 'bg-transparent opacity-50'" 
                            for="expense">
                                <i class="fa-solid fa-circle-arrow-down me-2"></i> Expense
                        </label>

                        <input type="radio" class="btn-check" name="type" id="income" value="1" v-model="form.type" @change="loadCategories">
                        <label 
                            class="btn border-0 py-3 fw-bold d-flex align-items-center justify-content-center text-success rounded-pill" 
                            :class="form.type == 1 ? 'bg-success-subtle border border-success-subtle shadow-sm' : 'bg-transparent opacity-50'" 
                            for="income">
                                <i class="fa-solid fa-circle-arrow-up me-2"></i> Income
                        </label>
                    </div>
                </div>

                <!-- Amount Input -->
                <div class="mb-4 text-center amount-container p-3 rounded-3 bg-light border">
                    <label for="amount" class="form-label small text-uppercase fw-bold text-muted mb-1">Amount</label>
                    <div class="input-group input-group-lg justify-content-center">
                        <span class="input-group-text bg-transparent border-0 border-bottom fs-1 fw-black"
                            :class="form.type == '1' ? 'text-success' : 'text-danger'">₱</span>
                        <input 
                            type="number" 
                            class="form-control bg-transparent border-0 border-bottom ps-1 fs-1 fw-black shadow-none text-center" 
                            :class="form.type == '1' ? 'text-success' : 'text-danger'"
                            id="amount" 
                            step="0.01" 
                            placeholder="0.00" 
                            v-model="form.amount" 
                            required
                            style="max-width: 200px;"
                        >
                    </div>
                </div>

                <!-- Description & Date Inputs -->
                <div class="row g-2 mb-4">
                    <div class="col-7">
                        <div class="form-floating custom-floating">
                            <input 
                                type="text" 
                                class="form-control border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring" 
                                :class="form.type == '1' ? 'focus-ring-success' : 'focus-ring-danger'"
                                id="description" 
                                placeholder="What was this for?" 
                                v-model="form.description" 
                                required
                            >
                            <label for="description" class="text-muted ps-2 pt-2">Description</label>
                        </div>
                    </div>

                    <div class="col-5">
                        <div class="form-floating custom-floating">
                            <input 
                                type="date" 
                                class="form-control border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none" 
                                id="date" 
                                placeholder="YYYY-MM-DD" 
                                v-model="form.date" 
                                required
                            >
                            <label for="date" class="text-muted ps-2 pt-2">Date</label>
                        </div>
                    </div>
                </div>

                <!-- Category Grid -->
                <div class="mb-4">
                    <label class="form-label small text-uppercase fw-bold text-muted d-block mb-1">Category</label>
                    <div class="interactive-scroll-container p-2 border rounded-3 bg-white" style="max-height: 160px; overflow-y: auto;">
                        <div class="row g-2">
                            <div class="col-4" v-for="category in categories" :key="category.id">
                                <button 
                                    type="button" 
                                    class="btn btn-sm w-100 py-2 px-1 border rounded-3 text-center transition-all d-flex flex-column align-items-center justify-content-center option-card"
                                    :class="form.category_id === category.id 
                                        ? (form.type == '1' ? 'bg-success-subtle text-success border-success-subtle shadow-sm' : 'bg-danger-subtle text-danger border-danger shadow-sm') 
                                        : 'btn-light text-dark border-0 bg-light'"
                                    @click="form.category_id = category.id"
                                >
                                    <i :class="[category.icon || 'fa-solid fa-layer-group', 'fs-6 mb-1']"></i>
                                    <span class="small text-truncate w-100 fw-medium" style="font-size: 0.72rem;">
                                        {{ category.name }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Budget Source -->
                <div class="mb-4">
                    <label class="form-label small text-uppercase fw-bold text-muted d-block mb-1">Budget Source</label>
                    <div class="interactive-scroll-container p-2 border rounded-3 bg-white" style="max-height: 140px; overflow-y: auto;">
                        <div class="d-flex flex-column gap-1">
                            <button 
                                type="button" 
                                v-for="budget in budgets" 
                                :key="budget.id"
                                class="btn btn-sm w-100 py-2 px-3 border rounded-3 text-start transition-all d-flex align-items-center gap-2 option-card shadow-none"
                                :class="form.budget_id === budget.id 
                                    ? (form.type == '1' ? 'bg-success-subtle text-success border-success-subtle shadow-sm' : 'bg-danger-subtle text-danger border-danger shadow-sm') 
                                    : 'btn-light text-dark border-0 bg-light'"
                                @click="selectBudget(budget.id)"
                            >
                                <i class="fa-solid fa-wallet fs-6"></i>
                                <span class="small text-truncate fw-medium" style="font-size: 0.75rem;">
                                    {{ budget.budget_name }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Specific Tag -->
                <div class="mb-3" v-if="tags && tags.length">
                    <label class="form-label small text-uppercase fw-bold text-muted d-block mb-1">Specific Tag</label>
                    <div class="d-flex flex-wrap gap-2 p-2 border rounded-3 bg-white" style="max-height: 120px; overflow-y: auto;">
                        <button 
                            type="button" 
                            v-for="tag in tags" 
                            :key="tag.id"
                            class="btn btn-sm rounded-pill px-3 py-1 transition-all border d-flex align-items-center gap-1"
                            :class="form.budget_item_id === tag.id 
                                ? (form.type == '1' ? 'bg-success-subtle text-success border-success-subtle shadow-sm' : 'bg-danger-subtle text-danger border-danger shadow-sm') 
                                : 'btn-light text-secondary border-0 bg-light'"
                            @click="form.budget_item_id = tag.id"
                        >
                            <span class="fw-bold" style="font-size: 0.75rem;">#{{ tag.tag }}</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Fixed Sticky Footer -->
            <div class="offcanvas-footer p-3 border-top bg-light d-flex align-items-center justify-content-end gap-2 flex-shrink-0 mt-auto">
                <button 
                    type="button" 
                    class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" 
                    data-bs-dismiss="offcanvas"
                >
                    Cancel
                </button>
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-check me-1"></i>
                    Save
                </button>
            </div>
        </form>
    </div>

    <!-- Import modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="submitFile">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">File</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-control" type="file" name="file" id="file" accept=".csv, text/csv" @change="onFileChange" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import Transaction(s)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <LoadingModal :visible="isLoading"></LoadingModal>
</template>

<script>
    import LoadingModal from './Shared/LoadingModal.vue';
    import { ref } from 'vue'

    export default {
        components: {
            LoadingModal
        },
        mounted() {
            this.fetchTransactions();
            this.loadCategories();
            this.fetchBudgets();
        },
        data() {
            return {
                // isloading vars
                isLoading: false,
                isTableLoading: true,
                isMetricsLoading: true,
                isEditing: false,
                fields: [
                    { key: 'type', label: 'Type', sortable: true },
                    { key: 'formatted_date', label: 'Date', sortable: true },
                    { key: 'description', label: 'Description', sortable: true },
                    { key: 'amount', label: 'Amount', sortable: true, class: "text-end" },
                    { key: 'category.name', label: 'Category', sortable: true },
                    { key: 'budget.budget_name', label: 'Budget Source', sortable: true },
                    { key: 'budget_item.tag', label: 'Tag', sortable: true },
                    { key: 'actions', label: 'Actions' }
                ],
                perPage: ref(10),
                currentPage: ref(1),
                rows: ref(0),
                filter: ref(''),
                transactions: [],
                categories: [],
                budgets: [],
                tags: [],
                form: {
                    id: null,
                    type: 2,
                    date: null,
                    description: '',
                    amount: null,
                    category_id: null,
                    budget_id: null,
                    budget_item_id: null,
                },
                file: null,
                // dashboard metrics
                currentMonthIncome: 0,
                currentMonthExpense: 0,
                totalIncome: 0,
                totalExpense: 0,
                // animations
                activeAnimations: 0,
                animatedCurrentMonthIncome: 0,
                animatedCurrentMonthExpense: 0,
                animatedTotalIncome: 0,
                animatedTotalExpense: 0,
            };
        },
        watch:{
            currentMonthIncome(newVal, oldVal) {
                this.animateCount('animatedCurrentMonthIncome', oldVal, newVal);
            },
            currentMonthExpense(newVal, oldVal) {
                this.animateCount('animatedCurrentMonthExpense', oldVal, newVal);
            },
            totalIncome(newVal, oldVal) {
                this.animateCount('animatedTotalIncome', oldVal, newVal);
            },
            totalExpense(newVal, oldVal) {
                this.animateCount('animatedTotalExpense', oldVal, newVal);
            },
        },
        computed: {
            paginatedItems() {
                const start = (this.currentPage - 1) * this.perPage.value;
                const end = start + this.perPage.value;
                return this.transactions.slice(start, end);
            },
            startRow() {
                return this.transactions.length === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
            },
            endRow() {
                return Math.min(this.currentPage * this.perPage, this.transactions.length)
            },
        },
        methods: {
            fetchTransactions() {
                axios.get('/api/transactions').then(response => {
                    this.transactions = response.data;
                    this.rows = this.transactions.length;
                }).catch(error => {
                    console.error('Error fetching transactions:', error);
                }).finally(() => {
                    this.isTableLoading = false;

                    // generate dashboard metrics after transactions are fetched
                    this.getCurrentMonthExpenses();
                    this.getOverallExpenses();
                    this.getCurrentMonthIncome();
                    this.getOverallIncome();

                    this.isMetricsLoading = false;
                });
            },
            add(modalId) {
                this.isEditing = false;
                this.form = {
                        id: null,
                        type: null,
                        date: new Date().toISOString().split('T')[0],
                        description: '',
                        amount: null,
                        category_id: null,
                    };
                this.openModal(modalId);

            },
            submitForm(e) {
                e.preventDefault();

                if(this.isEditing){
                    this.saveItem({
                        url: `/api/transactions/${this.form.id}`,
                        data: this.form,
                        method: 'put',
                        successMessage: 'Transaction updated successfully!',
                        errorMessage: 'Failed to update transaction.',
                        callback: () => {
                            e.target.reset();
                            this.editing = false;
                            this.fetchTransactions();
                            this.closeModal('transactionModal');
                        }
                    });
                }else{
                    this.saveItem({
                        url: '/api/transactions',
                        data: this.form,
                        successMessage: 'Transaction added successfully!',
                        errorMessage: 'Failed to add transaction.',
                        callback: () => {
                            e.target.reset();
                            this.fetchTransactions();
                            this.closeModal('transactionModal');
                        }
                    });
                }
            },
            fetchTransaction(id) {
                this.fetchItem({
                    url: `/api/transactions/${id}`,
                    successMessage: 'Transaction fetched successfully!',
                    errorMessage: 'Failed to fetch transaction.',
                    callback: (response) => {
                        this.form = response;
                        this.loadCategories();
                        this.fetchTags();
                        this.isEditing = true;
                        this.openModal('transactionModal');
                    }
                });
            },
            deleteTransaction(id) {
                this.deleteItem({
                    url: `/api/transactions/${id}`,
                    successMessage: 'Transaction deleted successfully!',
                    errorMessage: 'Failed to delete transaction.',
                    callback: () => {
                        this.fetchTransactions();
                    }
                });
            },
            onFileChange(e) {
                this.file = e.target.files[0];
            },
            submitFile(){
                const formData = new FormData();
                formData.append('file', this.file);

                axios.post('/api/transactions/import', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    this.fetchTransactions();
                    this.closeModal('importModal');
                }).catch(error => {
                    this.$swal('Error!', 'Failed to import transactions.', 'error');
                    console.error('Error importing transactions:', error);
                });
            },
            loadCategories() {
                this.categories = [];
                this.fetchCategories(this.form.type);
            },
            async syncTransactions() {
                this.isLoading = true;
                try{
                    const response = await axios.get('/google-sheet/sync');
                    this.$swal({
                        title: 'Sync Completed!',
                        html: `
                            Rows Imported: ${response.data.rows_imported}<br/>
                            Rows Failed: ${response.data.rows_failed}<br/>
                            Rows Total: ${response.data.total_rows}<br/>
                        `,
                        icon: 'success',
                    });
                }catch(error){
                    this.$swal('Error!', 'Failed to sync transactions.', 'error');
                    console.error('Error syncing transactions:', error);
                }finally{
                    this.isLoading = false;
                    this.fetchTransactions();
                }
            },
            fetchBudgets(){
                axios.get('/budgets/active').then(response => {
                    this.budgets = response.data;
                }).catch(error => {
                    console.error('Error fetching budgets:', error);
                });
            },
            fetchTags(){
                const budget_id = this.form.budget_id;
                axios.get(`/budget-items/${budget_id}`).then(response => {
                    this.tags = response.data;
                }).catch(error => {
                    console.error('Error fetching tags:', error);
                });
            },
            selectBudget(budgetId) {
                this.form.budget_id = budgetId;
                this.form.budget_item_id = null; // Reset tag selection when budget changes
                this.fetchTags();
            },
            // get metrics functions
            async getCurrentMonthExpenses() {
                try {
                    const response = await axios.get('/dashboard/current-month-expenses');
                    this.currentMonthExpense = response.data.total_expenses;
                } catch (error) {
                    console.error('Error fetching current month expenses:', error);
                } finally {
                    // this.isMetricsLoading = false;
                }
            },
            async getOverallExpenses() {
                try {
                    const response = await axios.get('/dashboard/overall-expenses');
                    this.totalExpense = response.data.total_overall_expenses;
                } catch (error) {
                    console.error('Error fetching overall expenses:', error);
                } finally {
                    // this.isMetricsLoading = false;
                }
            },
            async getCurrentMonthIncome() {
                try {
                    const response = await axios.get('/dashboard/current-month-income');
                    this.currentMonthIncome = response.data.total_income;
                } catch (error) {
                    console.error('Error fetching current month income:', error);
                } finally {
                    // this.isMetricsLoading = false;
                }
            },
            async getOverallIncome() {
                try {
                    const response = await axios.get('/dashboard/overall-income');
                    this.totalIncome = response.data.total_overall_income;
                } catch (error) {
                    console.error('Error fetching overall income:', error);
                } finally {
                    // this.isMetricsLoading = false;
                }
            },
            // animation functions
            animateCount(key, start, end, duration = 300) {
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
        }
    }
</script>

<style scoped>
    .fw-black { 
        font-weight: 700; 
    }

    /* Restricts heights so the modal stays visible without excessive page scrolling */
    .interactive-scroll-container {
        max-height: 120px;
        overflow-y: auto;
    }

    .option-card {
        transition: all 0.15s ease-in-out;
    }

    .option-card:hover {
        transform: translateY(-1px);
    }

    /* Custom scrollbars */
    .interactive-scroll-container::-webkit-scrollbar {
        width: 4px;
    }
    .interactive-scroll-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    /* Styling for the consistent scroll containers */
    .interactive-scroll-container {
        height: 120px; /* Fixed height to match category and force scrolling */
        overflow-y: auto;
    }

    /* Custom scrollbar styling (optional but recommended for visual consistency) */
    .interactive-scroll-container::-webkit-scrollbar {
        width: 4px; /* Thin scrollbar */
    }

    .interactive-scroll-container::-webkit-scrollbar-thumb {
        background: #cbd5e1; /* slate-300 */
        border-radius: 4px;
    }

    .custom-floating {
        min-height: 60px;
    }

    .custom-floating .form-control {
        height: 60px;
    }

    /* Enforce cursor and subtle dimming on disabled state hover */
    .btn-disabled-soft:disabled,
    .btn-disabled-soft[disabled] {
        cursor: not-allowed !important;
        pointer-events: auto !important; /* Enables mouse events so cursor: not-allowed shows on hover */
        opacity: 0.65;
        transition: opacity 0.2s ease-in-out;
    }

    .btn-disabled-soft:disabled:hover {
        opacity: 0.5;
        background-color: var(--bs-primary-bg-subtle) !important;
        border-color: var(--bs-primary-border-subtle) !important;
    }

    /* Smooth fade & micro-scale transition for metric loading */
    .fade-metric-enter-active,
    .fade-metric-leave-active {
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .fade-metric-enter-from,
    .fade-metric-leave-to {
        opacity: 0;
        transform: scale(0.98);
    }
</style>