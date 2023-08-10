<script setup>
import { ref, computed, onMounted } from "vue";
import CrossSvgIcon from "../../assets/icons/cross-svg-icon.vue";
import Loader from "../../components/shared/loader/Loader.vue";
import { useExpenseStore } from "./expenseStore";
import Multiselect from "@vueform/multiselect";

const props = defineProps(["expense_id", "categories"]);
const emit = defineEmits(["close", "refreshData"]);

const loading = ref(false);
const expenseStore = useExpenseStore();
const expense_data = computed(() => expenseStore.current_expense_item);

async function submitData() {
    expenseStore
        .editExpense(
            JSON.parse(JSON.stringify(expenseStore.current_expense_item))
        )
        .then(() => {
            emit("refreshData");
            emit("close");
        })
        .catch((error) => {
            console.log("error occurred" + error);
        });
}

async function fetchData(id) {
    loading.value = true;
    await expenseStore.fetchExpense(id);
    loading.value = false;
}

async function closeEditExpenseModal() {
    expenseStore.resetCurrentExpenseData();
    emit("close");
}

onMounted(async () => {
    fetchData(props.expense_id);
});
</script>

<template>
    <div class="modal fade show d-block">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Expense</h5>
                    <button type="button" class="close">
                        <CrossSvgIcon @click="closeEditExpenseModal" />
                    </button>
                </div>

                <div class="modal-body">
                    <Loader v-if="loading" />
                    <div class="form-items" v-if="loading == false">
                        <form action="">
                            <div class="form-item">
                                <label class="my-2">Expense Short Title</label>
                                <p
                                    class="text-danger"
                                    v-if="
                                        expenseStore.edit_expense_errors.title
                                    "
                                >
                                    {{ expenseStore.edit_expense_errors.title }}
                                </p>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="expense_data.title"
                                />
                            </div>
                            <div class="form-item">
                                <label class="my-2">Expense Category</label>
                                <p
                                    class="text-danger"
                                    v-if="
                                        expenseStore.edit_expense_errors
                                            .categories
                                    "
                                >
                                    {{
                                        expenseStore.edit_expense_errors
                                            .categories
                                    }}
                                </p>
                                <Multiselect
                                    :searchable="true"
                                    mode="tags"
                                    :hide-selected="false"
                                    v-model="expense_data.categories"
                                    :options="categories"
                                ></Multiselect>
                            </div>
                            <div class="form-item">
                                <label class="my-2">Expense Date</label>
                                <p
                                    class="text-danger"
                                    v-if="expenseStore.edit_expense_errors.date"
                                >
                                    {{ expenseStore.edit_expense_errors.date }}
                                </p>
                                <input
                                    type="date"
                                    class="form-control"
                                    v-model="expense_data.date"
                                />
                            </div>
                            <div class="form-item">
                                <label class="my-2">Expense Amount</label>
                                <p
                                    class="text-danger"
                                    v-if="
                                        expenseStore.edit_expense_errors.amount
                                    "
                                >
                                    {{
                                        expenseStore.edit_expense_errors.amount
                                    }}
                                </p>
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model="expense_data.amount"
                                />
                            </div>
                            <div class="form-item">
                                <label class="my-2">Description</label>
                                <textarea
                                    v-model="expense_data.description"
                                    class="form-control"
                                    rows="5"
                                ></textarea>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="modal-footer">
                    <button
                        class="btn btn-danger btn-sm"
                        @click="closeEditExpenseModal"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="btn btn-primary ml-1 btn-sm"
                        @click="submitData"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
