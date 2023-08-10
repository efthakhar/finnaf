<script setup>
import { computed, onMounted, ref } from "vue";
import Loader from "../../components/shared/loader/Loader.vue";
import Pagination from "../../components/shared/pagination/Pagination.vue";
import { useConfirmStore } from "../../components/shared/confirm-alert/confirmStore.js";
import { useIncomeStore } from "./incomeStore";
import { useIncomeCategoryStore } from "../income-category/incomeCategoryStore";
import BinSvgIcon from "../../assets/icons/bin-svg-icon.vue";
import EditSvgIcon from "../../assets/icons/edit-svg-icon.vue";
import ViewSvgIcon from "../../assets/icons/view-svg-icon.vue";
import AddNewButton from "../../components/buttons/AddNewButton.vue";
import FilterButton from "../../components/buttons/FilterButton.vue";
import BulkDeleteButton from "../../components/buttons/BulkDeleteButton.vue";
import AddIncome from "./AddIncome.vue";
import EditIncome from "./EditIncome.vue";
import ViewIncome from "./ViewIncome.vue";

const loading = ref(false);
const filterTab = ref(true);
const showAddIncome = ref(false);
const showEditIncome = ref(false);
const showViewIncome = ref(false);

const incomeStore = useIncomeStore();
const confirmStore = useConfirmStore();
const incomes = computed(() => incomeStore.incomes);
const incomeCategoryStore = useIncomeCategoryStore();
const incomeCategories = ref([]);
const q_title = ref("");
const selected_incomes = ref([]);
const all_selectd = ref(false);

function select_all() {
    if (all_selectd.value == false) {
        selected_incomes.value = [];
        incomeStore.incomes.forEach((element) => {
            selected_incomes.value.push(element.id);
        });
        all_selectd.value = true;
    } else {
        all_selectd.value = false;
        selected_incomes.value = [];
    }
}

async function deleteData(id) {
    confirmStore
        .show_box({ message: "Do you want to delete selected income?" })
        .then(async () => {
            if (confirmStore.do_action == true) {
                incomeStore.deleteIncome(id).then(() => {
                    incomeStore.fetchIncomes(
                        incomeStore.current_page,
                        incomeStore.limit,
                        incomeStore.q_title
                    );

                    if (Array.isArray(id)) {
                        all_selectd.value = false;
                        selected_incomes.value = [];
                    }
                });
            }
        });
}

function openEditIncomeModal(id) {
    incomeStore.edit_income_id = id;
    showEditIncome.value = true;
}

function openViewIncomeModal(id) {
    incomeStore.view_income_id = id;
    showViewIncome.value = true;
}

async function fetchData(
    page = incomeStore.current_page,
    limit = incomeStore.limit,
    q_title = incomeStore.q_title
) {
    loading.value = true;

    all_selectd.value = false;
    selected_incomes.value = [];

    try {
        incomeStore.fetchIncomes(page, limit, q_title).then((response) => {
            loading.value = false;
        });
    } catch (error) {
        // console.log(error);
        loading.value = false;
    }
}

onMounted(async () => {
    fetchData(1);
    incomeCategoryStore.fetchCatList().then((response) => {
        incomeCategories.value = response;
    });
});
</script>

