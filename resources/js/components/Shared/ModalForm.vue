<template>
    <div class="modal fade" :id="modalId" tabindex="-1" :aria-labelledby="modalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" :id="modalLabel">
                        <i class="fa-solid fa-edit me-2 text-primary"></i>
                        {{ isEdit ? 'Edit' : 'Add' }} {{ moduleName }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form @submit.prevent="handleSubmit" :id="module + 'Form'" novalidate>
                    <input type="hidden" name="id" v-model="form.id" />

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <template v-for="(field, key) in formFields" :key="key">
                                <div v-if="!field.hidden" :class="field.col || 'col-12'">
                                    <label :for="field.key" class="form-label small text-uppercase fw-bold text-muted mb-1">
                                        {{ field.label }}
                                        <span v-if="field.required" class="text-danger">*</span>
                                    </label>

                                    <input v-if="field.type === 'input'" :type="field.inputType" class="form-control bg-light border-0" :id="field.key"
                                        :placeholder="field.placeholder || `Enter ${field.label.toLowerCase()}`"
                                        :required="field.required" :step="field.inputType === 'number' ? '0.01' : null"
                                        v-model="form[field.key]" />

                                    <select v-else-if="field.type === 'select'" class="form-select bg-light border-0" :id="field.key"
                                        :required="field.required" v-model="form[field.key]">
                                        <option value="" disabled>Choose...</option>
                                        <option v-for="opt in field.options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                    </select>

                                    <textarea v-else-if="field.type === 'textarea'" class="form-control bg-light border-0" :id="field.key" rows="2"
                                        :placeholder="`Enter ${field.label.toLowerCase()}...`"
                                        :required="field.required" v-model="form[field.key]"></textarea>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-outline-secondary rounded-pill text-muted px-4" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm rounded-pill" :disabled="isLoading">
                            <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                            {{ isLoading ? 'Saving...' : `Save ${moduleName}` }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
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
            moduleName: "",
            modalId: "",
            modalLabel: "",
            form: {},
        };
    },
    methods: {
        handleSubmit(e) {
            this.formFields.forEach((field) => {
                if (field.value !== undefined) {
                    this.form[field.key] = field.value;
                }
            });
            console.log("Submitting form:", this.form);
            this.submitForm(e, {
                updateUrl: this.utilityUrl + `/${this.form.id}`,
                addUrl: this.utilityUrl,
                title: this.moduleName,
                modalId: this.modalId,
                form: this.form,
                callback: () => {
                    this.isEditing = false;
                    this.$emit("reload-table");
                    // this.cleanForm()
                },
            });
        },
    },
    mounted() {
        this.moduleName = this.capitalizeFirstLetter(this.module);
        this.modalId = this.module + "Modal";
        this.modalLabel = this.module + "ModalLabel";
        this.formId = this.module + "Form";
    },
    watch: {
        selectedItem: {
            immediate: true,
            handler(value) {
                if (value) {
                    this.form = { ...value };
                    this.isEditing = this.isEmptyObject(value) ? false : true;
                }
            },
        },
    },
};
</script>
