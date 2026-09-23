<template>
    <!-- Page Content -->
    <div class="container-fluid">
        <div class="card shadow-sm border border-secondary-subtle rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-primary-subtle text-primary me-3 px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-calculator"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">Budgets</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4 align-items-center">
                    <div class="col-md-8 d-flex gap-2">
                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" @click="add()">
                            <i class="fa-solid fa-plus me-2"></i>Add Budget
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
                        :per-page="perPage"
                        :current-page="currentPage"
                        :filter="filter"
                        :busy="isTableLoading"
                        hover
                        class="align-middle border-top"
                        thead-class="table-light text-uppercase small fw-bold"
                        :tbody-tr-class="getRowClass"
                    >
                        <template #table-busy>
                            <div class="text-center text-primary my-4 py-3">
                                <!-- Standard HTML Bootstrap Spinner fallback -->
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                <span class="fw-medium text-secondary">Loading budgets...</span>
                            </div>
                        </template>

                        <template #cell(budget_name)="row">
                            <router-link 
                                :to="`/budget-items/${row.item.id}`"
                                class="text-decoration-none fw-semibold text-primary link-offset-2-hover link-underline-hover"
                            >
                                {{ row.item.budget_name }}
                            </router-link>
                        </template>

                        <template #cell(budget)="row">
                            <div 
                                class="px-3 py-1 rounded-pill fw-semibold font-monospace"
                                :class="row.item.budget !== null && row.item.budget !== undefined 
                                    ? 'text-end bg-dark bg-opacity-10 text-dark-emphasis' 
                                    : 'text-center bg-secondary-subtle text-secondary small fs-7 fw-normal'"
                            >
                                {{ row.item.budget !== null && row.item.budget !== undefined 
                                    ? formatPeso(row.item.budget) 
                                    : 'Not yet defined' }}
                            </div>
                        </template>

                        <template #cell(actual)="row">
                            <div class="px-3 py-1 rounded-pill fw-semibold text-end font-monospace bg-dark bg-opacity-10 text-dark-emphasis">
                                {{ formatPeso(row.item.actual ?? 0) }}
                            </div>
                        </template>

                        <template #cell(remaining)="row">
                            <div 
                                class="px-3 py-1 rounded-pill fw-semibold text-end font-monospace"
                                :class="row.item.remaining < 0 
                                    ? 'bg-danger bg-opacity-10 text-danger' 
                                    : 'bg-success bg-opacity-10 text-success-emphasis'"
                            >
                                {{ formatPeso(row.item.remaining) }}
                            </div>
                        </template>

                        <!-- Status Column Template -->
                        <template #cell(status)="row">
                            <div class="text-center">
                                <span 
                                    class="px-2 py-1 rounded-pill small fw-semibold border d-inline-flex align-items-center gap-1"
                                    :class="row.item.status === 1 || row.item.status === '1' 
                                        ? 'bg-success-subtle text-success-emphasis border-success-subtle' 
                                        : 'bg-dark-subtle text-dark-emphasis border-dark-subtle'"
                                >
                                    <i 
                                        :class="row.item.status === 1 || row.item.status === '1' 
                                            ? 'fa-solid fa-circle-check' 
                                            : 'fa-solid fa-lock'"
                                        style="font-size: 0.75rem;"
                                    ></i>
                                    {{ row.item.status_label || (row.item.status === 1 ? 'Active' : 'Closed') }}
                                </span>
                            </div>
                        </template>
                        <template #cell(actions)="row">
                            <div class="d-flex gap-1">
                                <BButton 
                                    size="sm" 
                                    variant="light" 
                                    class="btn-icon rounded-circle bg-warning-subtle border-warning-subtle text-warning-emphasis shadow-sm px-2 py-1" 
                                    @click="edit(row.item.id)"
                                    title="Edit Transaction"
                                >
                                    <i class="fa-solid fa-pen-to-square small"></i>
                                </BButton>

                                <BButton 
                                    size="sm" 
                                    variant="danger" 
                                    class="btn-icon rounded-circle bg-danger-subtle border-danger-subtle text-danger-emphasis shadow-sm px-2 py-1" 
                                    @click="remove(row.item.id)"
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
    <div class="offcanvas offcanvas-end border-0 shadow" tabindex="-1" id="budgetCanvas" style="width: 550px;">
        
        <!-- Drawer Header -->
        <div class="offcanvas-header border-bottom py-3 px-4 flex-shrink-0">
            <h5 class="offcanvas-title fw-bolder d-flex align-items-center mb-0" id="budgetCanvasLabel">
                <i class="fa-solid fa-wallet text-primary me-3 fs-4"></i>
                <span class="text-dark">{{ form.id ? 'Edit' : 'New' }} Budget</span>
            </h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <!-- Form & Body Structure -->
        <form @submit.prevent="submitForm" novalidate class="d-flex flex-column h-100 mb-0 overflow-hidden">
            
            <!-- Scrollable Body Container -->
            <div class="offcanvas-body p-4 flex-grow-1" style="overflow-y: auto; min-height: 0;">
                
                <!-- Budget Name Input -->
                <div class="mb-4">
                    <div class="form-floating custom-floating">
                        <input 
                            type="text" 
                            class="form-control border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                            id="budget_name" 
                            placeholder="e.g. September 2026 Budget" 
                            v-model="form.budget_name" 
                            required
                        >
                        <label for="budget_name" class="text-muted ps-2 pt-2">Budget Name</label>
                    </div>
                </div>

                <!-- Start Date & End Date Inputs -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="form-floating custom-floating">
                            <input 
                                type="date" 
                                class="form-control border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                                id="start_date" 
                                placeholder="YYYY-MM-DD" 
                                v-model="form.start_date" 
                                required
                            >
                            <label for="start_date" class="text-muted ps-2 pt-2">Start Date</label>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-floating custom-floating">
                            <input 
                                type="date" 
                                class="form-control border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                                id="end_date" 
                                placeholder="YYYY-MM-DD" 
                                v-model="form.end_date" 
                                required
                            >
                            <label for="end_date" class="text-muted ps-2 pt-2">End Date</label>
                        </div>
                    </div>
                </div>

                <!-- Status Switcher (Active / Closed) -->
                <div class="mb-4">
                    <label class="form-label small text-uppercase fw-bold text-muted d-block mb-2">Status</label>
                    <div class="btn-group w-100 rounded-pill overflow-hidden border p-1 bg-light" role="group" aria-label="Budget Status">
                        <!-- Active Radio (1) -->
                        <input type="radio" class="btn-check" name="status" id="status_active" :value="1" v-model="form.status" required>
                        <label 
                            class="btn border-0 py-2 small fw-semibold d-flex align-items-center justify-content-center text-success rounded-pill" 
                            :class="Number(form.status) === 1 ? 'bg-success-subtle border border-success-subtle shadow-sm' : 'bg-transparent opacity-50'" 
                            for="status_active">
                            <i class="fa-solid fa-circle-check me-2"></i> Active
                        </label>

                        <!-- Closed/Inactive Radio (0) -->
                        <input type="radio" class="btn-check" name="status" id="status_closed" :value="0" v-model="form.status">
                        <label 
                            class="btn border-0 py-2 small fw-semibold d-flex align-items-center justify-content-center text-secondary rounded-pill" 
                            :class="Number(form.status) === 0 ? 'bg-secondary-subtle border border-secondary-subtle shadow-sm text-dark' : 'bg-transparent opacity-50'" 
                            for="status_closed">
                            <i class="fa-solid fa-lock me-2"></i> Closed
                        </label>
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
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-check me-1"></i>
                    Save Budget
                </button>
            </div>
        </form>
    </div>

