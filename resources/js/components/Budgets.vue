<template>
<div class="container-fluid">
    <div class="card shadow-sm border-1 rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-primary-subtle text-primary me-3 px-3 py-2 rounded">
                <i class="fa-solid fa-calculator me-2"></i>
                </div>
                <h5 class="mb-0 fw-bold">Budgets</h5>
            </div>
        </div>
        <div class="card-body px-4 pb-4">
            <DataTable
                :items="budgets"
                :fields="fields"
                :form-fields="formFields"
                :utilityUrl="utilityUrl"
                :module="module"
                :formatters="formatters"
                @select-item="selectedItem = $event"
                @reload-table="loadBudgets"
                @addFunction="resetSelection"
                :hasView="true"
                :viewUrl="viewUrl"
            />
        </div>
    </div>
</div>
</template>
<script>
    // import AddButton from './Shared/AddButton.vue';
    import DataTable from './Shared/DataTable.vue';

    export default {
        components: { DataTable },
        data() {
            return {
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
                    { key: 'status', label: 'Status'},
                    { key: 'actions', label: '' }
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
                }
            }
        },
        methods: {
            async loadBudgets(){
                this.budgets = await this.fetchRecords({ url: this.utilityUrl });
            },
            resetSelection(){
                this.selectedItem = {};
            },
        },
        mounted(){
            this.loadBudgets();
        },
    }
</script>
