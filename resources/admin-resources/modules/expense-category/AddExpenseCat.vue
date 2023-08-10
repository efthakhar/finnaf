<script setup>
import { computed, onMounted } from "vue";
import CrossSvgIcon from "../../assets/icons/cross-svg-icon.vue";
import { useExpenseCategoryStore } from "./expenseCategoryStore";

const emit = defineEmits(["close", "refreshData"]);

const expenseCategoryStore = useExpenseCategoryStore();
const expense_category_data = computed(
    () => expenseCategoryStore.current_expense_category_item
);

async function submitData() {
    expenseCategoryStore
        .addExpenseCat(
            JSON.parse(
                JSON.stringify(
                    expenseCategoryStore.current_expense_category_item
                )
            )
        )
        .then(() => {
            emit("refreshData");
            emit("close");
        })
        .catch((error) => {
            console.log("error occurred");
        });
}

async function closeAddExpenseCatModal() {
    expenseCategoryStore.resetCurrentExpenseCatData();
    emit("close");
}

onMounted(() => {
    expenseCategoryStore.resetCurrentExpenseCatData();
});
</script>

<template>
    <div class="modal fade show d-block">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Expense Category</h5>
                    <button type="button" class="close">
                        <CrossSvgIcon @click="closeAddExpenseCatModal" />
                    </button>
                </div>

                <div class="modal-body">
                    <form action="">
                        <div class="form-item">
                            <label class="my-2">Expense Category Name</label>
                            <p
                                class="text-danger"
                                v-if="
                                    expenseCategoryStore
                                        .add_expense_category_errors.name
                                "
                            >
                                {{
                                    expenseCategoryStore
                                        .add_expense_category_errors.name
                                }}
                            </p>
                            <input
                                type="text"
                                class="form-control"
                                v-model="expense_category_data.name"
                            />
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button
                        class="btn btn-danger btn-sm"
                        @click="closeAddExpenseCatModal"
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
