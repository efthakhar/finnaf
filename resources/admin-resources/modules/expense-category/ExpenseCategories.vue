<script setup>
import { computed, onMounted, ref } from "vue";
import Loader from "../../components/shared/loader/Loader.vue";
import Pagination from "../../components/shared/pagination/Pagination.vue";
import { useConfirmStore } from "../../components/shared/confirm-alert/confirmStore.js";
import { useExpenseCategoryStore } from "./expenseCategoryStore";
import BinSvgIcon from "../../assets/icons/bin-svg-icon.vue";
import EditSvgIcon from "../../assets/icons/edit-svg-icon.vue";
import ViewSvgIcon from "../../assets/icons/view-svg-icon.vue";
import AddNewButton from "../../components/buttons/AddNewButton.vue";
import FilterButton from "../../components/buttons/FilterButton.vue";
import BulkDeleteButton from "../../components/buttons/BulkDeleteButton.vue";
import AddExpenseCat from "./AddExpenseCat.vue";
import EditExpenseCat from "./EditExpenseCat.vue";
import ViewExpenseCat from "./ViewExpenseCat.vue";

const loading = ref(false);
const filterTab = ref(true);
const showAddExpenseCat = ref(false);
const showEditExpenseCat = ref(false);
const showViewExpenseCat = ref(false);

const confirmStore = useConfirmStore();
const expenseCategoryStore = useExpenseCategoryStore();
const expense_categories = computed(
    () => expenseCategoryStore.expense_categories
);
const q_name = ref("");
const selected_expense_cats = ref([]);
const all_selectd = ref(false);

function select_all() {
    if (all_selectd.value == false) {
        selected_expense_cats.value = [];
        expenseCategoryStore.expense_categories.forEach((element) => {
            selected_expense_cats.value.push(element.id);
        });
        all_selectd.value = true;
    } else {
        all_selectd.value = false;
        selected_expense_cats.value = [];
    }
}

async function deleteData(id) {
    confirmStore
        .show_box({
            message: "Do you want to delete selected expense category?",
        })
        .then(async () => {
            if (confirmStore.do_action == true) {
                expenseCategoryStore.deleteExpenseCat(id).then(() => {
                    expenseCategoryStore.fetchExpenseCats(
                        expenseCategoryStore.current_page,
                        expenseCategoryStore.limit,
                        expenseCategoryStore.q_name
                    );

                    if (Array.isArray(id)) {
                        all_selectd.value = false;
                        selected_expense_cats.value = [];
                    }
                });
            }
        });
}

function openEditExpenseCatModal(id) {
    expenseCategoryStore.edit_expense_category_id = id;
    showEditExpenseCat.value = true;
}

function openViewExpenseCatModal(id) {
    expenseCategoryStore.view_expense_category_id = id;
    showViewExpenseCat.value = true;
}

async function fetchData(
    page = expenseCategoryStore.current_page,
    limit = expenseCategoryStore.limit,
    q_name = expenseCategoryStore.q_name
) {
    loading.value = true;

    all_selectd.value = false;
    selected_expense_cats.value = [];

    try {
        expenseCategoryStore
            .fetchExpenseCats(page, limit, q_name)
            .then((response) => {
                loading.value = false;
            });
    } catch (error) {
        // console.log(error);
        loading.value = false;
    }
}

onMounted(async () => {
    fetchData(1);
});
</script>

<template>
    <div>
        <div class="page-top-box mb-2 d-flex flex-wrap">
            <h3 class="h3">Expense Category List</h3>
            <div class="page-heading-actions ms-auto">
                <BulkDeleteButton
                    v-if="selected_expense_cats.length > 0"
                    @click="deleteData(selected_expense_cats)"
                />
                <AddNewButton @click="showAddExpenseCat = true" />
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
                        v-model="q_name"
                        @keyup="
                            fetchData(1, expenseCategoryStore.limit, q_name)
                        "
                    />
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
                        <th>Name</th>
                        <th class="table-action-col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="expense_cat in expense_categories"
                        :key="expense_cat.id"
                    >
                        <td>
                            <input
                                type="checkbox"
                                class="form-check-input"
                                v-model="selected_expense_cats"
                                :value="expense_cat.id"
                            />
                        </td>
                        <td class="min150 max150">{{ expense_cat.name }}</td>
                        <td class="table-action-btns">
                            <ViewSvgIcon
                                color="#00CFDD"
                                @click="openViewExpenseCatModal(expense_cat.id)"
                            />
                            <EditSvgIcon
                                color="#739EF1"
                                @click="openEditExpenseCatModal(expense_cat.id)"
                            />
                            <BinSvgIcon
                                color="#FF7474"
                                @click="deleteData(expense_cat.id)"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination
            v-if="loading == false && expense_categories.length > 0"
            :total_pages="expenseCategoryStore.total_pages"
            :current_page="expenseCategoryStore.current_page"
            :per_page="expenseCategoryStore.limit"
            @pageChange="
                (currentPage) =>
                    fetchData(currentPage, expenseCategoryStore.limit)
            "
            @perPageChange="(perpage) => fetchData(1, perpage)"
        />
        <div class="modals-container">
            <AddExpenseCat
                v-if="showAddExpenseCat"
                @close="showAddExpenseCat = false"
                @refreshData="fetchData(1)"
            />
            <EditExpenseCat
                v-if="showEditExpenseCat"
                :expense_category_id="
                    expenseCategoryStore.edit_expense_category_id
                "
                @close="showEditExpenseCat = false"
                @refreshData="fetchData(expenseCategoryStore.current_page)"
            />
            <ViewExpenseCat
                v-if="showViewExpenseCat"
                :expense_category_id="
                    expenseCategoryStore.view_expense_category_id
                "
                @close="showViewExpenseCat = false"
            />
        </div>
    </div>
</template>
