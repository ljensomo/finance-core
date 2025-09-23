<template>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Categories</div>

                <div class="card-body">
                    <button class="btn btn-primary mb-3" @click="add('categoryModal')">Add Category</button>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="category in categories" :key="category.id">
                                <td>{{ category.type == 1 ? 'Income' : 'Expense' }}</td>
                                <td>{{ category.name }}</td>
                                <td>{{ category.description }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" @click="fetchCategory(category.id)">Edit</button>&nbsp;
                                    <button class="btn btn-sm btn-danger" @click="deleteCategory(category.id)">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
        }  
    }
</script>
