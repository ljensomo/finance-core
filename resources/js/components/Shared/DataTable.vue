<template>
    <div class="container-fluid table-responsive my-2">
        <BFormInput
            v-model="filter"
            placeholder="Type to Search..."
            class="mb-3"
            size="md"
            style="width:400px;"
        />
        <b-table
            :items="formattedItems"
            :fields="fields"
            :per-page="perPage"
            :current-page="currentPage"
            :filter="filter"
            striped
            hover
            bordered
            show-empty
        >
            <template #cell()="data">
                <span v-html="data.value"></span>
            </template>
            <template #cell(actions)="row">
                <BButton size="sm" variant="warning" @click="handleEdit(row.item.id)"><i class="fa-solid fa-edit me-2"></i>Edit</BButton>&nbsp;
                <BButton size="sm" variant="danger" @click="handleDelete(row.item.id)"><i class="fa-solid fa-trash me-2"></i>Delete</BButton>
            </template>
        </b-table>
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
        utilityUrl: String,
        module: String,
        formatters: Object
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
        async handleEdit(itemId){
            const record = await this.fetchItem({ url: this.utilityUrl+`/${itemId}` });
            this.$emit('select-item', record);
            this.openModal(this.module + 'Modal');
        },
        handleDelete(itemId){
            this.deleteItem({
                url: this.utilityUrl+`/${itemId}`,
                callback:() => {
                    this.$emit('reload-table');
                }
            })
        }
    },
    mounted() {
        
    },
    computed: {
        paginatedItems() {
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return this.items.slice(start, end);
        },
        startRow() {
            return this.items.length === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
        },
        endRow() {
            return Math.min(this.currentPage * this.perPage, this.items.length)
        },
        formattedItems() {
            return this.items.map(item => {
                const formatted = { ...item };
                for (const key in this.formatters) {
                    if(key in item){
                        formatted[key] = this.formatters[key](item[key]);
                    }
                }
                return formatted;
            })
        }
    }
}
</script>
