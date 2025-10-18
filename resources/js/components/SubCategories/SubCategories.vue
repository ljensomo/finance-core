<template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-bars me-2"></i>Sub-Categories</div>
                    <div class="card-body">
                        <button class="btn btn-primary" @click="triggerAddModal('subCategoryModal')">Add Sub-Category</button>
                        <hr>
                        <DataTable :items="subcategories" :fields="fields" :onEdit="handleEdit" :onDelete="handleDelete"></DataTable>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <SubCategoryModal @reload-subcategories="loadSubcategories" :selectedItem="selectedItem"></SubCategoryModal>
</template>

<script>
import DataTable from '../Shared/DataTable.vue'
import SubCategoryModal from './SubCategoryModal.vue'

export default {
    components: { SubCategoryModal, DataTable },
    data() {
        return {
            selectedItem: null,
            subcategories: [],
            fields: [
                { key: 'category.name', label: 'Category', sortable: true },
                { key: 'name', label: 'Name', sortable: true },
                { key: 'description', label: 'Description', sortable: true },
                { key: 'actions', label: 'Actions' }
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
        }
    },
    async mounted() {
        this.loadSubcategories()
    }
}
</script>
