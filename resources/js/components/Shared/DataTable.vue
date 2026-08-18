<template>
  <div class="container-fluid">
    <!-- Top Bar -->
    <div class="row g-3 mb-4 align-items-center">
      <div class="col-md-8">
        <BButton 
          variant="primary" 
          size="sm" 
          class="d-flex align-items-center gap-2 shadow-sm"
          @click="handleAdd"
        >
          <i class="fa-solid fa-plus"></i>
          <span>Add New Record</span>
        </BButton>
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

    <!-- Data Table Card -->
    <div class="card border rounded-3 shadow-sm">
      <div class="table-responsive overflow-visible p-0">
        <b-table
          :items="formattedItems"
          :fields="fields"
          :per-page="perPage"
          :current-page="currentPage"
          :filter="filter"
          hover
          responsive
          class="align-middle mb-0 table-hover table-bordered table-striped"
          thead-class="bg-secondary text-uppercase small fw-semibold text-secondary"
        >
          <!-- Category Column Slot -->
          <template #cell(category.name)="{ value }">
            <div class="d-flex align-items-center py-1">
              <div 
                class="category-icon-sm me-2 d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" 
                :class="getCategoryStyle(value).colorClass"
                style="width: 32px; height: 32px;"
              >
                <i :class="getCategoryStyle(value).icon"></i>
              </div>
              <span class="fw-medium text-dark">{{ value }}</span>
            </div>
          </template>

          <!-- Dynamic Fallback Slot -->
          <template #cell()="data">
            <span v-if="data.value && data.value.class" :class="data.value.class" v-html="data.value.value"></span>
            <span v-else v-html="data.value"></span>
          </template>
          
          <!-- Actions Column Slot -->
          <template #cell(actions)="{ item }">
            <div class="d-flex align-items-center justify-content" @click.stop>
              <BDropdown 
                size="sm" 
                variant="light" 
                class="action-dropdown" 
                toggle-class="btn-icon border text-muted shadow-none"
                no-caret
                right
                boundary="viewport"
                :popper-opts="{ strategy: 'fixed' }"
              >
                <template #button-content>
                  <i class="fa-solid fa-ellipsis-vertical"></i>
                </template>

                <BDropdownItem v-if="hasView" @click="handleView(item)">
                  <i class="fa-solid fa-eye text-info me-2 style-width-icon"></i>
                  <span>View Details</span>
                </BDropdownItem>

                <BDropdownItem @click="openSubtasksDrawer(item)">
                  <i class="fa-solid fa-sidebar text-primary me-2 style-width-icon"></i>
                  <span>Sub-tasks & Remarks</span>
                </BDropdownItem>

                <BDropdownItem @click="handleEdit(item._raw || item)">
                  <i class="fa-solid fa-pen-to-square text-warning me-2 style-width-icon"></i>
                  <span>Edit Item</span>
                </BDropdownItem>

                <BDropdownDivider />

                <BDropdownItem @click="handleDelete(item.id)" variant="danger">
                  <i class="fa-solid fa-trash text-danger me-2 style-width-icon"></i>
                  <span class="text-danger">Delete Item</span>
                </BDropdownItem>
              </BDropdown>
            </div>
          </template>
        </b-table>
      </div>
    </div>

    <!-- Pagination Footer -->
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

    <!-- Dynamic Add / Edit Record Drawer -->
    <BOffcanvas
      v-model="isAddDrawerOpen"
      placement="end"
      header-class="border-bottom bg-light py-3 px-4"
      body-class="p-4"
      style="width: 440px;"
    >
      <template #title>
        <div class="d-flex align-items-center gap-2">
          <i :class="isEditMode ? 'fa-solid fa-pen-to-square text-warning' : 'fa-solid fa-circle-plus text-primary'"></i>
          <span class="fw-semibold fs-6">{{ isEditMode ? 'Edit Record' : 'Add New Record' }}</span>
        </div>
      </template>

      <form @submit.prevent="handleSubmit" class="d-flex flex-column h-100 justify-content-between">
        <div class="d-flex flex-column gap-3 overflow-auto pe-1">
          <template v-for="field in visibleFormFields" :key="field.key">
            
            <!-- Standard Input Fields -->
            <div v-if="field.type === 'input'">
              <label class="form-label small fw-semibold text-secondary">
                {{ field.label }}
                <span v-if="field.required" class="text-danger">*</span>
              </label>
              <BFormInput
                v-model="form[field.key]"
                :type="field.inputType || 'text'"
                :placeholder="field.placeholder || ''"
                :required="field.required"
              />
            </div>

            <!-- Select Fields -->
            <div v-else-if="field.type === 'select'">
              <label class="form-label small fw-semibold text-secondary">
                {{ field.label }}
                <span v-if="field.required" class="text-danger">*</span>
              </label>
              <BFormSelect
                v-model="form[field.key]"
                :options="normalizeOptions(field.options)"
                :required="field.required"
              />
            </div>

            <!-- Textarea Fields -->
            <div v-else-if="field.type === 'textarea'">
              <label class="form-label small fw-semibold text-secondary">
                {{ field.label }}
                <span v-if="field.required" class="text-danger">*</span>
              </label>
              <textarea
                v-model="form[field.key]"
                class="form-control"
                rows="3"
                :placeholder="field.placeholder || 'Add optional notes...'"
                :required="field.required"
              ></textarea>
            </div>

          </template>
        </div>

        <!-- Panel Footer Buttons -->
        <div class="pt-4 border-top mt-auto d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-light border px-3" @click="isAddDrawerOpen = false">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2">
            <i class="fa-solid fa-check"></i>
            <span>{{ isEditMode ? 'Update' : 'Save Record' }}</span>
          </button>
        </div>
      </form>
    </BOffcanvas>

    <!-- Sub-tasks & Remarks Panel -->
    <BOffcanvas
      v-model="isSubtaskDrawerOpen"
      placement="end"
      header-class="border-bottom bg-light py-3 px-4"
      body-class="p-4"
      style="width: 400px;"
    >
      <template #title>
        <div class="d-flex align-items-center gap-2">
          <i class="fa-solid fa-sidebar text-primary"></i>
          <span class="fw-semibold fs-6">Sub-tasks & Remarks</span>
        </div>
      </template>

      <div v-if="selectedItem">
        <div class="bg-light p-3 rounded-3 mb-4 border">
          <div class="small text-muted text-uppercase fw-semibold mb-1">Selected Item</div>
          <div class="fw-bold text-dark fs-6">{{ selectedItem.item || selectedItem.name }}</div>
        </div>

        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold text-dark mb-0">
              <i class="fa-solid fa-list-check me-2 text-secondary"></i>Sub-tasks
            </h6>
            <button class="btn btn-sm btn-outline-primary py-0 px-2" @click="handleAddSubtask">
              <i class="fa-solid fa-plus me-1"></i>Add
            </button>
          </div>
          
          <div v-if="selectedItem.subtasks && selectedItem.subtasks.length" class="list-group list-group-flush border rounded-3">
            <div 
              v-for="(subtask, idx) in selectedItem.subtasks" 
              :key="idx" 
              class="list-group-item d-flex align-items-center justify-content-between py-2"
            >
              <span class="small">{{ subtask.title || subtask }}</span>
              <input type="checkbox" class="form-check-input" v-model="subtask.completed" />
            </div>
          </div>
          <div v-else class="text-muted small italic bg-light p-3 rounded text-center">
            No sub-tasks added yet.
          </div>
        </div>

        <hr class="my-4" />

        <div>
          <h6 class="fw-bold text-dark mb-2">
            <i class="fa-solid fa-comment-dots me-2 text-secondary"></i>Remarks & Notes
          </h6>
          <textarea 
            class="form-control form-control-sm" 
            rows="4" 
            placeholder="Add remarks or notes here..."
            v-model="selectedItem.remarks"
          ></textarea>
          <div class="d-flex justify-content-end mt-2">
            <button class="btn btn-sm btn-primary" @click="saveRemarks">Save Remarks</button>
          </div>
        </div>
      </div>
    </BOffcanvas>
  </div>
