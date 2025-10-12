<template>
    <div class="modal fade" id="subCategoryModal" tabindex="-1" aria-labelledby="categoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Sub-Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="handleSubmit">
                    <input type="hidden" name="id" v-model="form.id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" id="type" v-model="form.category_id" class="form-select" required>
                                <option selected disabled value="">Choose...</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>"
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Sub-Category Name" v-model="form.name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" class="form-control" id="description" cols="30" rows="4" placeholder="Sub-Category Description" v-model="form.description" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Sub-Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default{
    props: ['selectedItem'],
    data() {
        return {
            isEditing: false,
            form: {
                id: null,
                category_id: null,
                name: '',
                description: '',
            },
            categories: []
        }
    },
    methods: {
        handleSubmit(e){
            this.submitForm(e, {
                updateUrl: `/api/sub-categories/${this.form.id}`,
                addUrl: '/api/sub-categories',
                title: 'Sub-Category',
                modalId: 'subCategoryModal',
                form: this.form,
                callback: () => {
                    this.isEditing = false;
                    
                    this.cleanForm()
                }
            })
        },
        cleanForm(){
            this.form = {
                id: null,
                category_id: null,
                name: '',
                description: '',
            }
        },
    },
    mounted() {
        this.fetchCategories();
    },
    watch: {
        selectedItem: {
            immediate: true,
            handler(value) {
                if (value) {
                    this.form = { ...value }
                    this.isEditing = true;
                }
            }
        }
    }
}
</script>