</template>
<script>
    // import AddButton from './Shared/AddButton.vue';
    import DataTable from './Shared/DataTable.vue';
    import { ref } from 'vue';

    export default {
        components: { DataTable },
        data() {
            return {
                isTableLoading: true,
                module: 'budget',
                utilityUrl: '/api/budgets',
                selectedItem: null,
                budgets: [],
                viewUrl: '/budget-items',
                fields: [
                    { key: 'budget_name', label: 'Budget Name', sortable: true },
                    { key: 'formatted_start_date', label: 'Start Date', sortable: true },
                    { key: 'formatted_end_date', label: 'End Date', sortable: true },
                    { key: 'budget', label: 'Budget', sortable: true, class: 'text-end' },
                    { key: 'actual', label: 'Actual', sortable: true, class: 'text-end' },
                    { key: 'remaining', label: 'Remaining', sortable: true, class: 'text-end' },
                    { key: 'status', label: 'Status', thClass: 'text-center'},
                    { key: 'actions', label: 'Actions' }
                ],
                formFields: [
                    { key: 'id', label: 'ID', type: 'input', hidden: true, inputType: "text" },
                    { 
                        key: 'budget_name', 
                        label: 'Budget Name', 
                        type: 'input', 
                        required: true, 
                        inputType: "text",
                        placeholder: 'e.g. Monthly Budget, Vacation Budget'
                    },
                    { key: 'start_date', label: 'Start Date', type: 'input', required: true, inputType: "date" },
                    { key: 'end_date', label: 'End Date', type: 'input', required: true, inputType: "date" },
                ],
                formatters: {
                    budget: (val) => ({ 
                        value: this.formatPeso(val), 
                        class: 'font-monospace text-secondary' 
                    }),
                    actual: (val) => ({ 
                        value: this.formatPeso(val ?? 0), 
                        class: 'font-monospace text-dark fw-medium' 
                    }),
                    remaining: (val) => ({ 
                        value: this.formatPeso(val), 
                        class: `font-monospace fw-bold ${val < 0 ? 'text-danger' : 'text-success'}` 
                    }),
                },
                form:{
                    id: null,
                    budget_name: null,
                    status: 1,
                    start_date: null,
                    end_date: null,
                },
                perPage: ref(10),
                currentPage: ref(1),
                rows: ref(0),
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
                this.budgets = await this.fetchRecords({ url: this.utilityUrl });
                this.isTableLoading = false;
            },
            resetSelection(){
                this.selectedItem = {};
            },
            getRowClass(item, type) { // format display of rows
                if (!item || type !== 'row') return;

                if (Number(item.status) !== 1) {
                    return 'table-secondary opacity-75';
                }
            }, 
            add() {
                const formatLocalDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };

                const now = new Date();

                // Start Date: 1st day of next month (Local)
                const nextMonthStart = new Date(now.getFullYear(), now.getMonth() + 1, 1);
                const startDateFormatted = formatLocalDate(nextMonthStart);

                // End Date: Last day of next month (Local)
                const nextMonthEnd = new Date(now.getFullYear(), now.getMonth() + 2, 0);
                const endDateFormatted = formatLocalDate(nextMonthEnd);

                this.isEditing = false;
                this.form = {
                        id: null,
                        budget_name: null,
                        start_date: startDateFormatted,
                        end_date: endDateFormatted,
                        status: 1
                    };
                this.openModal('budgetCanvas');

            },
            edit(id){
                this.fetchItem({
                    url: `/api/budgets/${id}`,
                    errorMessage: 'Failed to retrieve budget details.',
                    callback: (response) => {
                        const formattedResponse = {
                            ...response,
                            start_date: response.start_date ? response.start_date.split('T')[0] : '',
                            end_date: response.end_date ? response.end_date.split('T')[0] : '',
                        };
                        this.form = formattedResponse;
                        this.isEditing = true;
                        this.openModal('budgetCanvas');
                    }
                })
            },
            submitForm(e){
                e.preventDefault();

                this.saveItem({
                    url: this.isEditing ? `/api/budgets/${this.form.id}` : '/api/budgets',
                    method: this.isEditing ? 'put' : 'post',
                    data: this.form,
                    successMessage: this.isEditing ? 'Budget updated successfully!' : 'Budget saved successfully!',
                    errorMessage: this.isEditing ? 'Failed to update budget.' : 'Failed to save budget.',
                    callback: () => {
                        e.target.reset();
                        this.loadBudgets();
                        this.closeModal('budgetCanvas');
                    }
                });
            },
            remove(id){
                this.deleteItem({
                    url: `/api/budgets/${id}`,
                    successMessage: 'Budget deleted successfully.',
                    errorMessage: 'Failed to delete budget.',
                    callback: () => {
                        this.loadBudgets();
                    }
                })
            }
        },
        mounted(){
            this.loadBudgets();
        },
    }
</script>
