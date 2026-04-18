<template>
    <div
        class="modal fade"
        :id="modalId"
        tabindex="-1"
        :aria-labelledby="modalLabel"
        aria-hidden="true"
        data-bs-backdrop="static"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" :id="modalLabel">
                        {{ isEdit ? "Edit" : "Add" }} {{ moduleName }}
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>

                <form
                    @submit.prevent="handleSubmit"
                    :id="module + 'Form'"
                    novalidate
                >
                    <input type="hidden" name="id" v-model="form.id" />

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div
                                v-for="(field, key) in formFields"
                                :key="key"
                                :class="
                                    field.hidden
                                        ? 'd-none'
                                        : field.col || 'col-12'
                                "
                            >
                                <div v-if="!field.hidden">
                                    <label
                                        :for="field.key"
                                        class="form-label fw-semibold"
                                    >
                                        {{ field.label }}
                                        <span
                                            v-if="field.required"
                                            class="text-danger"
                                            >*</span
                                        >
                                    </label>

                                    <input
                                        v-if="field.type === 'input'"
                                        :type="field.inputType"
                                        class="form-control"
                                        :id="field.key"
                                        :placeholder="
                                            field.placeholder ||
                                            `Enter ${field.label.toLowerCase()}`
                                        "
                                        :required="field.required"
                                        :step="
                                            field.inputType === 'number'
                                                ? '0.01'
                                                : null
                                        "
                                        v-model="form[field.key]"
                                    />

                                    <select
                                        v-else-if="field.type === 'select'"
                                        class="form-select"
                                        :id="field.key"
                                        :required="field.required"
                                        v-model="form[field.key]"
                                    >
                                        <option value="" disabled selected>
                                            Select {{ field.label }}
                                        </option>
                                        <option
                                            v-for="opt in field.options"
                                            :key="opt.value"
                                            :value="opt.value"
                                        >
                                            {{ opt.label }}
                                        </option>
                                    </select>

                                    <textarea
                                        v-else-if="field.type === 'textarea'"
                                        class="form-control"
                                        :id="field.key"
                                        rows="3"
                                        :placeholder="`Describe the ${field.label.toLowerCase()}...`"
                                        :required="field.required"
                                        v-model="form[field.key]"
                                    ></textarea>

                                    <div class="invalid-feedback">
                                        Please provide a valid
                                        {{ field.label }}.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button
                            type="button"
                            class="btn btn-outline-secondary px-4"
                            data-bs-dismiss="modal"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary px-4"
                            :disabled="isLoading"
                        >
                            <span
                                v-if="isLoading"
                                class="spinner-border spinner-border-sm me-2"
                                role="status"
                            ></span>
                            <i v-else class="fa-solid fa-check me-2"></i>
                            {{ isLoading ? "Saving..." : `Save ${moduleName}` }}
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
