<template>
    <button class="btn btn-primary" @click="triggerAdd">
        <i class="fa-solid fa-plus"></i>
        Add {{ this.capitalizeFirstLetter(this.module) }}
    </button>
    <hr>
</template>

<script>
export default {
    props: {
        module: String,
    },
    data() {
        return {
            modalId: ''
        }
    },
    methods: {
        triggerAdd(){
            this.$emit('add');
            this.openModal(this.modalId);
        }
    },
    mounted() {
        this.modalId = this.module + 'Modal';
    },
}
</script>
