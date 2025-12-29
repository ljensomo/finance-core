<template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-calculator me-2"></i>Budgets</div>
                    <div class="card-body">
                        <AddButton :module="module" @add="resetSelection"></AddButton>
                        <DataTable
                            :items="budgets"
                            :fields="fields"
                            :utilityUrl="utilityUrl"
                            :module="module"
                            @select-item="selectedItem = $event"
                            @reload-table="loadBudgets"
                            :formatters="formatters"
                            :hasView="true"
                            :viewUrl="viewUrl"
                        >
                        </DataTable>
                    </div>
                </div>
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
    import AddButton from './Shared/AddButton.vue';
    import DataTable from './Shared/DataTable.vue';
    import ModalForm from './Shared/ModalForm.vue';

    export default {
        components: { AddButton, DataTable, ModalForm },
        data() {
            return {
                module: 'budget',
                utilityUrl: '/api/budgets',
                selectedItem: null,
                budgets: [],
                viewUrl: '/budget-items',
                fields: [
                    { key: 'budget_name', label: 'Budget Name', sortable: true },
                    { key: 'start_date', label: 'Start Date', sortable: true },
                    { key: 'end_date', label: 'End Date', sortable: true },
                    { key: 'budget', label: 'Budget', sortable: true },
                    { key: 'actual', label: 'Actual', sortable: true },
                    { key: 'remaining', label: 'Remaining', sortable: true },
                    { key: 'actions', label: 'Actions' }
                ],
                formFields: [
                    { key: 'id', label: 'ID', type: 'input', hidden: true, inputType: "text" },
                    { key: 'budget_name', label: 'Budget Name', type: 'input', required: true, inputType: "text" },
                    { key: 'start_date', label: 'Start Date', type: 'input', required: true, inputType: "date" },
                    { key: 'end_date', label: 'End Date', type: 'input', required: true, inputType: "date" },
                ],
                formatters:{
                    budget: (value) => {
                        return this.formatPeso(value);
                    },
                    actual: (value) => {
                        const amount = value == null ? 0 : value;
                        return this.formatPeso(amount);
                    },
                    remaining: (value) => {
                        return this.formatPeso(value);
                    },
                    start_date: (value) => {
                        return new Date(value).toLocaleDateString();
                    },
                    end_date: (value) => {
                        return new Date(value).toLocaleDateString();
                    }
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
        }
    }
</script>
