<template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-bars me-2"></i>Sub-Categories</div>
                    <div class="card-body">
                        <button class="btn btn-primary" @click="triggerAddModal('subCategoryModal')">Add Sub-Category</button>
                        <hr>
                        <DataTable :items="subcategories" :fields="fields" :onEdit="handleEdit"></DataTable>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <SubCategoryModal :selectedItem="selectedItem"></SubCategoryModal>
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
                { key: 'name', label: 'Sub-Category', sortable: true },
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
    },
    async mounted() {
        this.subcategories = await this.fetchRecords({
                url: '/api/sub-categories',            
            });
    }
}
</script>
