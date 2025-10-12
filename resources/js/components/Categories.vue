<template>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-bars me-2"></i>Categories</div>
                <div class="card-body">
                    <button class="btn btn-primary" @click="add('categoryModal')">Add Category</button>
                    <hr>
                    <div class="container-fluid table-responsive my-2">
                        <BFormInput
                            v-model="filter"
                            placeholder="Type to Search..."
                            class="mb-3"
                            size="md"
                            style="width:400px;"
                        />
                        <BTable
                            :items="categories"
                            :fields="fields"
                            :per-page="perPage"
                            :current-page="currentPage"
                            :filter="filter"
                            striped
                            hover
                            bordered
                        >
                            <template #cell(type)="row">
                                <span v-if="row.item.type == 1" class="badge bg-success">
                                    <i class="fa-solid fa-arrow-up me-2"></i>Income
                                </span>
                                <span v-else class="badge bg-danger">
                                    <i class="fa-solid fa-arrow-down me-2"></i>Expense
                                </span>
                            </template>
                            <template #cell(actions)="row">
                                <BButton size="sm" variant="warning" @click="fetchCategory(row.item.id)">Edit</BButton>&nbsp;
                                <BButton size="sm" variant="danger" @click="deleteCategory(row.item.id)">Delete</BButton>
                            </template>
                        </BTable>
                        <div class="d-flex justify-content-between align-items-center mb-2 py-3">
                            <p>
                                Showing {{ startRow }}–{{ endRow }} of {{ categories.length }} rows
                            </p>
                            <BPagination
                                v-model="currentPage"
                                :total-rows="categories.length"
                                :per-page="perPage"
                                align="end"
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

<!-- Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form @submit.prevent="submitForm">
                <input type="hidden" name="id" v-model="form.id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" v-model="form.type" class="form-select">
                            <option selected disabled value="">Choose...</option>
                            <option value="1">Income</option>
                            <option value="2">Expense</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Category Name" v-model="form.name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="description" cols="30" rows="4" placeholder="Category Description" v-model="form.description" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
</template>

<script>
    import { Helpers } from '../methods/helpers.js';
    export default {
        mixins: [Helpers],
        mounted(){
            this.fetchCategories();
        },
        data() {
            return {
                categories: [],
                form: {
                    id: null,
                    type: '',
                    name: '',
                    description: '',
                },
                isEditing: false,
                // datatable
                perPage: 10,
                currentPage: 1,
                filter: '',
                fields: [
                    { key: 'type', label: 'Type', sortable: true },
                    { key: 'name', label: 'Category', sortable: true },
                    { key: 'description', label: 'Description', sortable: true },
                    { key: 'actions', label: 'Actions' }   
                ],
                isEditing: false
            };
        },
        methods:{
            add(modalId){
                this.isEditing = false;
                this.form = {
                    id: null,
                    name: '',
                    description: '',
                };
                this.openModal('categoryModal')
            },
            submitForm(e){
                e.preventDefault();
                
                if(this.isEditing){
                    this.updateItem({
                        url: `/api/categories/${this.form.id}`,
                        data: this.form,
                        successMessage: 'Category updated successfully!',
                        errorMessage: 'Failed to update category.',
                        callback: () => {
                            e.target.reset();
                            this.editing = false;
                            this.fetchCategories();
                            this.closeModal('categoryModal');
                        }
                    });
                }else{
                    this.addItem({
                        url: '/api/categories',
                        data: this.form,
                        successMessage: 'Category added successfully!',
                        errorMessage: 'Failed to add category.',
                        callback: () => {
                            e.target.reset();
                            this.fetchCategories();
                            this.closeModal('categoryModal');
                        }
                    })
                }
            },
            fetchCategory(id){
                this.fetchItem({
                    url: `/api/categories/${id}`,
                    successMessage: 'Category fetched successfully!',
                    errorMessage: 'Failed to fetch category.',
                    callback: (response) => {
                        this.form = response;
                        this.isEditing = true;
                        this.openModal('categoryModal');
                    }
                })
            },
            deleteCategory(id){
                this.deleteItem({
                    url: `/api/categories/${id}`,
                    successMessage: 'Category deleted successfully!',
                    errorMessage: 'Failed to delete category.',
                    callback: () => {
                        this.fetchCategories();
                    }
                });
            }
        },
        computed: {
            paginatedItems() {
                const start = (this.currentPage - 1) * this.perPage.value;
                const end = start + this.perPage.value;
                return this.categories.slice(start, end);
            },
            startRow() {
                return this.categories.length === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
            },
            endRow() {
                return Math.min(this.currentPage * this.perPage, this.categories.length)
            }
        }
    }
</script>
