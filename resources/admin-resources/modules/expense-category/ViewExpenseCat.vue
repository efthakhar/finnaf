<script setup>
import { ref, computed, onMounted } from "vue";
import CrossSvgIcon from "../../assets/icons/cross-svg-icon.vue";
import Loader from "../../components/shared/loader/Loader.vue";
import { useExpenseCategoryStore } from "./expenseCategoryStore";

const props = defineProps(["expense_category_id"]);
const emit = defineEmits(["close", "refreshData"]);

const loading = ref(false);
const expenseCategoryStore = useExpenseCategoryStore();
const expense_category_data = computed(
    () => expenseCategoryStore.current_expense_category_item
);

async function fetchData(id) {
    loading.value = true;
    await expenseCategoryStore.fetchExpenseCat(id);
    loading.value = false;
}

async function closeEditExpenseModal() {
    expenseCategoryStore.resetCurrentExpenseCatData();
    emit("close");
}

onMounted(async () => {
    fetchData(props.expense_category_id);
});
</script>

<template>
    <div class="modal fade show d-block">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Expense Category Details</h5>
                    <button type="button" class="close">
                        <CrossSvgIcon @click="closeEditExpenseModal" />
                    </button>
                </div>

                <div class="modal-body">
                    <Loader v-if="loading" />
                    <div class="form-items" v-if="loading == false">
                        <form action="">
                            <div class="form-item">
                                <label class="my-2">Category Name</label>
                                <input
                                    disabled
                                    type="text"
                                    class="form-control"
                                    v-model="expense_category_data.name"
                                />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
