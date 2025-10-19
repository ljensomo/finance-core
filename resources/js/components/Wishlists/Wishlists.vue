<template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-heart me-2"></i>Wishlists</div>
                    <div class="card-body">
                        <AddButton :module="module" @add="resetSelection"></AddButton>
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
import AddButton from '../Shared/AddButton.vue'
import DataTable from '../Shared/DataTable.vue'
import ModalForm from '../Shared/ModalForm.vue'

    export default {
        components: { DataTable, ModalForm, AddButton },
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
                        switch (val) {
                            case 1:
                                return 'Low';
                            case 2:
                                return 'Medium';
                            case 3:
                                return 'High';
                        }
                    },
                    status: (val) => {
                        switch (val) {
                            case 1:
                                return 'Planned';
                            case 2:
                                return 'To Buy';
                            case 3:
                                return 'To Fix';
                            case 4:
                                return 'In Progress';
                            case 5:
                                return 'Waiting';
                            case 6:
                                return 'On Hold';
                            case 7:
                                return 'Completed';
                            case 8:
                                return 'Cancelled';
                        }
                    },
                    estimated_cost: (val) => { return this.formatPeso(val) }
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
