<template>
    <div class="container-fluid table-responsive my-2">
        <BFormInput
            v-model="filter"
            placeholder="Type to Search..."
            class="mb-3"
            size="md"
            style="width:400px;"
        />
        <BTable
            :items="items"
            :fields="fields"
            :per-page="perPage"
            :current-page="currentPage"
            :filter="filter"
            striped
            hover
            bordered0319
        >
            <template #cell(actions)="row">
                <BButton size="sm" variant="warning" @click="onEdit(row.item.id)">Edit</BButton>&nbsp;
                <!-- <BButton size="sm" variant="danger" @click="onDelete(row.item.id)">Delete</BButton> -->
            </template>
        </BTable>
        <div class="d-flex justify-content-between align-items-center mb-2 py-3">
            <p>
                Showing {{ startRow }}–{{ endRow }} of {{ items.length }} rows
            </p>
            <BPagination
                v-model="currentPage"
                :total-rows="items.length"
                :per-page="perPage"
                align="end"
                first-text="⏮"
                prev-text="Prev"
                next-text="Next"
                last-text="⏭"
            />
        </div>
    </div>
</template>

<script>
export default{
    props: {
        items: Array,
        fields: Array,
        onEdit: Function,
        // onDelete: Function
    },
    data() {
        return {
            perPage: 10,
            currentPage: 1,
            filter: '',
            isLoading: true
        }
    },
    methods: {
        
    },
    mounted() {
        
    },
    computed: {
        paginatedItems() {
            const start = (this.currentPage - 1) * this.perPage.value;
            const end = start + this.perPage.value;
            return this.items.slice(start, end);
        },
        startRow() {
            return this.items.length === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
        },
        endRow() {
            return Math.min(this.currentPage * this.perPage, this.items.length)
        }
    }
}
</script>
