<script setup>
import { ref, computed, onMounted } from "vue";
import CrossSvgIcon from "../../assets/icons/cross-svg-icon.vue";
import Loader from "../../components/shared/loader/Loader.vue";
import { useIncomeCategoryStore } from "./incomeCategoryStore";

const props = defineProps(["income_category_id"]);
const emit = defineEmits(["close", "refreshData"]);

const loading = ref(false);
const incomeCategoryStore = useIncomeCategoryStore();
const income_category_data = computed(
    () => incomeCategoryStore.current_income_category_item
);

async function submitData() {
    incomeCategoryStore
        .editIncomeCat(
            JSON.parse(
                JSON.stringify(incomeCategoryStore.current_income_category_item)
            )
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
    await incomeCategoryStore.fetchIncomeCat(id);
    loading.value = false;
}

async function closeEditIncomeModal() {
    incomeCategoryStore.resetCurrentIncomeCatData();
    emit("close");
}

onMounted(async () => {
    fetchData(props.income_category_id);
});
</script>

<template>
    <div class="modal fade show d-block">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Income</h5>
                    <button type="button" class="close">
                        <CrossSvgIcon @click="closeEditIncomeModal" />
                    </button>
                </div>

                <div class="modal-body">
                    <Loader v-if="loading" />
                    <div class="form-items" v-if="loading == false">
                        <form action="">
                            <div class="form-item">
                                <label class="my-2">Income Category Name</label>
                                <p
                                    class="text-danger"
                                    v-if="
                                        incomeCategoryStore
                                            .edit_income_category_errors.name
                                    "
                                >
                                    {{
                                        incomeCategoryStore
                                            .edit_income_category_errors.name
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
                </div>

                <div class="modal-footer">
                    <button
                        class="btn btn-danger btn-sm"
                        @click="closeEditIncomeModal"
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
