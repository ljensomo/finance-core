<template>
    <!-- Page Content -->
    <div class="container-fluid">
        <div class="card shadow-sm border border-secondary-subtle rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-primary-subtle text-primary me-3 px-3 py-2 rounded-pill">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">Categories</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4 align-items-center">
                    <div class="col-md-8 d-flex gap-2">
                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" @click="add()">
                            <i class="fa-solid fa-plus me-2"></i>Add Category
                        </button>
                    </div>
                    <div class="col-md-4 d-flex gap-2 align-items-center">
                        <!-- Search Input Group -->
                        <div class="input-group input-group-sm rounded-pill overflow-hidden shadow-sm">
                            <!-- Icon Prefix (Rounded Start) -->
                            <span class="input-group-text bg-light border-secondary-subtle border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="fa-solid fa-magnifying-glass small"></i>
                            </span>

                            <!-- Search Input (Rounded End) -->
                            <BFormInput
                                v-model="filter"
                                placeholder="Type to Search..."
                                size="sm"
                                class="border-secondary-subtle border-start-0 shadow-none fw-medium text-secondary rounded-end-pill pe-3"
                            />
                        </div>
                    </div>
                </div>
                <div class="table-responsive rounded-3 overflow-hidden border border-secondary-subtle">
                    <BTable
                        :items="categories"
                        :fields="fields"
                        :filter="filter"
                        :busy="isTableLoading"
                        hover
                        small
                        striped
                        outlined
                    >
                        <template #table-busy>
                            <div class="text-center text-primary my-4 py-4 bg-dark-subtle rounded-3">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                <span class="fw-medium text-secondary">Loading budgets...</span>
                            </div>
                        </template>

                        <!-- Category Type -->
                        <template #cell(type)="data">
                            <span v-if="data.value == 1" class="badge rounded-pill bg-success-subtle text-success px-3">
                                <i class="fa-solid fa-circle-arrow-down me-1"></i> Income
                            </span>
                            <span v-else class="badge rounded-pill bg-danger-subtle text-danger px-3">
                            <i class="fa-solid fa-circle-arrow-up me-1"></i> Expense
                            </span>
                        </template>

                        <!-- Category Name with Icon -->
                        <template #cell(name)="data">
                            <div class="d-flex align-items-center gap-2">
                                <!-- Category Icon Badge -->
                                <div 
                                    class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
                                    :style="{ backgroundColor: data.item.color || '#6c757d', width: '28px', height: '28px' }"
                                >
                                    <i :class="data.item.icon || 'fa-solid fa-tag'" class="fs-6"></i>
                                </div>
                                
                                <!-- Category Name -->
                                <span class="fw-semibold text-dark">{{ data.value }}</span>
                            </div>
                        </template>

                        <!-- Color -->
                        <template #cell(color)="data">
                            <div v-if="data.value" class="d-inline-flex align-items-center gap-2">
                                <span 
                                    class="rounded-circle border shadow-sm" 
                                    :style="{ backgroundColor: data.value, width: '16px', height: '16px', display: 'inline-block' }"
                                ></span>
                                <code class="text-body font-monospace fw-semibold">{{ data.value }}</code>
                            </div>
                            <span v-else class="text-muted small">N/A</span>
                        </template>

                        <!-- Actions -->
                        <template #cell(actions)="row">
                            <div class="d-flex gap-1">
                                <BButton 
                                    size="sm" 
                                    variant="light" 
                                    class="btn-icon rounded-circle bg-warning-subtle border-warning-subtle text-warning-emphasis shadow-sm px-2 py-1" 
                                    @click="edit(row.item)"
                                    title="Edit Transaction"
                                >
                                    <i class="fa-solid fa-pen-to-square small"></i>
                                </BButton>

                                <BButton 
                                    size="sm" 
                                    variant="danger" 
                                    class="btn-icon rounded-circle bg-danger-subtle border-danger-subtle text-danger-emphasis shadow-sm px-2 py-1" 
                                    @click="remove(row.item)"
                                    title="Delete Transaction"
                                >
                                    <i class="fa-solid fa-trash small"></i>
                                </BButton>
                            </div>
                        </template>
                    </BTable>
                </div>
            </div>
        </div>
    </div>

    <!-- Canvas -->
    <div class="offcanvas offcanvas-end border-0 shadow" tabindex="-1" id="categoryCanvas" style="width: 550px;">
        
        <!-- Drawer Header -->
        <div class="offcanvas-header border-bottom py-3 px-4 flex-shrink-0">
            <h5 class="offcanvas-title fw-bolder d-flex align-items-center mb-0" id="categoryCanvasLabel">
                <i class="fa-solid fa-tags text-primary me-3 fs-4"></i>
                <span class="text-dark">{{ form.id ? 'Edit' : 'New' }} Category</span>
            </h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <!-- Form & Body Structure -->
        <form @submit.prevent="submitForm" novalidate class="d-flex flex-column h-100 mb-0 overflow-hidden">
            
            <!-- Scrollable Body Container -->
            <div class="offcanvas-body p-4 flex-grow-1" style="overflow-y: auto; min-height: 0;">
                
                <!-- Category Type Selector (Income / Expense) -->
                <div class="mb-4">
                    <label class="form-label small text-uppercase fw-bold text-muted d-block mb-2">Category Type</label>
                    <div class="btn-group w-100 rounded-pill overflow-hidden border p-1 bg-light" role="group" aria-label="Category Type">
                        <!-- Income Radio (1) -->
                        <input type="radio" class="btn-check" name="type" id="type_income" :value="1" v-model="form.type" required>
                        <label 
                            class="btn border-0 py-2 small fw-semibold d-flex align-items-center justify-content-center text-success rounded-pill" 
                            :class="Number(form.type) === 1 ? 'bg-success-subtle border border-success-subtle shadow-sm' : 'bg-transparent opacity-50'" 
                            for="type_income">
                            <i class="fa-solid fa-circle-arrow-down me-2"></i> Income
                        </label>

                        <!-- Expense Radio (2) -->
                        <input type="radio" class="btn-check" name="type" id="type_expense" :value="2" v-model="form.type">
                        <label 
                            class="btn border-0 py-2 small fw-semibold d-flex align-items-center justify-content-center text-danger rounded-pill" 
                            :class="Number(form.type) === 2 ? 'bg-danger-subtle border border-danger-subtle shadow-sm' : 'bg-transparent opacity-50'" 
                            for="type_expense">
                            <i class="fa-solid fa-circle-arrow-up me-2"></i> Expense
                        </label>
                    </div>
                </div>

                <!-- Category Name Input -->
                <div class="mb-4">
                    <div class="form-floating custom-floating">
                        <input 
                            type="text" 
                            class="form-control border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                            id="category_name" 
                            placeholder="e.g. Utilities, Salary, Food" 
                            v-model="form.name" 
                            required
                        >
                        <label for="category_name" class="text-muted ps-2 pt-2">Category Name</label>
                    </div>
                </div>

                <!-- Font Awesome Icon Picker Dropdown -->
                <div class="mb-4">
                    <label class="form-label small text-uppercase fw-bold text-muted d-block mb-2">Category Icon</label>
                    
                    <div class="dropdown">
                        <!-- Selected Icon Trigger Button -->
                        <button 
                            class="btn btn-outline-light border text-dark w-100 d-flex align-items-center justify-content-between p-2 rounded-3 shadow-none"
                            type="button" 
                            data-bs-toggle="dropdown" 
                            data-bs-auto-close="outside"
                            aria-expanded="false"
                        >
                            <div class="d-flex align-items-center gap-3">
                                <div 
                                    class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
                                    :style="{ backgroundColor: form.color || '#0d6efd', width: '36px', height: '36px' }"
                                >
                                    <i :class="form.icon || 'fa-solid fa-icons'"></i>
                                </div>
                                <span class="fw-medium font-monospace">{{ form.icon || 'Select an icon...' }}</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-muted small me-2"></i>
                        </button>

                        <!-- Icon Picker Menu -->
                        <div class="dropdown-menu p-3 shadow-lg border-0 rounded-3 w-100" style="max-height: 320px; overflow-y: auto;">
                            
                            <!-- Type-to-Search Input -->
                            <div class="input-group input-group-sm mb-3 sticky-top bg-white pt-1">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input 
                                    type="text" 
                                    class="form-control bg-light border-start-0 shadow-none" 
                                    placeholder="Type to search icons..." 
                                    v-model="iconSearchQuery"
                                >
                            </div>

                            <!-- Icon Grid Grid Options -->
                            <div class="d-flex flex-wrap gap-2 justify-content-start">
                                <button 
                                    v-for="icon in filteredIcons" 
                                    :key="icon"
                                    type="button"
                                    class="btn btn-sm border-0 rounded-3 p-2 d-flex align-items-center justify-content-center"
                                    :class="form.icon === icon ? 'btn-primary text-white' : 'btn-light text-secondary'"
                                    style="width: 42px; height: 42px;"
                                    :title="icon"
                                    @click="form.icon = icon"
                                >
                                    <i :class="icon" class="fs-5"></i>
                                </button>
                            </div>

                            <!-- Empty State when no match -->
                            <div v-if="filteredIcons.length === 0" class="text-center text-muted small py-3">
                                No icons found matching "{{ iconSearchQuery }}"
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Color Picker Input (Selection Only) -->
                <div class="mb-4">
                    <label for="category_color" class="form-label small text-uppercase fw-bold text-muted d-block mb-2">Category Color</label>
                    <div class="d-flex align-items-center gap-3 border-bottom pb-2">
                        <!-- Native Color Picker Input -->
                        <input 
                            type="color" 
                            class="form-control form-control-color border-0 rounded-circle cursor-pointer shadow-sm" 
                            id="category_color" 
                            v-model="form.color" 
                            title="Choose category color"
                            style="width: 42px; height: 42px; padding: 2px;"
                        >
                        
                        <!-- Read-only Display (Prevents typing, click triggers color picker) -->
                        <input 
                            type="text" 
                            class="form-control border-0 rounded-0 px-2 shadow-none font-monospace text-uppercase bg-transparent cursor-pointer" 
                            :value="form.color || '#000000'"
                            readonly
                            tabindex="-1"
                            onclick="document.getElementById('category_color').click();"
                        >
                    </div>
                </div>

                <!-- Description Input -->
                <div class="mb-4">
                    <div class="form-floating custom-floating">
                        <textarea 
                            class="form-control border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                            id="category_description" 
                            placeholder="Brief description..." 
                            v-model="form.description" 
                            style="height: 100px; resize: none;"
                        ></textarea>
                        <label for="category_description" class="text-muted ps-2 pt-2">Description</label>
                    </div>
                </div>

            </div>

            <!-- Fixed Sticky Footer -->
            <div class="offcanvas-footer p-3 border-top bg-light d-flex align-items-center justify-content-end gap-2 flex-shrink-0 mt-auto">
                <button 
                    type="button" 
                    class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" 
                    data-bs-dismiss="offcanvas"
                >
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-check me-1"></i>
                    Save Category
                </button>
            </div>
        </form>
    </div>
