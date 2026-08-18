<template>
    <div class="container-fluid">
        <div class="card shadow-sm border-1 rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <!-- Left Section: Icon & Title -->
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-primary-subtle text-primary me-3 px-3 py-2 rounded">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">Wishlists</h5>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <DataTable
                    :items="wishlists"
                    :fields="fields"
                    :form-fields="formFields"
                    :utilityUrl="utilityUrl"
                    :module="module"
                    :formatters="formatters"
                    @select-item="selectedItem = $event"
                    @reload-table="loadWishlists"
                >
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script>
import DataTable from '../Shared/DataTable.vue'

export default {
    components: { DataTable },
    data() {
        return {
            module: 'wishlist',
            utilityUrl: '/api/wishlists',
            selectedItem: null,
            wishlists: [],
            fields: [
                { key: 'item', label: 'Item', sortable: true },
                { key: 'type_label', label: 'Type', sortable: true },
                { key: 'category_label', label: 'Category', sortable: true },
                { key: 'priority', label: 'Priority', sortable: true },
                { key: 'estimated_cost', label: 'Estimated Cost', sortable: true },
                { key: 'notes', label: 'Notes', sortable: true },
                { key: 'status', label: 'Status', sortable: true },
                { key: 'formatted_target_date', label: 'Target Date', sortable: true },
                { key: 'formatted_created_at', label: 'Date Added', sortable: true },
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