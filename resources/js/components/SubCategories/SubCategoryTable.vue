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
            :items="subcategories"
            :fields="fields"
            :per-page="perPage"
            :current-page="currentPage"
            :filter="filter"
            striped
            hover
            bordered
        >
        </BTable>
    </div>
</template>

<script>
export default{
    data() {
        return {
            subcategories: [],
            perPage: 10,
            currentPage: 1,
            filter: '',
            fields: [
                { key: 'category.name', label: 'Category', sortable: true },
                { key: 'name', label: 'Sub-Category', sortable: true },
                { key: 'description', label: 'Description', sortable: true },
                { key: 'actions', label: 'Actions' }
            ]
        }
    }
}
</script>