</template>

<script>
    import iconFamilies from '@fortawesome/fontawesome-free/metadata/icon-families.json';

    export default {
        components: { },
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
                    { key: 'name', label: 'Name', sortable: true },
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
                },
                isEditing: false,
                isTableLoading: true,
                form: {
                    id: null,
                    type: 2,
                    name: '',
                    color: '#000000',
                    description: '',
                    icon: ''
                },
                iconSearchQuery: '',
                availableIcons: Object.keys(iconFamilies).map(name => `fa-solid fa-${name}`)
            };
        },
        computed: {
            filteredIcons() {
                if (!this.iconSearchQuery) return this.availableIcons;
                const query = this.iconSearchQuery.toLowerCase();
                return this.availableIcons.filter(icon => icon.toLowerCase().includes(query));
            }
        },
        methods:{
            clearRowHighlights() {
                // Replace 'categories' with your actual data property name if different
                const list = this.categories || this.items || [];
                
                if (Array.isArray(list)) {
                    list.forEach(item => {
                        delete item._rowVariant;
                    });
                }
            },
            async loadCategories(){
                this.categories = await this.fetchRecords({ url: this.utilityUrl });
                this.isTableLoading = false;
            },
            add() {
                this.isEditing = false;
                this.form = {
                        id: null,
                        type: 2,
                        name: '',
                        color: '#000000',
                        description: ''
                    };
                this.openModal('categoryCanvas');

            },
            edit(item){
                this.clearRowHighlights();
                item._rowVariant = 'warning';

                this.fetchItem({
                    url: `/api/categories/${item.id}`,
                    errorMessage: 'Failed to retrieve category details.',
                    callback: (response) => {
                        this.form = response;
                        this.isEditing = true;
                        this.openModal('categoryCanvas');
                    }
                })
            },
            submitForm(e){
                e.preventDefault();

                this.saveItem({
                    url: this.isEditing ? `/api/categories/${this.form.id}` : '/api/categories',
                    method: this.isEditing ? 'put' : 'post',
                    data: this.form,
                    successMessage: this.isEditing ? 'Category updated successfully!' : 'Category saved successfully!',
                    errorMessage: this.isEditing ? 'Failed to update category.' : 'Failed to save category.',
                    callback: () => {
                        e.target.reset();
                        this.loadCategories();
                        this.closeModal('categoryCanvas');
                        this.clearRowHighlights();
                    }
                });
            },
            remove(item){
                this.clearRowHighlights();
                item._rowVariant = 'danger';

                this.deleteItem({
                    url: `/api/categories/${item.id}`,
                    successMessage: 'Category deleted successfully.',
                    errorMessage: 'Failed to delete category.',
                    callback: () => {
                        this.loadCategories();
                    }
                })
            }
        },
        mounted(){
            this.loadCategories();
        }
    }
</script>
