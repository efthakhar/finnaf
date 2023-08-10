<script setup>
import { computed, onMounted, ref } from "vue";
import Loader from "../../components/shared/loader/Loader.vue";
import Pagination from "../../components/shared/pagination/Pagination.vue";
import { useConfirmStore } from "../../components/shared/confirm-alert/confirmStore.js";
import { useExpenseStore } from "./expenseStore";
import { useExpenseCategoryStore } from "../expense-category/expenseCategoryStore";
import BinSvgIcon from "../../assets/icons/bin-svg-icon.vue";
import EditSvgIcon from "../../assets/icons/edit-svg-icon.vue";
import ViewSvgIcon from "../../assets/icons/view-svg-icon.vue";
import AddNewButton from "../../components/buttons/AddNewButton.vue";
import FilterButton from "../../components/buttons/FilterButton.vue";
import BulkDeleteButton from "../../components/buttons/BulkDeleteButton.vue";
import AddExpense from "./AddExpense.vue";
import EditExpense from "./EditExpense.vue";
import ViewExpense from "./ViewExpense.vue";

const loading = ref(false);
const filterTab = ref(true);
const showAddExpense = ref(false);
const showEditExpense = ref(false);
const showViewExpense = ref(false);

const expenseStore = useExpenseStore();
const confirmStore = useConfirmStore();
const expenses = computed(() => expenseStore.expenses);
const expenseCategoryStore = useExpenseCategoryStore();
const expenseCategories = ref([]);
const q_title = ref("");
const selected_expenses = ref([]);
const all_selectd = ref(false);

function select_all() {
    if (all_selectd.value == false) {
        selected_expenses.value = [];
        expenseStore.expenses.forEach((element) => {
            selected_expenses.value.push(element.id);
        });
        all_selectd.value = true;
    } else {
        all_selectd.value = false;
        selected_expenses.value = [];
    }
}

async function deleteData(id) {
    confirmStore
        .show_box({ message: "Do you want to delete selected expense?" })
        .then(async () => {
            if (confirmStore.do_action == true) {
                expenseStore.deleteExpense(id).then(() => {
                    expenseStore.fetchExpenses(
                        expenseStore.current_page,
                        expenseStore.limit,
                        expenseStore.q_title
                    );

                    if (Array.isArray(id)) {
                        all_selectd.value = false;
                        selected_expenses.value = [];
                    }
                });
            }
        });
}

function openEditExpenseModal(id) {
    expenseStore.edit_expense_id = id;
    showEditExpense.value = true;
}

function openViewExpenseModal(id) {
    expenseStore.view_expense_id = id;
    showViewExpense.value = true;
}

async function fetchData(
    page = expenseStore.current_page,
    limit = expenseStore.limit,
    q_title = expenseStore.q_title
) {
    loading.value = true;

    all_selectd.value = false;
    selected_expenses.value = [];

    try {
        expenseStore.fetchExpenses(page, limit, q_title).then((response) => {
            loading.value = false;
        });
    } catch (error) {
        // console.log(error);
        loading.value = false;
    }
}

onMounted(async () => {
    fetchData(1);
    expenseCategoryStore.fetchCatList().then((response) => {
        expenseCategories.value = response;
    });
});
</script>

<template>
    <div>
        <div class="page-top-box mb-2 d-flex flex-wrap">
            <h3 class="h3">Expense List</h3>
            <div class="page-heading-actions ms-auto">
                <BulkDeleteButton
                    v-if="selected_expenses.length > 0"
                    @click="deleteData(selected_expenses)"
                />
                <AddNewButton @click="showAddExpense = true" />
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
                        @keyup="fetchData(1, expenseStore.limit, q_title)"
                    />
                </div>
                <div class="col-md-3 col-sm-6 my-1">
                    <select
                        class="form-select"
                        v-model="expenseStore.q_category"
                        @change="fetchData(1)"
                    >
                        <option value="">select category</option>
                        <option
                            :key="expenseCategory.value"
                            :value="expenseCategory.value"
                            v-for="expenseCategory in expenseCategories"
                        >
                            {{ expenseCategory.label }}
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
                            v-model="expenseStore.q_start_date"
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
                            v-model="expenseStore.q_end_date"
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
                            v-model="expenseStore.q_start_amount"
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
                            v-model="expenseStore.q_end_amount"
                        />
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 my-1">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Sort By</span>
                        <select
                            class="form-select"
                            v-model="expenseStore.q_sort_column"
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
                            v-model="expenseStore.q_sort_order"
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
                    <tr v-for="expense in expenses" :key="expense.id">
                        <td>
                            <input
                                type="checkbox"
                                class="form-check-input"
                                v-model="selected_expenses"
                                :value="expense.id"
                            />
                        </td>
                        <td class="min150 max150">{{ expense.title }}</td>
                        <td class="min100 max100">{{ expense.amount }}</td>
                        <td class="min200 max200">
                            <span
                                :key="expense_cat.value"
                                v-for="expense_cat in expense.categories"
                                class="badge bg-primary m-1 px-2 shadow-sm py-1"
                            >
                                {{ expense_cat.label }}
                            </span>
                        </td>
                        <td class="min100 max100">{{ expense.date }}</td>
                        <td class="table-action-btns">
                            <ViewSvgIcon
                                color="#00CFDD"
                                @click="openViewExpenseModal(expense.id)"
                            />
                            <EditSvgIcon
                                color="#739EF1"
                                @click="openEditExpenseModal(expense.id)"
                            />
                            <BinSvgIcon
                                color="#FF7474"
                                @click="deleteData(expense.id)"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination
            v-if="loading == false && expenses.length > 0"
            :total_pages="expenseStore.total_pages"
            :current_page="expenseStore.current_page"
            :per_page="expenseStore.limit"
            @pageChange="
                (currentPage) => fetchData(currentPage, expenseStore.limit)
            "
            @perPageChange="(perpage) => fetchData(1, perpage)"
        />
        <div class="modals-container">
            <AddExpense
                v-if="showAddExpense"
                :categories="expenseCategories"
                @close="showAddExpense = false"
                @refreshData="fetchData(1)"
            />
            <EditExpense
                v-if="showEditExpense"
                :expense_id="expenseStore.edit_expense_id"
                :categories="expenseCategories"
                @close="showEditExpense = false"
                @refreshData="fetchData(expenseStore.current_page)"
            />
            <ViewExpense
                v-if="showViewExpense"
                :expense_id="expenseStore.view_expense_id"
                @close="showViewExpense = false"
            />
        </div>
    </div>
</template>
