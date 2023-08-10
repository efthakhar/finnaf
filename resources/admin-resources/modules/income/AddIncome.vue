<script setup>
import { computed, onMounted } from "vue";
import CrossSvgIcon from "../../assets/icons/cross-svg-icon.vue";
import { useIncomeStore } from "./incomeStore";
import Multiselect from "@vueform/multiselect";

const emit = defineEmits(["close", "refreshData"]);
const props = defineProps(["categories"]);

const incomeStore = useIncomeStore();
const income_data = computed(() => incomeStore.current_income_item);

async function submitData() {
    incomeStore
        .addIncome(JSON.parse(JSON.stringify(incomeStore.current_income_item)))
        .then(() => {
            emit("refreshData");
            emit("close");
        })
        .catch((error) => {
            console.log("error occurred");
        });
}

async function closeAddIncomeModal() {
    incomeStore.resetCurrentIncomeData();
    emit("close");
}

onMounted(() => {
    incomeStore.resetCurrentIncomeData();
});
</script>

<template>
    <div class="modal fade show d-block">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Income</h5>
                    <button type="button" class="close">
                        <CrossSvgIcon @click="closeAddIncomeModal" />
                    </button>
                </div>

                <div class="modal-body">
                    <form action="">
                        <div class="form-item">
                            <label class="my-2">Income Short Title</label>
                            <p
                                class="text-danger"
                                v-if="incomeStore.add_income_errors.title"
                            >
                                {{ incomeStore.add_income_errors.title }}
                            </p>
                            <input
                                type="text"
                                class="form-control"
                                v-model="income_data.title"
                            />
                        </div>
                        <div class="form-item">
                            <label class="my-2">Income Category</label>
                            <p
                                class="text-danger"
                                v-if="incomeStore.add_income_errors.categories"
                            >
                                {{ incomeStore.add_income_errors.categories }}
                            </p>

                            <Multiselect
                                :searchable="true"
                                mode="tags"
                                :hide-selected="false"
                                v-model="income_data.categories"
                                :options="categories"
                            ></Multiselect>
                        </div>
                        <div class="form-item">
                            <label class="my-2">Income Date</label>
                            <p
                                class="text-danger"
                                v-if="incomeStore.add_income_errors.date"
                            >
                                {{ incomeStore.add_income_errors.date }}
                            </p>
                            <input
                                type="date"
                                class="form-control"
                                v-model="income_data.date"
                            />
                        </div>
                        <div class="form-item">
                            <label class="my-2">Income Amount</label>
                            <p
                                class="text-danger"
                                v-if="incomeStore.add_income_errors.amount"
                            >
                                {{ incomeStore.add_income_errors.amount }}
                            </p>
                            <input
                                type="number"
                                class="form-control"
                                v-model="income_data.amount"
                            />
                        </div>
                        <div class="form-item">
                            <label class="my-2">Description</label>
                            <textarea
                                v-model="income_data.description"
                                class="form-control"
                                rows="5"
                            ></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button
                        class="btn btn-danger btn-sm"
                        @click="closeAddIncomeModal"
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
