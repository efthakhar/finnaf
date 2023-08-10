<script setup>
import { ref, computed, onMounted } from "vue";
import CrossSvgIcon from "../../assets/icons/cross-svg-icon.vue";
import Loader from "../../components/shared/loader/Loader.vue";
import { useIncomeStore } from "./incomeStore";

const props = defineProps(["income_id"]);
const emit = defineEmits(["close", "refreshData"]);

const loading = ref(false);
const incomeStore = useIncomeStore();
const income_data = computed(() => incomeStore.current_income_item);

async function fetchData(id) {
    loading.value = true;
    await incomeStore.fetchIncome(id);
    loading.value = false;
}

async function closeViewIncomeModal() {
    incomeStore.resetCurrentIncomeData();
    emit("close");
}

onMounted(async () => {
    fetchData(props.income_id);
});
</script>

<template>
    <div class="modal fade show d-block">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Income Record Details</h5>
                    <button type="button" class="close">
                        <CrossSvgIcon @click="closeViewIncomeModal" />
                    </button>
                </div>

                <div class="modal-body">
                    <Loader v-if="loading" />
                    <div class="form-items" v-if="loading == false">
                        <form action="">
                            <div class="form-item">
                                <label class="my-2">Income Short Title: </label>

                                <input
                                    disabled
                                    type="text"
                                    class="form-control"
                                    v-model="income_data.title"
                                />
                            </div>
                            <div class="form-item">
                                <label class="my-2">Income Category: </label>
                                <div>
                                    <span
                                        :key="income_cat.value"
                                        v-for="income_cat in income_data.categories_details"
                                        class="badge bg-primary m-1 px-2 shadow-sm py-2 rounded-2"
                                    >
                                        {{ income_cat.label }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-item">
                                <label class="my-2">Income Date: </label>

                                <input
                                    disabled
                                    type="date"
                                    class="form-control"
                                    v-model="income_data.date"
                                />
                            </div>
                            <div class="form-item">
                                <label class="my-2">Income Amount: </label>

                                <input
                                    disabled
                                    type="number"
                                    class="form-control"
                                    v-model="income_data.amount"
                                />
                            </div>
                            <div class="form-item">
                                <label class="my-2">Description: </label>
                                <textarea
                                    disabled
                                    v-model="income_data.description"
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
