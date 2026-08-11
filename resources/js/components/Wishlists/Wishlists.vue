<template>
    <div class="container-fluid">
        <div class="card shadow-sm border-1 rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-primary-subtle text-primary me-3 px-3 py-2 rounded">
                    <i class="fa-solid fa-heart me-2"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">Wishlists</h5>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <DataTable
                    :items="wishlists"
                    :fields="fields"
                    :utilityUrl="utilityUrl"
                    :module="module"
                    @select-item="selectedItem = $event"
                    @reload-table="loadWishlists"
                    :formatters="formatters"
                >
                </DataTable>
            </div>
        </div>
    </div>

    <ModalForm 
        :module="module"
        :formFields="formFields"
        :utilityUrl="utilityUrl"
        :selected-item="selectedItem"
        @reload-table="loadWishlists"
    >
    </ModalForm>
</template>

<script>
// import AddButton from '../Shared/AddButton.vue'
import DataTable from '../Shared/DataTable.vue'
import ModalForm from '../Shared/ModalForm.vue'

    export default {
        components: { DataTable, ModalForm },
        data() {
            return {
                module: 'wishlist',
                utilityUrl: '/api/wishlists',
                selectedItem: null,
                wishlists: [],
                fields: [
                    { key: 'item', label: 'Item', sortable: true },
                    { key: 'priority', label: 'Priority', sortable: true },
                    { key: 'estimated_cost', label: 'Estimated Cost', sortable: true },
                    { key: 'notes', label: 'Notes', sortable: true },
                    { key: 'status', label: 'Status', sortable: true },
                    { key: 'actions', label: 'Actions' }
                ],
                formFields: [
                    { key: 'id', label: 'ID', type: 'input', hidden: true, inputType: "text" },
                    { key: 'item', label: 'Item', type: 'input', required: true},
                    { key: 'priority', label: 'Priority', type: 'select', required: true, options:[
                        { value: 1, label: 'Low' },
                        { value: 2, label: 'Medium' },
                        { value: 3, label: 'High' },
                    ]},
                    { key: 'estimated_cost', label: 'Estimated Cost', type: 'input', inputType: 'number', required: true, placeholder: '₱ 0.00'},
                    { key: 'notes', label: 'Notes', type: 'textarea'},
                    { key: 'status', label: 'Status', type: 'select', required: true, options:[
                        { value: 1, label: 'Planned' },
                        { value: 2, label: 'To Buy' },
                        { value: 3, label: 'To Fix' },
                        { value: 4, label: 'In Progress' },
                        { value: 5, label: 'Waiting' },
                        { value: 6, label: 'On Hold' },
                        { value: 7, label: 'Completed' },
                        { value: 8, label: 'Cancelled' },
                    ]}
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
                        
                        // Uses formatPeso if available, or falls back to Intl number formatting
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
                    url: '/api/wishlists',
                });
            },
            resetSelection() {
                this.selectedItem = {};
            }
        },
        mounted() {
            this.loadWishlists()
            this.form = this.setFormVariables(this.formFields)
        }
    }
</script>
