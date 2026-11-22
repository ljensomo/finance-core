<template>
    <!-- Page Content Start -->
    <div class="container-fluid">
        <div class="card shadow-sm border border-secondary-subtle rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-primary-subtle text-primary me-3 px-3 py-2 rounded-pill">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">Wishlists</h5>
                </div>
            </div>

            <div class="card-body">
                <!-- Mini Wishlist Dashboard (Static Preview) -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="border border-secondary-subtle rounded-3 p-3 bg-light-subtle h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted fw-semibold">Active Goals</span>
                                <i class="fa-solid fa-piggy-bank text-primary small"></i>
                            </div>
                            <div class="fw-bold fs-5">6</div>
                            <div class="small text-muted mt-1">
                                4 purchase · 2 travel
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="border border-secondary-subtle rounded-3 p-3 bg-light-subtle h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted fw-semibold">Total Saved</span>
                                <i class="fa-solid fa-wallet text-success small"></i>
                            </div>
                            <div class="fw-bold fs-5 font-monospace text-success">
                                ₱48,500.00
                            </div>
                            <div class="small text-muted mt-1">
                                54% of ₱90,000.00
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="border border-secondary-subtle rounded-3 p-3 bg-light-subtle h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted fw-semibold">Remaining Needed</span>
                                <i class="fa-solid fa-bullseye text-warning small"></i>
                            </div>
                            <div class="fw-bold fs-5 font-monospace">
                                ₱41,500.00
                            </div>
                            <div class="small text-muted mt-1">
                                Across active wishlists
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="border border-secondary-subtle rounded-3 p-3 bg-light-subtle h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted fw-semibold">Completed</span>
                                <i class="fa-solid fa-circle-check text-info small"></i>
                            </div>
                            <div class="fw-bold fs-5">
                                3
                            </div>
                            <div class="small text-muted mt-1">
                                Spent ₱27,200.00
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action & Search Toolbar -->
                <div class="row g-3 mb-4 align-items-center">
                    <div class="col-md-7 d-flex flex-wrap gap-2">
                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" @click="add()">
                            <i class="fa-solid fa-plus me-2"></i>Add Wishlist
                        </button>
                        <button
                            v-if="groupedSections.length"
                            type="button"
                            class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                            @click="setAllGroupsExpanded(true)"
                        >
                            <i class="fa-solid fa-angles-down me-1"></i>Expand All
                        </button>
                        <button
                            v-if="groupedSections.length"
                            type="button"
                            class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                            @click="setAllGroupsExpanded(false)"
                        >
                            <i class="fa-solid fa-angles-up me-1"></i>Collapse All
                        </button>
                    </div>

                    <div class="col-md-5 d-flex gap-2 align-items-center">
                        <!-- Search Input Group -->
                        <div class="input-group input-group-sm rounded-pill overflow-hidden shadow-sm">
                            <!-- Icon Prefix (Rounded Start) -->
                            <span class="input-group-text bg-light border-secondary-subtle border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="fa-solid fa-magnifying-glass small"></i>
                            </span>

                            <!-- Search Input (Rounded End) -->
                            <BFormInput
                                v-model="filter"
                                placeholder="Type to Search..."
                                size="sm"
                                class="border-secondary-subtle border-start-0 shadow-none fw-medium text-secondary rounded-end-pill pe-3"
                            />
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="isTableLoading" class="text-center text-primary my-4 py-4 bg-dark-subtle rounded-3">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    <span class="fw-medium text-secondary">Loading wishlists...</span>
                </div>

                <!-- Empty State -->
                <div v-else-if="!groupedSections.length" class="text-center py-5 border border-secondary-subtle rounded-3 bg-light-subtle text-muted">
                    <i class="fa-regular fa-folder-open fs-4 mb-2 d-block"></i>
                    No wishlists found.
                </div>

                <!-- Collapsible Grouped Tables -->
                <div v-else class="d-flex flex-column gap-3">
                    <div
                        v-for="section in groupedSections"
                        :key="section.key"
                        class="border border-secondary-subtle rounded-3 overflow-hidden bg-white"
                    >
                        <!-- Group Header Row -->
                        <div
                            class="px-3 py-2 bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 user-select-none"
                            style="cursor: pointer;"
                            @click="toggleGroup(section.key)"
                        >
                            <div class="d-flex align-items-center gap-2">
                                <i
                                    class="fa-solid fa-chevron-down text-muted small"
                                    :style="{
                                        transform: isGroupExpanded(section.key) ? 'rotate(0deg)' : 'rotate(-90deg)',
                                        transition: 'transform 0.15s ease',
                                    }"
                                ></i>
                                <span class="fw-bold text-dark">{{ section.title }}</span>
                                <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                                    {{ section.items.length }}
                                </span>
                            </div>

                            <div class="small font-monospace text-muted">
                                Saved: <strong class="text-dark">{{ formatPeso(section.savedTotal) }}</strong>
                                / Target: <strong class="text-dark">{{ formatPeso(section.targetTotal) }}</strong>
                                ({{ section.percent }}%)
                            </div>
                        </div>

                        <!-- Collapsible Table Body -->
                        <div v-show="isGroupExpanded(section.key)" class="table-responsive border-top border-secondary-subtle">
                            <table class="table table-hover table-sm align-middle mb-0">
                                <thead class="table-light text-uppercase small fw-bold">
                                    <tr>
                                        <th>Item / Destination</th>
                                        <th>Category</th>
                                        <th>Priority</th>
                                        <th class="text-end">Cost / Progress</th>
                                        <th>Target / Trip Date</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in section.items" :key="item.id" :class="getRowClass(item)">
                                        <!-- Item / Destination with Type Icon -->
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span
                                                    class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                                                    :class="Number(item.type) === 2 ? 'bg-info-subtle text-info-emphasis' : 'bg-primary-subtle text-primary'"
                                                    style="width: 28px; height: 28px;"
                                                    :title="Number(item.type) === 2 ? 'Travel' : 'Purchase'"
                                                >
                                                    <i :class="Number(item.type) === 2 ? 'fa-solid fa-plane fa-xs' : 'fa-solid fa-bag-shopping fa-xs'"></i>
                                                </span>
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ item.item }}</div>
                                                    <div
                                                        v-if="item.notes"
                                                        class="small text-muted text-truncate"
                                                        style="max-width: 240px;"
                                                        :title="item.notes"
                                                    >
                                                        {{ item.notes }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Category -->
                                        <td>
                                            <span class="text-dark">
                                                {{ item.category?.name || item.category_name || '—' }}
                                            </span>
                                        </td>

                                        <!-- Priority Badge -->
                                        <td>
                                            <span
                                                class="px-2 py-1 rounded-pill small fw-semibold border d-inline-flex align-items-center gap-1"
                                                :class="getPriorityBadge(item.priority).class"
                                            >
                                                <i
                                                    v-if="getPriorityBadge(item.priority).icon"
                                                    :class="getPriorityBadge(item.priority).icon"
                                                    style="font-size: 0.75rem;"
                                                ></i>
                                                {{ getPriorityBadge(item.priority).label }}
                                            </span>
                                        </td>

                                        <!-- Estimated Cost / Actual Cost & Saved Progress -->
                                        <td>
                                            <div class="text-end ms-auto" style="min-width: 140px;">
                                                <!-- Completed (Done = 3): Show Actual Cost & Estimated comparison -->
                                                <template v-if="Number(item.status) === 3">
                                                    <div class="fw-semibold font-monospace text-success">
                                                        {{ formatPeso(item.actual_cost ?? item.estimated_cost ?? 0) }}
                                                    </div>
                                                    <div class="small text-muted">
                                                        Est. {{ formatPeso(item.estimated_cost ?? 0) }}
                                                    </div>
                                                </template>

                                                <!-- Active (Saving / Ready): Show Estimated Cost & Saved Progress -->
                                                <template v-else>
                                                    <div class="fw-semibold font-monospace">
                                                        {{ formatPeso(item.estimated_cost ?? 0) }}
                                                    </div>
                                                    <div class="small text-muted mt-1">
                                                        Saved {{ formatPeso(item.saved_amount ?? 0) }} ({{ getProgressPercent(item) }}%)
                                                    </div>
                                                    <div class="progress mt-1" style="height: 4px;">
                                                        <div
                                                            class="progress-bar"
                                                            :class="getProgressPercent(item) >= 100 ? 'bg-success' : 'bg-primary'"
                                                            role="progressbar"
                                                            :style="{ width: getProgressPercent(item) + '%' }"
                                                        ></div>
                                                    </div>
                                                </template>
                                            </div>
                                        </td>

                                        <!-- Single Target Date or Travel Date Range -->
                                        <td>
                                            <span>{{ item.formatted_date_range || '—' }}</span>
                                        </td>

                                        <!-- Status Badge -->
                                        <td>
                                            <span
                                                class="px-2 py-1 rounded-pill small fw-semibold d-inline-flex align-items-center gap-1"
                                                :class="getStatusBadge(item.status).class"
                                            >
                                                <i
                                                    :class="getStatusBadge(item.status).icon"
                                                    style="font-size: 0.75rem;"
                                                ></i>
                                                {{ getStatusBadge(item.status).label }}
                                            </span>
                                        </td>

                                        <!-- Row Actions -->
                                        <td>
                                            <div class="d-flex align-items-center justify-content-end gap-1">
                                                <BButton
                                                    size="sm"
                                                    variant="light"
                                                    class="btn-icon rounded-circle bg-warning-subtle border-warning-subtle text-warning-emphasis shadow-sm px-2 py-1"
                                                    @click="edit(item.id)"
                                                    title="Edit Wishlist"
                                                >
                                                    <i class="fa-solid fa-pen-to-square small"></i>
                                                </BButton>
                                                <BButton
                                                    v-if="Number(item.status) !== 3"
                                                    size="sm"
                                                    variant="light"
                                                    class="btn-icon rounded-circle bg-success-subtle border-success-subtle text-success-emphasis shadow-sm px-2 py-1"
                                                    @click="markAsCompleted(item)"
                                                    :title="Number(item.type) === 2 ? 'Mark Trip Completed' : 'Mark as Purchased'"
                                                >
                                                    <i class="fa-solid fa-check small"></i>
                                                </BButton>
                                                <BButton
                                                    size="sm"
                                                    variant="danger"
                                                    class="btn-icon rounded-circle bg-danger-subtle border-danger-subtle text-danger-emphasis shadow-sm px-2 py-1"
                                                    @click="remove(item.id)"
                                                    title="Delete Wishlist"
                                                >
                                                    <i class="fa-solid fa-trash small"></i>
                                                </BButton>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Content End -->

    <!-- Wishlist Offcanvas -->
    <div class="offcanvas offcanvas-end border-0 shadow" tabindex="-1" id="wishlistCanvas" style="width: 550px;">
        
        <!-- Drawer Header -->
        <div class="offcanvas-header border-bottom py-3 px-4 flex-shrink-0">
            <h5 class="offcanvas-title fw-bolder d-flex align-items-center mb-0" id="wishlistCanvasLabel">
                <i class="fa-solid fa-heart text-primary me-3 fs-4"></i>
                <span class="text-dark">{{ form.id ? 'Edit' : 'Add New' }} Wishlist Item</span>
            </h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <!-- Form & Body Structure -->
        <form @submit.prevent="submitForm" novalidate class="d-flex flex-column h-100 mb-0 overflow-hidden">
            
            <!-- Scrollable Body Container -->
            <div class="offcanvas-body p-4 flex-grow-1" style="overflow-y: auto; min-height: 0;">
                
                <!-- Type Switcher (Purchase / Travel) -->
                <div class="mb-4">
                    <!-- <label class="form-label small text-uppercase fw-bold text-muted d-block mb-2">Type</label> -->
                    <div class="btn-group w-100 rounded-pill overflow-hidden border p-1 bg-light" role="group" aria-label="Wishlist Type">
                        <!-- Purchase Radio (1) -->
                        <input type="radio" class="btn-check" name="type" id="type_purchase" :value="1" v-model="form.type" @change="onTypeChange(1)" required>
                        <label 
                            class="btn border-0 py-2 small fw-semibold d-flex align-items-center justify-content-center rounded-pill" 
                            :class="Number(form.type) === 1 ? 'bg-primary-subtle text-primary border border-primary-subtle shadow-sm' : 'bg-transparent text-secondary opacity-50'" 
                            for="type_purchase">
                            <i class="fa-solid fa-bag-shopping me-2"></i> Purchase
                        </label>

                        <!-- Travel Radio (2) -->
                        <input type="radio" class="btn-check" name="type" id="type_travel" :value="2" v-model="form.type" @change="onTypeChange(2)">
                        <label 
                            class="btn border-0 py-2 small fw-semibold d-flex align-items-center justify-content-center rounded-pill" 
                            :class="Number(form.type) === 2 ? 'bg-info-subtle text-info-emphasis border border-info-subtle shadow-sm' : 'bg-transparent text-secondary opacity-50'" 
                            for="type_travel">
                            <i class="fa-solid fa-plane me-2"></i> Travel
                        </label>
                    </div>
                </div>

                <!-- Item / Destination Input -->
                <div class="mb-4">
                    <div class="form-floating custom-floating">
                        <input 
                            type="text" 
                            class="form-control border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                            id="item_name" 
                            :placeholder="Number(form.type) === 2 ? 'e.g. Iloilo & Kalibo Trip' : 'e.g. New Shoes'" 
                            v-model="form.item" 
                            required
                        >
                        <label for="item_name" class="text-muted ps-2 pt-2">
                            {{ Number(form.type) === 2 ? 'Destination / Trip Name' : 'Item Name' }}
                        </label>
                    </div>
                </div>

                <!-- Group Selector (Optional Bundling, e.g., Family Gifts) -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="wishlist_group_id" class="form-label small text-uppercase fw-bold text-muted mb-0">
                            Group (Optional)
                        </label>
                        <button 
                            type="button" 
                            class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold"
                            @click="showNewGroupInput = !showNewGroupInput"
                        >
                            <i class="fa-solid fa-plus me-1"></i>{{ showNewGroupInput ? 'Cancel New Group' : 'New Group' }}
                        </button>
                    </div>

                    <div class="form-floating custom-floating" v-if="!showNewGroupInput">
                        <select 
                            class="form-select border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                            id="wishlist_group_id" 
                            v-model="form.wishlist_group_id"
                        >
                            <option :value="null">No Group (Standalone Item)</option>
                            <option v-for="group in wishlistGroups" :key="group.id" :value="group.id">
                                {{ group.name }}
                            </option>
                        </select>
                        <label for="wishlist_group_id" class="text-muted ps-2 pt-2">Bundle Under Goal / Group</label>
                    </div>

                    <!-- Inline New Group Input -->
                    <div v-else class="d-flex gap-2 align-items-center pt-1">
                        <input 
                            type="text" 
                            class="form-control form-control-sm" 
                            placeholder="e.g. Family Gifts" 
                            v-model="newGroupName"
                            @keydown.enter.prevent="createQuickGroup"
                        >
                        <button 
                            type="button" 
                            class="btn btn-sm btn-primary rounded-pill px-3 flex-shrink-0"
                            @click="createQuickGroup"
                            :disabled="!newGroupName.trim()"
                        >
                            Save
                        </button>
                    </div>
                </div>

                <!-- Category & Priority Row -->
                <div class="row g-3 mb-4">
                    <div class="col-7">
                        <div class="form-floating custom-floating">
                            <select 
                                class="form-select border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                                id="category_id" 
                                v-model="form.category" 
                                required
                            >
                                <option value="" disabled>Select Category</option>
                                <option v-for="cat in wishlistCategories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                            <label for="category_id" class="text-muted ps-2 pt-2">Category</label>
                        </div>
                    </div>

                    <div class="col-5">
                        <div class="form-floating custom-floating">
                            <select 
                                class="form-select border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                                id="priority" 
                                v-model="form.priority" 
                                required
                            >
                                <option :value="1">High</option>
                                <option :value="2">Medium</option>
                                <option :value="3">Low</option>
                            </select>
                            <label for="priority" class="text-muted ps-2 pt-2">Priority</label>
                        </div>
                    </div>
                </div>

                <!-- Estimated Cost & Saved Amount Row -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="form-floating custom-floating">
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0"
                                class="form-control border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                                id="estimated_cost" 
                                placeholder="0.00" 
                                v-model="form.estimated_cost" 
                                required
                            >
                            <label for="estimated_cost" class="text-muted ps-2 pt-2">Estimated Cost (₱)</label>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-floating custom-floating">
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0"
                                class="form-control border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                                id="saved_amount" 
                                placeholder="0.00" 
                                v-model="form.saved_amount"
                            >
                            <label for="saved_amount" class="text-muted ps-2 pt-2">Saved So Far (₱)</label>
                        </div>
                    </div>
                </div>

                <!-- Target Date / Trip Date Range -->
                <div class="row g-3 mb-4">
                    <div :class="Number(form.type) === 2 ? 'col-6' : 'col-12'">
                        <div class="form-floating custom-floating">
                            <input 
                                type="date" 
                                class="form-control border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                                id="target_date" 
                                placeholder="YYYY-MM-DD" 
                                v-model="form.target_date"
                            >
                            <label for="target_date" class="text-muted ps-2 pt-2">
                                {{ Number(form.type) === 2 ? 'Trip Start Date' : 'Target Date (Optional)' }}
                            </label>
                        </div>
                    </div>

                    <!-- End Date (Shown only when Type = Travel) -->
                    <div class="col-6" v-if="Number(form.type) === 2">
                        <div class="form-floating custom-floating">
                            <input 
                                type="date" 
                                class="form-control border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                                id="end_date" 
                                placeholder="YYYY-MM-DD" 
                                v-model="form.end_date"
                            >
                            <label for="end_date" class="text-muted ps-2 pt-2">Trip End Date</label>
                        </div>
                    </div>
                </div>

                <!-- Status Switcher (Saving / Ready / Completed) -->
                <div class="mb-4">
                    <label class="form-label small text-uppercase fw-bold text-muted d-block mb-2">Status</label>
                    <div class="btn-group w-100 rounded-pill overflow-hidden border p-1 bg-light" role="group" aria-label="Wishlist Status">
                        <!-- Saving (1) -->
                        <input type="radio" class="btn-check" name="status" id="status_saving" :value="1" v-model="form.status" required>
                        <label 
                            class="btn border-0 py-2 small fw-semibold d-flex align-items-center justify-content-center rounded-pill" 
                            :class="Number(form.status) === 1 ? 'bg-primary-subtle text-primary border border-primary-subtle shadow-sm' : 'bg-transparent text-secondary opacity-50'" 
                            for="status_saving">
                            <i class="fa-solid fa-piggy-bank me-2"></i> Saving
                        </label>

                        <!-- Ready (2) -->
                        <input type="radio" class="btn-check" name="status" id="status_ready" :value="2" v-model="form.status">
                        <label 
                            class="btn border-0 py-2 small fw-semibold d-flex align-items-center justify-content-center rounded-pill" 
                            :class="Number(form.status) === 2 ? 'bg-success-subtle text-success border border-success-subtle shadow-sm' : 'bg-transparent text-secondary opacity-50'" 
                            for="status_ready">
                            <i class="fa-solid fa-circle-check me-2"></i> Ready
                        </label>

                        <!-- Completed (3) -->
                        <input type="radio" class="btn-check" name="status" id="status_completed" :value="3" v-model="form.status">
                        <label 
                            class="btn border-0 py-2 small fw-semibold d-flex align-items-center justify-content-center rounded-pill" 
                            :class="Number(form.status) === 3 ? 'bg-secondary-subtle text-dark border border-secondary-subtle shadow-sm' : 'bg-transparent text-secondary opacity-50'" 
                            for="status_completed">
                            <i class="fa-solid fa-check-double me-2"></i> Done
                        </label>
                    </div>
                </div>

                <!-- Notes Input -->
                <div class="mb-3">
                    <div class="form-floating custom-floating">
                        <textarea 
                            class="form-control border-0 border-bottom rounded-0 px-2 pt-4 pb-2 shadow-none focus-ring focus-ring-primary" 
                            id="notes" 
                            style="height: 90px;"
                            placeholder="Optional details, links, or budget breakdown..." 
                            v-model="form.notes"
                        ></textarea>
                        <label for="notes" class="text-muted ps-2 pt-2">Notes / Link / Breakdown (Optional)</label>
                    </div>
                </div>

            </div>

            <!-- Fixed Sticky Footer -->
            <div class="offcanvas-footer p-3 border-top bg-light d-flex align-items-center justify-content-end gap-2 flex-shrink-0 mt-auto">
                <button 
                    type="button" 
                    class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" 
                    data-bs-dismiss="offcanvas"
                >
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-check me-1"></i>
                    {{ form.id ? 'Update' : 'Add to' }} Wishlist
                </button>
            </div>
        </form>
    </div>
    <!-- Wishlist Offcanvas End -->
</template>

<script>
import DataTable from './Shared/DataTable.vue'
import { ref } from 'vue';

export default {
    components: { DataTable },
    data() {
        return {
            // loading vars
            isLoading:true,
            module: 'wishlist',
            utilityUrl: '/api/wishlists',
            selectedItem: null,
            isTableLoading: true,
            isEditing: false,
            wishlists: [],
            wishlistCategories: [],
            wishlistGroups: [], // e.g. [{ id: 1, name: 'Family Gifts' }]
            groupExpandedState: {},
            showNewGroupInput: false,
            newGroupName: '',
            tableTransition: {
                name: 'flip-list'
            },
            fields: [
                { key: 'name', label: 'Item / Destination', sortable: true },
                { key: 'category_label', label: 'Category', sortable: true },
                { key: 'priority', label: 'Priority', sortable: true },
                { key: 'estimated_cost', label: 'Cost / Progress', sortable: true, tdClass: 'text-end' },
                { key: 'formatted_date_range', label: 'Target / Trip Date', sortable: true },
                { key: 'status', label: 'Status', sortable: true },
                { key: 'actions', label: 'Actions' }
            ],
            form: {
                id: null,
                type: 1,            // 1 = Purchase, 2 = Travel
                item: '',
                category: '',
                priority: 1,        // 1 = High, 2 = Medium, 3 = Low
                estimated_cost: '',
                saved_amount: 0,
                actual_cost: null,
                target_date: '',
                end_date: '',
                status: 1,          // 1 = Saving, 2 = Ready, 3 = Done
                completed_at: '',
                notes: '',
                log_to_expense: true,
                wishlist_group_id: ''
            },
            perPage: ref(10),
            currentPage: ref(1),
            rows: ref(0),
            filter: ref(''),
        }
    },
    watch: {
        'form.status'(newStatus) {
            if (Number(newStatus) === 3) {
                if (this.form.actual_cost === null || this.form.actual_cost === '') {
                    this.form.actual_cost = this.form.estimated_cost;
                }
                if (!this.form.completed_at) {
                    this.form.completed_at = new Date().toISOString().slice(0, 10);
                }
            }
        }
    },
    computed: {
        groupedSections() {
            const items = Array.isArray(this.wishlists) ? this.wishlists : [];
            const query = (this.filter || '').trim().toLowerCase();

            const filtered = !query
                ? items
                : items.filter((item) => {
                    const name = String(item.item || '').toLowerCase();
                    const notes = String(item.notes || '').toLowerCase();
                    const groupName = String(item.group?.name || item.group_name || '').toLowerCase();
                    const catName = String(item.category?.name || item.category_name || '').toLowerCase();
                    return (
                        name.includes(query) ||
                        notes.includes(query) ||
                        groupName.includes(query) ||
                        catName.includes(query)
                    );
                });

            const groupBuckets = new Map();

            for (const group of this.wishlistGroups || []) {
                groupBuckets.set(String(group.id), {
                    key: String(group.id),
                    groupId: group.id,
                    title: group.name,
                    items: [],
                    savedTotal: 0,
                    targetTotal: 0,
                });
            }

            const ungrouped = {
                key: 'ungrouped',
                groupId: null,
                title: 'Ungrouped',
                items: [],
                savedTotal: 0,
                targetTotal: 0,
            };

            for (const item of filtered) {
                const isDone = Number(item.status) === 3;
                const est = Number(item.estimated_cost ?? 0);
                const actual = Number(item.actual_cost ?? est);
                const saved = Number(item.saved_amount ?? 0);
                const target = isDone ? actual : est;

                if (!item.wishlist_group_id) {
                    ungrouped.items.push(item);
                    ungrouped.savedTotal += saved;
                    ungrouped.targetTotal += target;
                    continue;
                }

                const key = String(item.wishlist_group_id);
                if (!groupBuckets.has(key)) {
                    groupBuckets.set(key, {
                        key,
                        groupId: item.wishlist_group_id,
                        title: item.group?.name || item.group_name || 'Group',
                        items: [],
                        savedTotal: 0,
                        targetTotal: 0,
                    });
                }

                const bucket = groupBuckets.get(key);
                bucket.items.push(item);
                bucket.savedTotal += saved;
                bucket.targetTotal += target;
            }

            const sections = Array.from(groupBuckets.values())
                .filter((sec) => sec.items.length > 0)
                .map((sec) => ({
                    ...sec,
                    percent: sec.targetTotal > 0
                        ? Math.min(100, Math.round((sec.savedTotal / sec.targetTotal) * 100))
                        : 0,
                }));

            if (ungrouped.items.length > 0) {
                const percent = ungrouped.targetTotal > 0
                    ? Math.min(100, Math.round((ungrouped.savedTotal / ungrouped.targetTotal) * 100))
                    : 0;
                sections.push({ ...ungrouped, percent });
            }

            return sections;
        },
    },
    methods: {
        async fetchWishlists() {
            this.wishlists = await this.fetchRecords({
                url: '/api/wishlists'
            });
            this.isTableLoading = false;
        },
        async fetchWishlistCategories(type = 1) {
            this.wishlistCategories = await this.fetchRecords({
                url: `wishlist-categories/${type}`
            });
        },
        async fetchWishlistGroups() {
            this.wishlistGroups = await this.fetchRecords({
                url: 'wishlist-groups'
            });
        },
        resetSelection() {
            this.selectedItem = {};
        },
        add(){
            this.form = {
                id: null,
                type: 1,
                category: null,
                priority: null,
                estimated_cost: null,
                saved_amount: null,
                target_date: null,
                end_date: null,
                status: 1,
                notes: null,
                wishlist_group_id: null

            }
            this.isEditing = false;
            this.openModal('wishlistCanvas');
        },
        edit(id){
            this.fetchItem({
                url: `/api/wishlists/${id}`,
                errorMessage: 'Failed to retrieve wishlist item details.',
                callback: (response) =>{
                    this.form = response;
                    this.isEditing = true;
                    this.openModal('wishlistCanvas');
                }
            });
        },
        submitForm(e){
            e.preventDefault();

            this.saveItem({
                url: this.isEditing ? `/api/wishlists/${this.form.id}` : '/api/wishlists',
                method: this.isEditing ? 'put' : 'post',
                data: this.form,
                successMessage: this.isEditing ? 'Wishlist updated successfully!' : 'Wishlist added successfully!',
                errorMessage: this.isEditing ? 'Failed to update wishlist.' : 'Failed to add wishlist.',
                callback: () => {
                    e.target.reset();
                    this.fetchWishlists();
                    this.closeModal('wishlistCanvas');
                }
            });
        },
        remove(id){
            this.deleteItem({
                url: `/api/wishlists/${id}`,
                successMessage: 'Wishlist deleted successfully',
                errorMessage: 'Failed to delete wishlist item',
                callback: () => {
                    this.fetchWishlists();
                } 
            });
        },
        async onTypeChange(type) {
            this.form.wishlist_category_id = null;
            this.form.category = null;

            if (Number(type) !== 2) {
                this.form.end_date = null;
            }

            await this.fetchWishlistCategories(Number(type));
        },
        getPriorityBadge(priority) {
            switch (Number(priority)) {
                case 1:
                    return { label: 'High', class: 'bg-danger-subtle text-danger-emphasis' };
                case 2:
                    return { label: 'Medium', class: 'bg-warning-subtle text-warning-emphasis' };
                default:
                    return { label: 'Low', class: 'bg-secondary-subtle text-secondary-emphasis' };
            }
        },
        getStatusBadge(status) {
            const map = {
                1: {
                    label: 'Saving',
                    class: 'bg-primary-subtle text-primary-emphasis border-primary-subtle',
                    icon: 'fa-solid fa-piggy-bank',
                },
                2: {
                    label: 'Ready',
                    class: 'bg-secondary-subtle text-secondary-emphasis border-secondary-subtle',
                    icon: 'fa-solid fa-circle-check',
                },
                3: {
                    label: 'Done',
                    class: 'bg-success-subtle text-success-emphasis border-success-subtle',
                    icon: 'fa-solid fa-check-double',
                },
            };

            return map[Number(status)] || {
                label: 'Saving',
                class: 'bg-primary-subtle text-primary-emphasis border-primary-subtle',
                icon: 'fa-solid fa-piggy-bank',
            };
        },
        getProgressPercent(item) {
            const est = Number(item.estimated_cost || 0);
            const saved = Number(item.saved_amount || 0);
            if (est <= 0) return saved > 0 ? 100 : 0;
            return Math.min(100, Math.round((saved / est) * 100));
        },
        formatGoalDate(item) {
            if (!item.target_date) return '—';
            if (Number(item.type) === 2 && item.end_date) {
                // return `${this.formatShortDate(item.target_date)} – ${this.formatShortDate(item.end_date)}`;
            }
            // return this.formatShortDate(item.target_date);
            return item.target_date;
        },
        getRowClass(item, type) { // format display of rows
            if (!item || type !== 'row') return;

            if (Number(item.status) !== 1) {
                return 'table-secondary opacity-75';
            }
        }, 
        markAsCompleted(item) {
            this.form = {
                ...item,
                status: 3,
                actual_cost: item.actual_cost ?? item.estimated_cost,
                completed_at: new Date().toISOString().slice(0, 10),
                log_to_expense: true
            };

            const canvas = bootstrap.Offcanvas.getOrCreateInstance(
                document.getElementById('wishlistCanvas')
            );
            canvas.show();

            this.$nextTick(() => {
                document.getElementById('actual_cost')?.focus();
            });
        },
        async createQuickGroup() {
            const name = this.newGroupName.trim();
            if (!name || this.isCreatingGroup) return;

            this.isCreatingGroup = true;
            try {
                const response = await axios.post('/wishlist-groups', {
                    name,
                    type: this.form.type || null,
                });

                const created = response.data.data || response.data;
                const exists = this.wishlistGroups.some((g) => Number(g.id) === Number(created.id));

                if (!exists) {
                    this.wishlistGroups.push(created);
                    this.wishlistGroups.sort((a, b) => a.name.localeCompare(b.name));
                }

                this.form.wishlist_group_id = created.id;
                this.newGroupName = '';
                this.showNewGroupInput = false;
            } catch (error) {
                console.error('Failed to create wishlist group:', error);
            } finally {
                this.isCreatingGroup = false;
            }
        },
        isGroupExpanded(key) {
            return this.groupExpandedState[key] !== false;
        },
        toggleGroup(key) {
            this.groupExpandedState = {
                ...this.groupExpandedState,
                [key]: !this.isGroupExpanded(key),
            };
        },
        setAllGroupsExpanded(expanded) {
            const next = {};
            for (const sec of this.groupedSections) {
                next[sec.key] = expanded;
            }
            this.groupExpandedState = next;
        },
    },
    mounted() {
        this.fetchWishlists();
        this.fetchWishlistCategories();
        this.fetchWishlistGroups();
    }
}
</script>