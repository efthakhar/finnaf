<script setup>
import { onMounted, ref } from "vue";
import { defineAsyncComponent } from "vue";
import { useSidebar } from "../stores/sidebar";

const sidebarStore = useSidebar();

const props = defineProps({ link_details: Object });

const show_sublinks = ref(false);
const sublinks = ref([]);

function handleNavlinkClick() {
    if (window.innerWidth <= 767) {
        useSidebar().toggle();
    }
}

const Icon = defineAsyncComponent(() =>
    import(`../assets/icons/${props.link_details.icon_name}.vue`)
);

onMounted(() => {

    sublinks.value = props.link_details.sub_links;
    if (sublinks.value) {
        sublinks.value = sublinks.value.map((obj) => obj.link);
    }
});
</script>

<template>
    <div>
        <!-- For Links Only -->
        <div v-if="!link_details.sub_links">
            <li
                class="sidebar-item"
                :class="{ active: $route.fullPath == link_details.link }"
            >
                <RouterLink
                    @click="handleNavlinkClick"
                    class="sidebar-link"
                    :to="link_details.link"
                    v-if="!link_details.sub_links"
                >
                    <Icon />
                    <span>{{ link_details.label }}</span>
                </RouterLink>
            </li>
        </div>

        <!-- For Links with SubLinks -->
        <div v-if="link_details.sub_links">
            <li
                class="sidebar-item has-sub"
                :class="{ active: sublinks.includes($route.fullPath) }"
            >
                <span
                    href="#"
                    class="sidebar-link"
                    @click="show_sublinks = !show_sublinks"
                >
                    <Icon />
                    <span>{{ link_details.label }}</span>
                </span>
                {{}}
                <ul class="submenu" :class="{ active: show_sublinks }">
                    <div v-for="slink in link_details.sub_links">
                        <RouterLink
                            @click="handleNavlinkClick"
                            class="sidebar-sublink"
                            :class="{ active: $route.fullPath == slink.link }"
                            :to="slink.link"
                        >
                            <span>{{ slink.label }}</span>
                        </RouterLink>
                    </div>
                </ul>
            </li>
        </div>
    </div>
</template>
