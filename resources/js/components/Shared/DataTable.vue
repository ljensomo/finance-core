<template>
<div class="container-fluid">
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-8">
            <AddButton :module="module" @click="handleAdd" />
        </div>
        <div class="col-md-4">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                </span>
                <BFormInput
                    v-model="filter"
                    placeholder="Search records..."
                    class="border-start-0 ps-0"
                />
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <b-table
                :items="formattedItems"
                :fields="fields"
                :per-page="perPage"
                :current-page="currentPage"
                :filter="filter"
                striped
                hover
                class="align-middle mb-0 border-top"
                thead-class="table-light text-uppercase small fw-bold text-muted"
            >
                <template #cell()="data">
                    <span v-if="data.field.key === 'category.name'">
                        <div class="d-flex align-items-center">
                            <div class="category-icon-sm me-2 d-flex align-items-center justify-content-center rounded-circle" :class="getCategoryStyle(data.value).colorClass">
                                <i :class="getCategoryStyle(data.value).icon"></i>
                            </div>
                            <span class="fw-medium text-secondary">{{ data.value }}</span>
                        </div>
                    </span>
                    <span v-else>
                        <span v-if="data.value.class" :class="data.value.class" v-html="data.value.value"></span>
                        <span v-else v-html="data.value"></span>
                    </span>
                </template>
                
                <template #cell(actions)="row">
                    <div class="d-flex gap-1">
                        <BButton v-if="hasView" size="sm" variant="light" class="text-info border" @click="handleView(row.item.id)">
                            <i class="fa-solid fa-eye"></i>
                        </BButton>
                        <BButton size="sm" variant="light" class="text-warning border" @click="handleEdit(row.item.id)">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </BButton>
                        <BButton size="sm" variant="light" class="text-danger border" @click="handleDelete(row.item.id)">
                            <i class="fa-solid fa-trash"></i>
                        </BButton>
                    </div>
                </template>
            </b-table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center pt-3">
        <p class="text-muted small mb-0">
            Showing <strong>{{ startRow }}–{{ endRow }}</strong> of <strong>{{ items.length }}</strong> entries
        </p>
        <BPagination
            v-model="currentPage"
            :total-rows="items.length"
            :per-page="perPage"
            class="mb-0"
            first-number
            last-number
        />
    </div>
</div>
</template>

<script>
    import AddButton from './AddButton.vue';

    export default{
        components: { AddButton },
        props: {
            items: Array,
            fields: Array,
            utilityUrl: String,
            module: String,
            formatters: Object,
            hasView: false,
            viewUrl: String,
        },
        data() {
            return {
                perPage: 10,
                currentPage: 1,
                filter: '',
                isLoading: true,
                isEdit: false,
            }
        },
        methods: {
            async handleEdit(itemId){
                this.$emit('isEditing', true);
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
            },
            handleView(itemId){
                if(this.viewUrl){
                    this.$router.push({ path: this.viewUrl, query: { id: itemId } });
                }
            },
            handleAdd(){
                this.$emit('addFunction');
            },
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

<style scoped>
.category-icon-sm {
  width: 28px;
  height: 28px;
  font-size: 0.75rem;
}

/* Custom Subtles (if not in your Bootstrap version) */
.bg-orange-subtle { background-color: #fff3e0; }
.text-orange { color: #ef6c00; }

.bg-purple-subtle { background-color: #f3e5f5; }
.text-purple { color: #7b1fa2; }

.bg-blue-subtle { background-color: #e3f2fd; }
.text-blue { color: #1976d2; }

.bg-indigo-subtle { background-color: #e8eaf6; }
.text-indigo { color: #3f51b5; }

/* Ensure icons are centered */
.category-icon-sm i {
  display: block;
}
</style>
