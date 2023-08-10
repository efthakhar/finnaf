<script setup>
import { computed, onMounted, ref } from "vue";
import Loader from "../../components/shared/loader/Loader.vue";
import Pagination from "../../components/shared/pagination/Pagination.vue";
import { useConfirmStore } from "../../components/shared/confirm-alert/confirmStore.js";
import { useIncomeCategoryStore } from "./incomeCategoryStore";
import BinSvgIcon from "../../assets/icons/bin-svg-icon.vue";
import EditSvgIcon from "../../assets/icons/edit-svg-icon.vue";
import ViewSvgIcon from "../../assets/icons/view-svg-icon.vue";
import AddNewButton from "../../components/buttons/AddNewButton.vue";
import FilterButton from "../../components/buttons/FilterButton.vue";
import BulkDeleteButton from "../../components/buttons/BulkDeleteButton.vue";
import AddIncomeCat from "./AddIncomeCat.vue";
import EditIncomeCat from "./EditIncomeCat.vue";
import ViewIncomeCat from "./ViewIncomeCat.vue";

const loading = ref(false);
const filterTab = ref(true);
const showAddIncomeCat = ref(false);
const showEditIncomeCat = ref(false);
const showViewIncomeCat = ref(false);

const confirmStore = useConfirmStore();
const incomeCategoryStore = useIncomeCategoryStore();
const income_categories = computed(() => incomeCategoryStore.income_categories);
const q_name = ref("");
const selected_income_cats = ref([]);
const all_selectd = ref(false);

function select_all() {
    if (all_selectd.value == false) {
        selected_income_cats.value = [];
        incomeCategoryStore.income_categories.forEach((element) => {
            selected_income_cats.value.push(element.id);
        });
        all_selectd.value = true;
    } else {
        all_selectd.value = false;
        selected_income_cats.value = [];
    }
}

async function deleteData(id) {
    confirmStore
        .show_box({
            message: "Do you want to delete selected income category?",
        })
        .then(async () => {
            if (confirmStore.do_action == true) {
                incomeCategoryStore.deleteIncomeCat(id).then(() => {
                    incomeCategoryStore.fetchIncomeCats(
                        incomeCategoryStore.current_page,
                        incomeCategoryStore.limit,
                        incomeCategoryStore.q_name
                    );

                    if (Array.isArray(id)) {
                        all_selectd.value = false;
                        selected_income_cats.value = [];
                    }
                });
            }
        });
}

function openEditIncomeCatModal(id) {
    incomeCategoryStore.edit_income_category_id = id;
    showEditIncomeCat.value = true;
}

function openViewIncomeCatModal(id) {
    incomeCategoryStore.view_income_category_id = id;
    showViewIncomeCat.value = true;
}

async function fetchData(
    page = incomeCategoryStore.current_page,
    limit = incomeCategoryStore.limit,
    q_name = incomeCategoryStore.q_name
) {
    loading.value = true;

    all_selectd.value = false;
    selected_income_cats.value = [];

    try {
        incomeCategoryStore
            .fetchIncomeCats(page, limit, q_name)
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
            <h3 class="h3">Income Category List</h3>
            <div class="page-heading-actions ms-auto">
                <BulkDeleteButton
                    v-if="selected_income_cats.length > 0"
                    @click="deleteData(selected_income_cats)"
                />
                <AddNewButton @click="showAddIncomeCat = true" />
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
                        @keyup="fetchData(1, incomeCategoryStore.limit, q_name)"
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
                        v-for="income_cat in income_categories"
                        :key="income_cat.id"
                    >
                        <td>
                            <input
                                type="checkbox"
                                class="form-check-input"
                                v-model="selected_income_cats"
                                :value="income_cat.id"
                            />
                        </td>
                        <td class="min150 max150">{{ income_cat.name }}</td>
                        <td class="table-action-btns">
                            <ViewSvgIcon
                                color="#00CFDD"
                                @click="openViewIncomeCatModal(income_cat.id)"
                            />
                            <EditSvgIcon
                                color="#739EF1"
                                @click="openEditIncomeCatModal(income_cat.id)"
                            />
                            <BinSvgIcon
                                color="#FF7474"
                                @click="deleteData(income_cat.id)"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination
            v-if="loading == false && income_categories.length > 0"
            :total_pages="incomeCategoryStore.total_pages"
            :current_page="incomeCategoryStore.current_page"
            :per_page="incomeCategoryStore.limit"
            @pageChange="
                (currentPage) =>
                    fetchData(currentPage, incomeCategoryStore.limit)
            "
            @perPageChange="(perpage) => fetchData(1, perpage)"
        />
        <div class="modals-container">
            <AddIncomeCat
                v-if="showAddIncomeCat"
                @close="showAddIncomeCat = false"
                @refreshData="fetchData(1)"
            />
            <EditIncomeCat
                v-if="showEditIncomeCat"
                :income_category_id="
                    incomeCategoryStore.edit_income_category_id
                "
                @close="showEditIncomeCat = false"
                @refreshData="fetchData(incomeCategoryStore.current_page)"
            />
            <ViewIncomeCat
                v-if="showViewIncomeCat"
                :income_category_id="
                    incomeCategoryStore.view_income_category_id
                "
                @close="showViewIncomeCat = false"
            />
        </div>
    </div>
</template>
