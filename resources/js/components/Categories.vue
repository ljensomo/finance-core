<template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-bars me-2"></i>Categories</div>
                    <div class="card-body">
                        <AddButton :module="module" @add="resetSelection"></AddButton>
                        <DataTable
                            :items="categories"
                            :fields="fields"
                            :utilityUrl="utilityUrl"
                            :module="module"
                            @select-item="selectedItem = $event"
                            @reload-table="loadCategories"
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
        @reload-table="fetchCategories"
    >
    </ModalForm>
</template>

<script>
    import AddButton from './Shared/AddButton.vue';
    import DataTable from './Shared/DataTable.vue';
    import ModalForm from './Shared/ModalForm.vue';

    export default {
        components: { AddButton, DataTable, ModalForm },
        mounted(){
            this.fetchCategories();
        },
        data() {
            return {
                module: 'category',
                utilityUrl: '/api/categories',
                selectedItem: null,
                categories: [],
                fields: [
                    { key: 'type', label: 'Type', sortable: true },
                    { key: 'name', label: 'Category', sortable: true },
                    { key: 'color', label: 'Color' },
                    { key: 'description', label: 'Description', sortable: true },
                    { key: 'actions', label: 'Actions' }   
                ],
                formFields: [
                    { key: 'id', label: 'ID', type: 'input', hidden: true, inputType: "text" },
                    { key: 'type', label: 'Type', type: 'select', required: true, options:[
                        { value: 2, label: 'Expense' },
                        { value: 1, label: 'Income' },
                    ]},
                    { key: 'name', label: 'Category Name', type: 'input', required: true},
                    { key: 'color', label: 'Color', type: 'input', required: true, inputType: "color"},
                    { key: 'description', label: 'Description', type: 'textarea'}
                ],
                formatters: {
                    type: (val) => {
                        if(val == 1){
                            return '<span class="badge bg-success"><i class="fa-solid fa-arrow-up me-2"></i>Income</span>';
                        } else if(val == 2){
                            return '<span class="badge bg-danger"><i class="fa-solid fa-arrow-down me-2"></i>Expense</span>';
                        }
                    },
                    color: (val) => {
                        return `<span class="badge" style="background-color: ${val}; color: #fff;">${val}</span>`;
                    }
                }
            };
        },
        methods:{
            async loadCategories(){
                this.categories = await this.fetchRecords({ url: this.utilityUrl });
            },
            resetSelection(){
                this.selectedItem = {};
            },
        },
        mounted(){
            this.loadCategories();
        }
    }
</script>
