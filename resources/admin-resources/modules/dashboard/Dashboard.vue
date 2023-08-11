<script setup>
import { ref, onMounted } from "vue";
import VueApexCharts from "vue3-apexcharts";
import Loader from "../../components/shared/loader/Loader.vue";
import axios from "axios";
import WalletSvgIcon from "../../assets/icons/wallet-svg-icon.vue";
import CoinSvgIcon from "../../assets/icons/coin-1-svg-icon.vue";
import twentyfourSVGICon from "../../assets/icons/twentyfour-svg-icon.vue";
import HandLoveSVGICon from "../../assets/icons/hand-love-svg-icon.vue";

const loading = ref(false);
const report_data = ref({});

let incomes_chart_data = ref({
    chartOptions: {
        title: {
            text: "Current Month Incomes",
            align: "left",
            style: { color: "#475f7b" },
        },
        chart: {
            id: "incomes_chart",
        },
        xaxis: {
            categories: [],
        },
    },
    series: [
        {
            name: "income",
            data: [],
        },
    ],
});
let expenses_chart_data = ref({
    chartOptions: {
        title: {
            text: "Current Month Expenses",
            align: "left",
            style: { color: "#475f7b" },
        },
        chart: {
            id: "expenses_chart",
        },
        xaxis: {
            categories: [],
        },
    },
    series: [
        {
            name: "expenses",
            data: [],
        },
    ],
});

let incomeCatData = ref({
    series: [],
    chartOptions: {
        title: {
            text: "Income Categories",
            align: "left",
            style: { color: "#475f7b" },
        },
        labels: [],
        theme: {
            monochrome: {
                enabled: true,
            },
        },
        // responsive: [
        //     {
        //         breakpoint: 480,
        //         options: {
        //             chart: {
        //                 width: 200,
        //             },
        //             legend: {
        //                 position: "bottom",
        //             },
        //         },
        //     },
        // ],
    },
});
let expenseCatData = ref({
    series: [],
    chartOptions: {
        title: {
            text: "Expense Categories",
            align: "left",
            style: { color: "#475f7b" },
        },
        labels: [],
        theme: {
            monochrome: {
                enabled: true,
            },
        },
        // responsive: [
        //     {
        //         breakpoint: 480,
        //         options: {
        //             chart: {
        //                 width: 200,
        //             },
        //             legend: {
        //                 position: "bottom",
        //             },
        //         },
        //     },
        // ],
    },
});

async function fetchData() {
    loading.value = true;
    await axios
        .get(`/api/dashboard-reports`)
        .then((response) => {
            report_data.value = response.data;

            incomes_chart_data.value.chartOptions.xaxis.categories =
                response.data.current_month_incomes.map(
                    (income) => income.date
                );
            incomes_chart_data.value.series[0].data =
                response.data.current_month_incomes.map(
                    (income) => income.amount
                );

            expenses_chart_data.value.chartOptions.xaxis.categories =
                response.data.current_month_expenses.map(
                    (expense) => expense.date
                );
            expenses_chart_data.value.series[0].data =
                response.data.current_month_expenses.map(
                    (expense) => expense.amount
                );

            incomeCatData.value.chartOptions.labels =
                response.data.incomeCatData.map((income) => income.name);
            incomeCatData.value.series = response.data.incomeCatData.map(
                (income) => income.incomes_count
            );

            expenseCatData.value.chartOptions.labels =
                response.data.expenseCatData.map((expense) => expense.name);
            expenseCatData.value.series = response.data.expenseCatData.map(
                (expense) => expense.expenses_count
            );
        })
        .catch((errors) => {
            console.log(errors);
        });
    loading.value = false;
}

onMounted(async () => {
    fetchData();
});
</script>

<template>
    <div class="dashboard-page">
        <Loader v-if="loading" />
        <div class="dashboard-page-contents mx-2" v-if="loading == false">
            <div class="dashboard-top-stats row flex-wrap">
                <!-- Current Month Income  -->
                <div class="col-md-3 col-sm-6 my-1 p-1 min150">
                    <div
                        class="bg-white shadow-sm d-flex flex-wrap rounded-3 p-3 align-items-center"
                    >
                        <div class="bg-info p-3 rounded-3 me-4">
                            <twentyfourSVGICon
                                color="white"
                                width="28"
                                height="28"
                            />
                        </div>
                        <div class="my-2">
                            <span class="h3">{{
                                report_data.today_incomes
                            }}</span>
                            <br />
                            <span class="text-muted fs-6">Today Incomes</span>
                        </div>
                    </div>
                </div>
                <!-- Total Income -->
                <div class="col-md-3 col-sm-6 my-1 p-1 min150">
                    <div
                        class="bg-white shadow-sm d-flex flex-wrap rounded-3 p-3 align-items-center"
                    >
                        <div class="bg-info p-3 rounded-3 me-4">
                            <WalletSvgIcon
                                color="white"
                                width="28"
                                height="28"
                            />
                        </div>
                        <div class="my-2">
                            <span class="h3">{{
                                report_data.total_incomes
                            }}</span>
                            <br />
                            <span>Total Incomes</span>
                        </div>
                    </div>
                </div>
                <!-- Total Expenses -->
                <div class="col-md-3 col-sm-6 my-1 p-1 min150">
                    <div
                        class="bg-white shadow-sm d-flex flex-wrap rounded-3 p-3 align-items-center"
                    >
                        <div class="bg-info p-3 rounded-3 me-4">
                            <CoinSvgIcon color="white" width="28" height="28" />
                        </div>
                        <div class="my-2">
                            <span class="h3">{{
                                report_data.total_expenses
                            }}</span>
                            <br />
                            <span>Total Expenses</span>
                        </div>
                    </div>
                </div>
                <!-- Net Income -->
                <div class="col-md-3 col-sm-6 my-1 p-1 min150">
                    <div
                        class="bg-white shadow-sm d-flex flex-wrap rounded-3 p-3 align-items-center"
                    >
                        <div class="bg-info p-3 rounded-3 me-4">
                            <HandLoveSVGICon
                                color="white"
                                width="28"
                                height="28"
                            />
                        </div>
                        <div class="my-2">
                            <span class="h3">{{
                                report_data.net_incomes
                            }}</span>
                            <br />
                            <span>Net Incomes</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dashboard-charts my-3 row">
                <div class="col-md-6 p-1">
                    <VueApexCharts
                        width="100%"
                        height="350"
                        type="bar"
                        :options="incomes_chart_data.chartOptions"
                        :series="incomes_chart_data.series"
                    />
                </div>
                <div class="col-md-6 p-1">
                    <VueApexCharts
                        width="100%"
                        height="350"
                        type="bar"
                        :options="expenses_chart_data.chartOptions"
                        :series="expenses_chart_data.series"
                    />
                </div>
            </div>
            <div class="dashboard-charts my-3 row">
                <div class="col-md-6 p-1">
                    <VueApexCharts
                        type="pie"
                        width="100%"
                        :options="incomeCatData.chartOptions"
                        :series="incomeCatData.series"
                    />
                </div>
                <div class="col-md-6 p-1">
                    <VueApexCharts
                        type="pie"
                        width="100%"
                        :options="expenseCatData.chartOptions"
                        :series="expenseCatData.series"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
