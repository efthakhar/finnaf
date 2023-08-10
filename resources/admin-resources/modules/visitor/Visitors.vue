<script setup>
import { computed, onMounted, ref } from "vue";
import axios from "axios";
import Loader from "../../components/shared/loader/Loader.vue";
import Pagination from "../../components/shared/pagination/Pagination.vue";

const loading = ref(false);

const visitors = ref([]);
const visit_count = ref(0)
const current_page = ref(1);
const per_page = ref(10);
const total_pages = ref(1);

async function fetchData(page, limit) {
    loading.value = true
    current_page.value = page;
    per_page.value = limit;
    await axios
        .get(`/api/visitors?page=${page}&limit=${limit}`)
        .then((response) => {
            visitors.value = response.data.data.data;
            total_pages.value = response.data.data.last_page;
            loading.value = false;
            visit_count.value = response.data.total_site_visit
        })
        .catch((errors) => {
            // console.log(errors);
            loading.value = false;
        });
}

onMounted(async () => {
    fetchData(1, 10);
});
</script>

<template>
    <div>
        <div class="page-top-box mb-2 d-flex flex-wrap">
            <h3 class="h3">Site Visitors </h3>
            <span class="small-text text-success">({{ visit_count }})</span>
        </div>
        <Loader v-if="loading" />
        <div
            class="bg-white shadow-sm table-responsive"
            v-if="loading == false"
        >
            <table class="table mb-0 table-hover ">
                <thead class="thead-dark">
                    <tr>
                        <th>IP Address</th>
                        <th class="table-action-col">Total Login</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="visitor in visitors" :key="visitor.id">
                        <td class="max100">{{ visitor.ip }}</td>
                        <td class="max100">{{ visitor.count }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination
            v-if="loading == false && visitors.length > 0"
            :total_pages="total_pages"
            :current_page="current_page"
            :per_page="per_page"
            @pageChange="(currentPage) => fetchData(currentPage, per_page)"
            @perPageChange="(perpage) => fetchData(1, perpage)"
        />
    </div>
</template>
