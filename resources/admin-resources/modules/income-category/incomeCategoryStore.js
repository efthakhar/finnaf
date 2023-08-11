import axios from "axios";
import formatValidationErrors from "../../utils/format-validation-errors";
import { defineStore } from "pinia";
import { useNotificationStore } from "../../components/shared/notification/notificationStore";

export const useIncomeCategoryStore = defineStore("income_category", {
    state: () => ({
        current_page: 1,
        total_pages: 0,
        limit: 10,

        q_name: "",

        income_categories: [],

        edit_income_category_id: null,
        view_income_category_id: null,

        add_income_category_errors: {},

        edit_income_category_errors: {},

        current_income_category_item: {
            id: "",
            name: "",
        },
    }),

    getters: {},

    actions: {
        resetCurrentIncomeCatData() {
            this.current_income_category_item = {
                id: "",
                name: "",
            };
            this.add_income_category_errors = [];
            this.edit_income_category_errors = [];
        },

        fetchCatList() {
            return new Promise((resolve, reject) => {
                axios
                    .get(`/api/income-categories/list`)
                    .then((response) => {
                        resolve(response.data.data);
                    })
                    .catch((errors) => {
                        reject(errors);
                    });
            });
        },

        fetchIncomeCats(page, limit, q_name = "") {
            return new Promise((resolve, reject) => {
                axios
                    .get(
                        `/api/income-categories?page=${page}&limit=${limit}&name=${q_name}`
                    )
                    .then((response) => {
                        this.income_categories = response.data.data;
                        if (response.data.meta) {
                            this.total_pages = response.data.meta.last_page;
                            this.current_page = response.data.meta.current_page;
                            this.limit = response.data.meta.per_page;
                            this.q_name = q_name;
                        }
                        resolve(this.income_categories);
                    })
                    .catch((errors) => {
                        reject(errors);
                    });
            });
        },

        async fetchIncomeCat(id) {
            return new Promise((resolve, reject) => {
                axios
                    .get(`/api/income-categories/${id}`)
                    .then((response) => {
                        this.current_income_category_item = response.data.data;
                        resolve(response.data.data);
                    })
                    .catch((errors) => {
                        reject(errors);
                    });
            });
        },

        async addIncomeCat(data) {
            return new Promise((resolve, reject) => {
                axios
                    .post(`/api/income-categories`, data)
                    .then((response) => {
                        this.resetCurrentIncomeCatData();
                        const notifcationStore = useNotificationStore();
                        notifcationStore.pushNotification({
                            message: "IncomeCat Added Successfully",
                            type: "success",
                            time: 2000,
                        });

                        resolve();
                    })
                    .catch((error) => {
                        const notifcationStore = useNotificationStore();
                        notifcationStore.pushNotification({
                            message: "Error Occurred",
                            type: "error",
                            time: 2000,
                        });

                        if (error.response.status == 422) {
                            this.add_income_category_errors =
                                formatValidationErrors(
                                    error.response.data.errors
                                );
                        }
                        reject(error);
                    });
            });
        },

        async editIncomeCat(data) {
            return new Promise((resolve, reject) => {
                axios
                    .put(
                        `/api/income-categories/${this.edit_income_category_id}`,
                        data
                    )
                    .then((response) => {
                        this.resetCurrentIncomeCatData();
                        const notifcationStore = useNotificationStore();
                        notifcationStore.pushNotification({
                            message: "income category updated successfully",
                            type: "success",
                        });
                        resolve(response);
                    })
                    .catch((errors) => {
                        console.log(errors);
                        const notifcationStore = useNotificationStore();
                        notifcationStore.pushNotification({
                            message: "Error Occurred",
                            type: "error",
                        });

                        if (errors.response.status == 422) {
                            this.edit_income_category_errors =
                                formatValidationErrors(
                                    errors.response.data.errors
                                );
                        }
                        reject(errors);
                    });
            });
        },

        async deleteIncomeCat(id) {
            return new Promise((resolve, reject) => {
                axios
                    .delete(`/api/income-categories/${id}`)
                    .then((response) => {
                        if (
                            this.income_categories.length == 1 ||
                            (Array.isArray(id) &&
                                id.length == this.income_categories.length)
                        ) {
                            this.current_page == 1
                                ? (this.current_page = 1)
                                : (this.current_page -= 1);
                        }

                        this.resetCurrentIncomeCatData();
                        const notifcationStore = useNotificationStore();
                        notifcationStore.pushNotification({
                            message: "income category deleted successfully",
                            type: "success",
                            time: 2000,
                        });

                        resolve(response);
                    })
                    .catch((errors) => {
                        if (
                            errors.response.data.error_type &&
                            errors.response.data.error_type == "HAS_CHILD_ERROR"
                        ) {
                            const notifcationStore = useNotificationStore();
                            notifcationStore.pushNotification({
                                message:
                                    "Category is associated with non zero income records. Delete that incomes first.",
                                type: "error",
                                time: 5000,
                            });
                        }

                        reject(errors);
                    });
            });
        },
    },
});