</template>

<script>
export default {
    props: {
        items: { type: Array, default: () => [] },
        fields: { type: Array, default: () => [] },
        formFields: { type: Array, default: () => [] },
        utilityUrl: String,
        module: String,
        formatters: Object,
        hasView: { type: Boolean, default: false },
        viewUrl: String
    },
    data() {
        return {
            perPage: 10,
            currentPage: 1,
            filter: '',
            isLoading: false,

            // Drawer Visibility States
            isAddDrawerOpen: false,
            isSubtaskDrawerOpen: false,
            isEditMode: false,
            selectedItem: null,

            // Dynamic Form State
            form: {}
        }
    },
    computed: {
        visibleFormFields() {
            return this.formFields.filter(f => !f.hidden);
        },
        startRow() {
            return this.items.length === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
        },
        endRow() {
            return Math.min(this.currentPage * this.perPage, this.items.length);
        },
        formattedItems() {
            return this.items.map(item => {
                const formatted = { ...item, _raw: item };
                for (const key in this.formatters) {
                    if (key in item) {
                        formatted[key] = this.formatters[key](item[key]);
                    }
                }
                return formatted;
            });
        }
    },
    methods: {
        resetForm() {
            const dynamicForm = {};
            this.formFields.forEach(field => {
                dynamicForm[field.key] = field.defaultValue ?? null;
            });
            this.form = dynamicForm;
        },

        normalizeOptions(options) {
            if (!options) return [];
            return options.map(opt => ({
                value: opt.value,
                text: opt.label || opt.text
            }));
        },

        handleSubmit(e) {
            if (Array.isArray(this.formFields)) {
                this.formFields.forEach((field) => {
                    if (field.value !== undefined) {
                        this.form[field.key] = field.value;
                    }
                });
            }

            console.log("Submitting form:", this.form);

            this.submitForm(e, {
                updateUrl: `${this.utilityUrl}/${this.form.id}`,
                addUrl: this.utilityUrl,
                title: this.moduleName || this.module,
                modalId: this.modalId,
                form: this.form,
                callback: () => {
                    this.isEditing = false;
                    this.isEditMode = false;
                    this.isAddDrawerOpen = false;
                    this.$emit("reload-table");
                    this.resetForm();
                },
            });
        },

        handleAdd() {
            this.isEditMode = false;
            this.resetForm();
            this.isAddDrawerOpen = true;
        },

        handleEdit(item) {
            this.isEditMode = true;
            const dynamicForm = {};
            console.log('formFields', this.formFields);
            this.formFields.forEach(field => {
                dynamicForm[field.key] = item[field.key] !== undefined ? item[field.key] : null;
            });
            console.log('dynamic form:', dynamicForm);
            this.form = dynamicForm;
            this.isAddDrawerOpen = true;
        },

        openSubtasksDrawer(item) {
            this.selectedItem = item;
            this.isSubtaskDrawerOpen = true;
        },

        handleAddSubtask() {
            if (!this.selectedItem.subtasks) {
                this.selectedItem.subtasks = [];
            }
            this.selectedItem.subtasks.push({ title: 'New Sub-task', completed: false });
        },

        saveRemarks() {
            this.$emit('save-remarks', this.selectedItem);
            this.isSubtaskDrawerOpen = false;
        },

        handleView(item) {
            this.$emit('view', item);
        },

        handleDelete(id) {
          this.deleteItem({
            url: `${this.utilityUrl}/${id}`,
            confirmTitle: `Delete ${this.moduleName || this.module}?`,
            callback: () => {
              this.$emit("reload-table");
            },
          });
        },

        getCategoryStyle(value) {
            return {
                colorClass: 'bg-light text-dark',
                icon: 'fa-solid fa-folder'
            };
        }
    },
    mounted() {
        this.resetForm();
    }
}
</script>

<style scoped>
.b-table-sticky-header, .table-responsive, [class*=table-responsive-] {
    margin-bottom: 0 !important;
}
.category-icon-sm {
  width: 28px;
  height: 28px;
  font-size: 0.75rem;
}

.category-icon-sm i {
  display: block;
}

.btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease-in-out;
}

.btn-icon.hover-info:hover { color: #0dcaf0 !important; background-color: #e0f8ff !important; }
.btn-icon.hover-warning:hover { color: #ffc107 !important; background-color: #fff8e6 !important; }
.btn-icon.hover-danger:hover { color: #dc3545 !important; background-color: #ffebe9 !important; }
</style>