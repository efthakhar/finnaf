<script setup>
import { computed, onMounted } from "vue";
import CrossSvgIcon from "../../assets/icons/cross-svg-icon.vue";
import { useIncomeCategoryStore } from "./incomeCategoryStore";

const emit = defineEmits(["close", "refreshData"]);

const incomeCategoryStore = useIncomeCategoryStore();
const income_category_data = computed(
    () => incomeCategoryStore.current_income_category_item
);

async function submitData() {
    incomeCategoryStore
        .addIncomeCat(
            JSON.parse(
                JSON.stringify(incomeCategoryStore.current_income_category_item)
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

async function closeAddIncomeCatModal() {
    incomeCategoryStore.resetCurrentIncomeCatData();
    emit("close");
}

onMounted(() => {
    incomeCategoryStore.resetCurrentIncomeCatData();
});
</script>

<template>
    <div class="modal fade show d-block">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Income Category</h5>
                    <button type="button" class="close">
                        <CrossSvgIcon @click="closeAddIncomeCatModal" />
                    </button>
                </div>

                <div class="modal-body">
                    <form action="">
                        <div class="form-item">
                            <label class="my-2">Income Category Name</label>
                            <p
                                class="text-danger"
                                v-if="
                                    incomeCategoryStore
                                        .add_income_category_errors.name
                                "
                            >
                                {{
                                    incomeCategoryStore
                                        .add_income_category_errors.name
                                }}
                            </p>
                            <input
                                type="text"
                                class="form-control"
                                v-model="income_category_data.name"
                            />
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button
                        class="btn btn-danger btn-sm"
                        @click="closeAddIncomeCatModal"
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
