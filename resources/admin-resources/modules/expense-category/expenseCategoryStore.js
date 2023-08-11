import axios from "axios";
import formatValidationErrors from "../../utils/format-validation-errors";
import { defineStore } from "pinia";
import { useNotificationStore } from "../../components/shared/notification/notificationStore";

export const useExpenseCategoryStore = defineStore("expense_category", {
    state: () => ({
        current_page: 1,
        total_pages: 0,
        limit: 10,

        q_name: "",

        expense_categories: [],

        edit_expense_category_id: null,
        view_expense_category_id: null,

        add_expense_category_errors: {},

        edit_expense_category_errors: {},

        current_expense_category_item: {
            id: "",
            name: "",
        },
    }),

    getters: {},

    actions: {
        resetCurrentExpenseCatData() {
            this.current_expense_category_item = {
                id: "",
                name: "",
            };
            this.add_expense_category_errors = [];
            this.edit_expense_category_errors = [];
        },

        fetchCatList() {
            return new Promise((resolve, reject) => {
                axios
                    .get(`/api/expense-categories/list`)
                    .then((response) => {
                        resolve(response.data.data);
                    })
                    .catch((errors) => {
                        reject(errors);
                    });
            });
        },

        fetchExpenseCats(page, limit, q_name = "") {
            return new Promise((resolve, reject) => {
                axios
                    .get(
                        `/api/expense-categories?page=${page}&limit=${limit}&name=${q_name}`
                    )
                    .then((response) => {
                        this.expense_categories = response.data.data;
                        if (response.data.meta) {
                            this.total_pages = response.data.meta.last_page;
                            this.current_page = response.data.meta.current_page;
                            this.limit = response.data.meta.per_page;
                            this.q_name = q_name;
                        }
                        resolve(this.expense_categories);
                    })
                    .catch((errors) => {
                        reject(errors);
                    });
            });
        },

        async fetchExpenseCat(id) {
            return new Promise((resolve, reject) => {
                axios
                    .get(`/api/expense-categories/${id}`)
                    .then((response) => {
                        this.current_expense_category_item = response.data.data;
                        resolve(response.data.data);
                    })
                    .catch((errors) => {
                        reject(errors);
                    });
            });
        },

        async addExpenseCat(data) {
            return new Promise((resolve, reject) => {
                axios
                    .post(`/api/expense-categories`, data)
                    .then((response) => {
                        this.resetCurrentExpenseCatData();
                        const notifcationStore = useNotificationStore();
                        notifcationStore.pushNotification({
                            message: "ExpenseCat Added Successfully",
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
                            this.add_expense_category_errors =
                                formatValidationErrors(
                                    error.response.data.errors
                                );
                        }
                        reject(error);
                    });
            });
        },

        async editExpenseCat(data) {
            return new Promise((resolve, reject) => {
                axios
                    .put(
                        `/api/expense-categories/${this.edit_expense_category_id}`,
                        data
                    )
                    .then((response) => {
                        this.resetCurrentExpenseCatData();
                        const notifcationStore = useNotificationStore();
                        notifcationStore.pushNotification({
                            message: "expense category updated successfully",
                            type: "success",
                        });
                        resolve(response);
                    })
                    .catch((errors) => {
                        const notifcationStore = useNotificationStore();
                        notifcationStore.pushNotification({
                            message: "Error Occurred",
                            type: "error",
                        });

                        if (errors.response.status == 422) {
                            this.edit_expense_category_errors =
                                formatValidationErrors(
                                    errors.response.data.errors
                                );
                        }
                        reject(errors);
                    });
            });
        },

        async deleteExpenseCat(id) {
            return new Promise((resolve, reject) => {
                axios
                    .delete(`/api/expense-categories/${id}`)
                    .then((response) => {
                        if (
                            this.expense_categories.length == 1 ||
                            (Array.isArray(id) &&
                                id.length == this.expense_categories.length)
                        ) {
                            this.current_page == 1
                                ? (this.current_page = 1)
                                : (this.current_page -= 1);
                        }

                        this.resetCurrentExpenseCatData();
                        const notifcationStore = useNotificationStore();
                        notifcationStore.pushNotification({
                            message: "expense category deleted successfully",
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
                                    "Category is associated with non zero expense records. Delete that expenses first.",
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
