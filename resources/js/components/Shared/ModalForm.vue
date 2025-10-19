<template>
    <div class="modal fade" :id="modalId" tabindex="-1" :aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" :id="modalLabel">{{ moduleName }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="handleSubmit" :id="module + 'Form'">
                    <input type="hidden" name="id" v-model="form.id">
                    <div class="modal-body">
                        <div v-for="(field, key) in formFields" :key="key">
                            <div :class="[field.hidden ? '' : 'mb-3']">
                                <label v-if="!field.hidden" :for="field.key" class="form-label">{{ field.label }}</label>
                                <input 
                                    v-if="field.type === 'input'"
                                    :type="field.inputType"
                                    class="form-control"
                                    :id="field.key"
                                    :name="field.key"
                                    :placeholder="field.placeholder ? field.placeholder : 'Enter ' + field.label"
                                    :hidden="field.hidden === true"
                                    :required="field.required === true"
                                    v-model="form[field.key]" 
                                />
                                <select 
                                    v-else-if="field.type === 'select'"
                                    :name="field.key"
                                    :id="field.key"
                                    class="form-select"
                                    :required="field.required === true"
                                    v-model="form[field.key]"
                                >
                                    <option selected disabled value="">Choose...</option>
                                    <option v-for="option in field.options" :key="option.value" :value="option.value">{{ option.label }}</option>
                                </select>
                                <textarea
                                    v-else-if="field.type === 'textarea'"
                                    :name="field.key"
                                    :id="field.key"
                                    class="form-control"
                                    :cols="30"
                                    :rows="3"
                                    :placeholder="'Enter ' + field.label"
                                    :required="field.required === true"
                                    v-model="form[field.key]"
                                    >
                                </textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save {{ moduleName }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default{
    props: {
        module: String,
        selectedItem: Object,
        formFields: Object,
        formVariables: Object,
        utilityUrl: String,
    },
    data() {
        return {
            isEditing: false,
            // modal properties
            moduleName: '',
            modalId: '',
            modalLabel: '',
            form: []
        }
    },
    methods: {
        handleSubmit(e){
            this.submitForm(e, {
                updateUrl: this.utilityUrl + `/${this.form.id}`,
                addUrl: this.utilityUrl,
                title: this.moduleName,
                modalId: this.modalId,
                form: this.form,
                callback: () => {
                    this.isEditing = false;
                    this.$emit('reload-table');
                    // this.cleanForm()
                }
            })
        },
    },
    mounted() {
        this.moduleName = this.capitalizeFirstLetter(this.module);
        this.modalId = this.module + 'Modal';
        this.modalLabel = this.module + 'ModalLabel';
        this.formId = this.module + 'Form';
    },
    watch: {
        selectedItem: {
            immediate: true,
            handler(value) {
                if (value) {
                    this.form = { ...value }
                    this.isEditing = this.isEmptyObject(value) ? false : true;
                }
            }
        },
    }
}
</script>
