<script setup>
import { ref, computed, onMounted } from "vue";
import CrossSvgIcon from "../../assets/icons/cross-svg-icon.vue";
import Loader from "../../components/shared/loader/Loader.vue";
import { useExpenseStore } from "./expenseStore";

const props = defineProps(["expense_id"]);
const emit = defineEmits(["close", "refreshData"]);

const loading = ref(false);
const expenseStore = useExpenseStore();
const expense_data = computed(() => expenseStore.current_expense_item);

async function fetchData(id) {
    loading.value = true;
    await expenseStore.fetchExpense(id);
    loading.value = false;
}

async function closeViewExpenseModal() {
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
                    <h5 class="modal-title">Expense Record Details</h5>
                    <button type="button" class="close">
                        <CrossSvgIcon @click="closeViewExpenseModal" />
                    </button>
                </div>

                <div class="modal-body">
                    <Loader v-if="loading" />
                    <div class="form-items" v-if="loading == false">
                        <form action="">
                            <div class="form-item">
                                <label class="my-2">Expense Short Title: </label>

                                <input
                                    disabled
                                    type="text"
                                    class="form-control"
                                    v-model="expense_data.title"
                                />
                            </div>
                            <div class="form-item">
                                <label class="my-2">Expense Category: </label>
                                <div>
                                    <span
                                        :key="expense_cat.value"
                                        v-for="expense_cat in expense_data.categories_details"
                                        class="badge bg-primary m-1 px-2 shadow-sm py-2 rounded-2"
                                    >
                                        {{ expense_cat.label }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-item">
                                <label class="my-2">Expense Date: </label>

                                <input
                                    disabled
                                    type="date"
                                    class="form-control"
                                    v-model="expense_data.date"
                                />
                            </div>
                            <div class="form-item">
                                <label class="my-2">Expense Amount: </label>

                                <input
                                    disabled
                                    type="number"
                                    class="form-control"
                                    v-model="expense_data.amount"
                                />
                            </div>
                            <div class="form-item">
                                <label class="my-2">Description: </label>
                                <textarea
                                    disabled
                                    v-model="expense_data.description"
                                    class="form-control"
                                    rows="5"
                                ></textarea>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
