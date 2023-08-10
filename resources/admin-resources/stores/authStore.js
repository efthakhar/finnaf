import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "axios";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        fetched: false,
        user: {},
        authenticated: 0,
    }),

    getters: {},

    actions: {
        async getAuthUser() {
            await axios
                .get(`/api/users/authenticated-user`)
                .then((response) => {
                    this.user = response.data.user;
                    this.authenticated = response.data.authenticated;
                })
                .catch((errors) => {});
        },

        // userCan(ability) {
        //     return this.permissions.includes(ability);
        // },
    },
});
