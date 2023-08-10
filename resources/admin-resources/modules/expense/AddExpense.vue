<script setup>
import { computed, onMounted } from "vue";
import CrossSvgIcon from "../../assets/icons/cross-svg-icon.vue";
import { useExpenseStore } from "./expenseStore";
import Multiselect from "@vueform/multiselect";

const emit = defineEmits(["close", "refreshData"]);
const props = defineProps(["categories"]);

const expenseStore = useExpenseStore();
const expense_data = computed(() => expenseStore.current_expense_item);

async function submitData() {
    expenseStore
        .addExpense(
            JSON.parse(JSON.stringify(expenseStore.current_expense_item))
        )
        .then(() => {
            emit("refreshData");
            emit("close");
        })
        .catch((error) => {
            console.log("error occurred");
        });
}

async function closeAddExpenseModal() {
    expenseStore.resetCurrentExpenseData();
    emit("close");
}

onMounted(() => {
    expenseStore.resetCurrentExpenseData();
});
</script>

<template>
    <div class="modal fade show d-block">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Expense</h5>
                    <button type="button" class="close">
                        <CrossSvgIcon @click="closeAddExpenseModal" />
                    </button>
                </div>

                <div class="modal-body">
                    <form action="">
                        <div class="form-item">
                            <label class="my-2">Expense Short Title</label>
                            <p
                                class="text-danger"
                                v-if="expenseStore.add_expense_errors.title"
                            >
                                {{ expenseStore.add_expense_errors.title }}
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
                                    expenseStore.add_expense_errors.categories
                                "
                            >
                                {{ expenseStore.add_expense_errors.categories }}
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
                                v-if="expenseStore.add_expense_errors.date"
                            >
                                {{ expenseStore.add_expense_errors.date }}
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
                                v-if="expenseStore.add_expense_errors.amount"
                            >
                                {{ expenseStore.add_expense_errors.amount }}
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

                <div class="modal-footer">
                    <button
                        class="btn btn-danger btn-sm"
                        @click="closeAddExpenseModal"
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
