<template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-bars me-2"></i>Sub-Categories</div>
                    <div class="card-body">
                        <AddButton :module="module" @add="resetSelection"></AddButton>
                        <DataTable 
                            :items="subcategories"
                            :fields="fields"
                            :utilityUrl="utilityUrl"
                            :module="module"
                            @select-item="selectedItem = $event"
                            @reload-table="loadSubcategories"
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
        @reload-table="loadSubcategories"
    >
    </ModalForm>
</template>

<script>
import AddButton from '../Shared/AddButton.vue'
import DataTable from '../Shared/DataTable.vue'
import ModalForm from '../Shared/ModalForm.vue'

export default {
    components: { DataTable, AddButton, ModalForm },
    data() {
        return {
            module: 'sub-category',
            utilityUrl: '/api/sub-categories',
            selectedItem: null,
            subcategories: [],
            categories: [],
            categoryOptions: [],
            fields: [
                { key: 'category.name', label: 'Category', sortable: true },
                { key: 'name', label: 'Name', sortable: true },
                { key: 'description', label: 'Description', sortable: true },
                { key: 'actions', label: 'Actions' }
            ],
            formFields: [
                { key: 'id', label: 'ID', type: 'input', hidden: true, inputType: "text" },
                { key: 'category_id', label: 'Category', type: 'select', required: true, options: []},
                { key: 'name', label: 'Name', type: 'input', required: true},
                { key: 'description', label: 'Description', type: 'textarea', required: true}
            ]
        }
    },
    methods: {
        async handleEdit(itemId){
            const record = await this.fetchItem({ url: `/api/sub-categories/${itemId}` });
            this.selectedItem = record;
            this.isEditing = true;
            this.openModal('subCategoryModal');
        },
        handleDelete(itemId){
            this.deleteItem({
                url: `/api/sub-categories/${itemId}`,
                callback:() => {
                    this.loadSubcategories();
                }
            })
        },
        async loadSubcategories() {
            this.subcategories = await this.fetchRecords({
                url: '/api/sub-categories',
            });
        },
        async loadCategories() {
            this.categories = await this.fetchRecords({
                url: '/api/categories',
            });
        },
        setCategoryOptions(){
            this.categoryOptions = this.buildOptions(this.categories, "name", "id");
        },
        resetSelection() {
            this.selectedItem = {};
        }
    },
    async mounted() {
        await this.loadSubcategories();
        await this.loadCategories();
        this.setCategoryOptions(); // format options
        this.formFields[1].options = this.categoryOptions; // add options to category select
        this.categories = []; // clean the category - not needed anymore
    }
}
</script>
