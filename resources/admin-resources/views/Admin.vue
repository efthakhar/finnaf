<script setup>
import Sidebar from "../components/Sidebar.vue";
import Navbar from "../components/Navbar.vue";
import Loader from "../components/shared/loader/Loader.vue";
import NoticationsContainer from "../components/shared/notification/notications-container.vue";
import ConfirmBox from "../components/shared/confirm-alert/confirm-box.vue";
import { useConfirmStore } from "../components/shared/confirm-alert/confirmStore";
import { onMounted, ref } from "vue";
import { useAuthStore } from "../stores/authStore";

const adminReady = ref(false);
const authStore = useAuthStore();
const confirmStore = useConfirmStore();

onMounted(async () => {
    adminReady.value = false;

    await authStore.getAuthUser();

    if (authStore.authenticated !== 1) {
        window.location.href = "/";
    } else {
        adminReady.value = true;
    }
});
</script>
<template>
    <div>
        <div v-if="adminReady == false" class="pt-5">
            <Loader />
        </div>
        <div id="app" v-if="adminReady == true && authStore.authenticated == 1">
            <Sidebar />
            <div id="main">
                <Navbar />
                <div class="main-content container-fluid">
                    <router-view />
                </div>
            </div>
        </div>
        <div class="admin-area-modals-container">
            <ConfirmBox v-if="confirmStore.show_confirm_box" />
            <NoticationsContainer />
        </div>
    </div>
</template>

<style src="@vueform/multiselect/themes/default.css"></style>
<style>
.multiselect-tag {
    background: #5a8dee;
}
.multiselect-option.is-selected,
.multiselect-option.is-selected.is-pointed {
    background: #5a8eeea4;
}
.multiselect-option.is-selected.is-pointed:hover {
    background: #5a8eee;
}
</style>
