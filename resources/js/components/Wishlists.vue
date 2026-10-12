<template>
    <div class="container-fluid">
        <div class="card shadow-sm border border-secondary-subtle rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-primary-subtle text-primary me-3 px-3 py-2 rounded-pill">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">Wishlists</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4 align-items-center">
                    <div class="col-md-8 d-flex gap-2">
                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" @click="add()">
                            <i class="fa-solid fa-plus me-2"></i>Add Wishlist
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
                        :items="wishlists"
                        :fields="fields"
                        :per-page="perPage"
                        :current-page="currentPage"
                        :filter="filter"
                        :busy="isTableLoading"
                        hover
                        small
                        class="align-middle border-top"
                        thead-class="table-light text-uppercase small fw-bold"
                        :tbody-tr-class="getRowClass"
                        :tbody-transition-props="tableTransition"
                    >
                        <template #table-busy>
                            <div class="text-center text-primary my-4 py-4 bg-dark-subtle rounded-3">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                <span class="fw-medium text-secondary">Loading wishlists...</span>
                            </div>
                        </template>

                        <template #cell(estimated_cost)="row">
                            <div class="px-3 py-1 rounded-pill fw-semibold text-end font-monospace bg-dark bg-opacity-10 text-dark-emphasis">
                                {{ formatPeso(row.item.estimated_cost ?? 0) }}
                            </div>
                        </template>

                    </BTable>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DataTable from './Shared/DataTable.vue'

export default {
    components: { DataTable },
    data() {
        return {
            // loading vars
            isLoading:true,
            module: 'wishlist',
            utilityUrl: '/api/wishlists',
            selectedItem: null,
            isTableLoading: true,
            wishlists: [],
            fields: [
                { key: 'item', label: 'Item', sortable: true },
                { key: 'category_label', label: 'Category', sortable: true },
                { key: 'priority', label: 'Priority', sortable: true },
                { key: 'estimated_cost', label: 'Estimated Cost', sortable: true },
                { key: 'notes', label: 'Notes', sortable: true },
                { key: 'status', label: 'Status', sortable: true },
                { key: 'formatted_target_date', label: 'Target Date', sortable: true },
                { key: 'actions', label: '' }
            ],
            formFields: [
                { key: 'id', label: 'ID', type: 'input', hidden: true, inputType: 'text' },
                { key: 'item', label: 'Item', type: 'input', required: true, placeholder: 'e.g. New Shoes, Smart Watch' },
                {
                    key: 'type',
                    label: 'Type',
                    type: 'select',
                    required: true,
                    options: [
                    { value: 1, label: 'Home Improvement & Renovation' },
                    { value: 2, label: 'Home Appliances' },
                    { value: 3, label: 'Furniture & Home Decor' },
                    { value: 4, label: 'Electronics & Tech' },
                    { value: 5, label: 'Fashion & Lifestyle' },
                    { value: 6, label: 'Hobbies & Creative' },
                    { value: 7, label: 'Experiences & Travel' },
                    { value: 8, label: 'Gifts & Occasions' },
                    { value: 9, label: 'Everyday & Maintenance' }
                    ]
                },
                {
                    key: 'category',
                    label: 'Category',
                    type: 'select',
                    placeholder: 'Select location or context',
                    options: [
                    { value: 1, label: 'Exterior & Building' },
                    { value: 2, label: 'Living Room' },
                    { value: 3, label: 'Kitchen & Dining' },
                    { value: 4, label: 'Bedroom & Bath' },
                    { value: 5, label: 'Home Office / Workstation' },
                    { value: 6, label: 'Mobile & On-the-Go' },
                    { value: 7, label: 'Studio & Creative Work' },
                    { value: 8, label: 'Network & Tech Infrastructure' },
                    { value: 9, label: 'Personal Care & Wardrobe' },
                    { value: 10, label: 'Leisure & Recreation' }
                    ]
                },
                { 
                    key: 'priority', 
                    label: 'Priority', 
                    type: 'select', 
                    required: true, 
                    options: [
                        { value: 1, label: 'Low' },
                        { value: 2, label: 'Medium' },
                        { value: 3, label: 'High' }
                    ]
                },
                { key: 'estimated_cost', label: 'Estimated Cost', type: 'input', inputType: 'number', required: true, placeholder: '₱ 0.00' },
                { key: 'target_date', label: 'Target Date', type: 'input', inputType: 'date' },
                { key: 'notes', label: 'Notes', type: 'textarea', placeholder: 'Add optional notes or descriptions...' },
                { 
                    key: 'status', 
                    label: 'Status', 
                    type: 'select', 
                    required: true, 
                    options: [
                        { value: 1, label: 'Idea / Planning' },
                        { value: 2, label: 'Saving / Budgeted' },
                        { value: 3, label: 'Ready to Buy / Book' },
                        { value: 4, label: 'Ordered / Booked' },
                        { value: 5, label: 'In Progress / Work Underway' },
                        { value: 6, label: 'On Hold' },
                        { value: 7, label: 'Completed / Acquired' },
                        { value: 8, label: 'Cancelled' }
                    ]
                }
            ],
            formatters: {
                priority: (val) => {
                    const priorities = {
                        1: { label: 'Low', badge: 'bg-secondary' },
                        2: { label: 'Medium', badge: 'bg-warning text-dark' },
                        3: { label: 'High', badge: 'bg-danger' }
                    };
                    const p = priorities[val];
                    return p 
                        ? `<span class="badge ${p.badge} px-2 py-1">${p.label}</span>` 
                        : '<span class="text-muted">-</span>';
                },

                status: (val) => {
                    const statuses = {
                        1: { label: 'Planned', badge: 'bg-light text-dark border' },
                        2: { label: 'To Buy', badge: 'bg-info text-dark' },
                        3: { label: 'To Fix', badge: 'bg-warning text-dark' },
                        4: { label: 'In Progress', badge: 'bg-primary' },
                        5: { label: 'Waiting', badge: 'bg-secondary' },
                        6: { label: 'On Hold', badge: 'bg-dark' },
                        7: { label: 'Completed', badge: 'bg-success' },
                        8: { label: 'Cancelled', badge: 'bg-danger' }
                    };
                    const s = statuses[val];
                    return s 
                        ? `<span class="badge ${s.badge} rounded-pill px-2 py-1">${s.label}</span>` 
                        : '<span class="text-muted">-</span>';
                },

                estimated_cost: (val) => {
                    const num = parseFloat(val);
                    if (isNaN(num) || num === null) return '₱0.00';
                    
                    return typeof this.formatPeso === 'function' 
                        ? this.formatPeso(num) 
                        : `₱${num.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
            }
        }
    },
    methods: {
        async loadWishlists() {
            this.wishlists = await this.fetchRecords({
                url: '/api/wishlists'
            });
            this.isTableLoading = false;
        },
        resetSelection() {
            this.selectedItem = {};
        }
    },
    mounted() {
        this.loadWishlists();
    }
}
</script>