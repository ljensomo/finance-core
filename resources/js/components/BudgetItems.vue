<template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-calculator me-2"></i>Budgets | <strong>{{ budget.budget_name }}</strong></div>
                    <div class="card-body">
                        <a href="/budgets" class="btn btn-secondary me-2"><i class="fa-solid fa-arrow-left me-2"></i>Back</a>
                        <AddButton :module="module" @add="resetSelection"></AddButton>
                        <DataTable
                            :items="budgets"
                            :fields="fields"
                            :utilityUrl="utilityUrl"
                            :module="module"
                            @select-item="selectedItem = $event"
                            @reload-table="loadBudgets"
                            :formatters="formatters"
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
                budgetId: this.$route.query.id || null,
                module: 'budget-item',
                utilityUrl: '/api/budget-items',
                selectedItem: null,
                budget: {},
                budgets: [],
                categories: [],
                categoryOptions: [],
                fields: [
                    { key: 'item_name', label: 'Title', sortable: true },
                    { key: 'category.name', label: 'Category', sortable: true },
                    { key: 'sub_category', label: 'Sub Category', sortable: true },
                    { key: 'description', label: 'Description', sortable: true },
                    { key: 'amount', label: 'Budget Amount', sortable: true },
                    { key: 'actions', label: 'Actions' }
                ],
                formFields: [
                    { key: 'id', label: 'ID', type: 'input', hidden: true, inputType: "text" },
                    { key: 'budget_id', label: 'Budget ID', type: 'defaultInput', hidden: true, required: true, inputType: "text", value: this.$route.query.id },
                    { key: 'item_name', label: 'Item Name', type: 'input', required: true, inputType: "text" },
                    { key: 'category_id', label: 'Category', type: 'select', required: true, options: [] },
                    { key: 'sub_category_id', label: 'Sub Category', type: 'select', options: [] },
                    { key: 'amount', label: 'Amount', type: 'input', required: true, inputType:"number" },
                    { key: 'description', label: 'Description', type: 'textarea', required: true, inputType: "text" },
                ],
                formatters:{
                    amount: (value) => {
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
        },
        async mounted(){
            this.budget = await this.fetchItem({ url: '/api/budgets/'+this.$route.query.id });
            this.loadBudgets();
            await this.loadCategories();
            this.setCategoryOptions(); // format options
            this.formFields[3].options = this.categoryOptions; // add options to category select
        }
    }
</script>