<template>
    <div>
        <div class="page-top-box mb-2 d-flex flex-wrap">
            <h3 class="h3">Income List</h3>
            <div class="page-heading-actions ms-auto">
                <BulkDeleteButton
                    v-if="selected_incomes.length > 0"
                    @click="deleteData(selected_incomes)"
                />
                <AddNewButton @click="showAddIncome = true" />
                <FilterButton @click="filterTab = !filterTab" />
            </div>
        </div>
        <div class="p-1 my-2" v-if="filterTab">
            <div class="row">
                <div class="col-md-3 col-sm-6 my-1">
                    <input
                        type="text"
                        class="form-control"
                        placeholder="type name.."
                        v-model="q_title"
                        @keyup="fetchData(1, incomeStore.limit, q_title)"
                    />
                </div>
                <div class="col-md-3 col-sm-6 my-1">
                    <select
                        class="form-select"
                        v-model="incomeStore.q_category"
                        @change="fetchData(1)"
                    >
                        <option value="">select category</option>
                        <option
                            :key="incomeCategory.value"
                            :value="incomeCategory.value"
                            v-for="incomeCategory in incomeCategories"
                        >
                            {{ incomeCategory.label }}
                        </option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 my-1">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">From</span>
                        <input
                            type="date"
                            class="form-control"
                            @change="fetchData(1)"
                            v-model="incomeStore.q_start_date"
                        />
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 my-1">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">To</span>
                        <input
                            type="date"
                            class="form-control"
                            @change="fetchData(1)"
                            v-model="incomeStore.q_end_date"
                        />
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 my-1">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Min Amount</span>
                        <input
                            type="number"
                            class="form-control"
                            @input="fetchData(1)"
                            v-model="incomeStore.q_start_amount"
                        />
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 my-1">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Max Amount</span>
                        <input
                            type="number"
                            class="form-control"
                            @input="fetchData(1)"
                            v-model="incomeStore.q_end_amount"
                        />
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 my-1">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Sort By</span>
                        <select
                            class="form-select"
                            v-model="incomeStore.q_sort_column"
                            @change="fetchData(1)"
                        >
                            <option value="id">Default</option>
                            <option value="date">Date</option>
                            <option value="amount">Amount</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 my-1">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">order</span>
                        <select
                            class="form-select"
                            v-model="incomeStore.q_sort_order"
                            @change="fetchData(1)"
                        >
                            <option value="desc">desc</option>
                            <option value="asc">asc</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <Loader v-if="loading" />
        <div
            class="table-responsive bg-white shadow-sm"
            v-if="loading == false"
        >
            <table class="table mb-0 table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            <input
                                type="checkbox"
                                class="form-check-input"
                                @click="select_all"
                                v-model="all_selectd"
                            />
                        </th>
                        <th>Title</th>
                        <th>Amount</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th class="table-action-col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="income in incomes" :key="income.id">
                        <td>
                            <input
                                type="checkbox"
                                class="form-check-input"
                                v-model="selected_incomes"
                                :value="income.id"
                            />
                        </td>
                        <td class="min150 max150">{{ income.title }}</td>
                        <td class="min100 max100">{{ income.amount }}</td>
                        <td class="min200 max200">
                            <span
                                :key="income_cat.value"
                                v-for="income_cat in income.categories"
                                class="badge bg-primary m-1 px-2 shadow-sm py-1"
                            >
                                {{ income_cat.label }}
                            </span>
                        </td>
                        <td class="min100 max100">{{ income.date }}</td>
                        <td class="table-action-btns">
                            <ViewSvgIcon
                                color="#00CFDD"
                                @click="openViewIncomeModal(income.id)"
                            />
                            <EditSvgIcon
                                color="#739EF1"
                                @click="openEditIncomeModal(income.id)"
                            />
                            <BinSvgIcon
                                color="#FF7474"
                                @click="deleteData(income.id)"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination
            v-if="loading == false && incomes.length > 0"
            :total_pages="incomeStore.total_pages"
            :current_page="incomeStore.current_page"
            :per_page="incomeStore.limit"
            @pageChange="
                (currentPage) => fetchData(currentPage, incomeStore.limit)
            "
            @perPageChange="(perpage) => fetchData(1, perpage)"
        />
        <div class="modals-container">
            <AddIncome
                v-if="showAddIncome"
                :categories="incomeCategories"
                @close="showAddIncome = false"
                @refreshData="fetchData(1)"
            />
            <EditIncome
                v-if="showEditIncome"
                :income_id="incomeStore.edit_income_id"
                :categories="incomeCategories"
                @close="showEditIncome = false"
                @refreshData="fetchData(incomeStore.current_page)"
            />
            <ViewIncome
                v-if="showViewIncome"
                :income_id="incomeStore.view_income_id"
                @close="showViewIncome = false"
            />
        </div>
    </div>
</template>
