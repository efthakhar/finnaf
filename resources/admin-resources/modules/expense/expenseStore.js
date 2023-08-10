import axios from "axios";
import formatValidationErrors from "../../utils/format-validation-errors";
import { defineStore } from "pinia";
import { useNotificationStore } from "../../components/shared/notification/notificationStore";

export const useExpenseStore = defineStore("expense", {
    state: () => ({
        current_page: 1,
        total_pages: 0,
        limit: 10,

        q_title: "",
        q_category: "",
        q_start_amount: "",
        q_end_amount: "",
        q_start_date: "",
        q_end_date: "",
        q_sort_column: "id",
        q_sort_order: "desc",

        expenses: [],

        edit_expense_id: null,
        view_expense_id: null,

        add_expense_errors: {},

        edit_expense_errors: {},

        current_expense_item: {
            id: "",
            title: "",
            amount: "",
            date: "",
            description: "",
            categories: [],
        },
    }),

    getters: {},

    actions: {
        resetCurrentExpenseData() {
            this.current_expense_item = {
                id: "",
                title: "",
                amount: "",
                date: "",
                description: "",
                categories: [],
            };
            this.add_expense_errors = [];
            this.edit_expense_errors = [];
        },

        fetchExpenses(page, limit, q_title = "") {
            return new Promise((resolve, reject) => {
                axios
                    .get(
                        `/api/expenses?page=${page}&limit=${limit}&title=${q_title}&category=${this.q_category}&start_amount=${this.q_start_amount}&end_amount=${this.q_end_amount}&start_date=${this.q_start_date}&end_date=${this.q_end_date}&sort_column=${this.q_sort_column}&sort_order=${this.q_sort_order}`
                    )
                    .then((response) => {
                        this.expenses = response.data.data;
                        if (response.data.meta) {
                            this.total_pages = response.data.meta.last_page;
                            this.current_page = response.data.meta.current_page;
                            this.limit = response.data.meta.per_page;
                            this.q_title = q_title;
                        }
                        resolve(this.expenses);
                    })
                    .catch((errors) => {
                        reject(errors);
                    });
            });
        },

        async fetchExpense(id) {
            return new Promise((resolve, reject) => {
                axios
                    .get(`/api/expenses/${id}`)
                    .then((response) => {
                        this.current_expense_item = response.data.data;

                        this.current_expense_item.categories_details =
                            response.data.data.categories;

                        this.current_expense_item.categories =
                            response.data.data.categories.map(
                                (item) => item.value
                            );
                        resolve(response.data.data);
                    })
                    .catch((errors) => {
                        reject(errors);
                    });
            });
        },

        async addExpense(data) {
            return new Promise((resolve, reject) => {
                axios
                    .post(`/api/expenses`, data)
                    .then((response) => {
                        this.resetCurrentExpenseData();
                        const notifcationStore = useNotificationStore();
                        notifcationStore.pushNotification({
                            message: "Expense Added Successfully",
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
                            this.add_expense_errors = formatValidationErrors(
                                error.response.data.errors
                            );
                        }
                        reject(error);
                    });
            });
        },

        async editExpense(data) {
            return new Promise((resolve, reject) => {
                axios
                    .put(`/api/expenses/${this.edit_expense_id}`, data)
                    .then((response) => {
                        this.resetCurrentExpenseData();
                        const notifcationStore = useNotificationStore();
                        notifcationStore.pushNotification({
                            message: "expense record updated successfully",
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
                            this.edit_expense_errors = formatValidationErrors(
                                errors.response.data.errors
                            );
                        }
                        reject(errors);
                    });
            });
        },

        async deleteExpense(id) {
            return new Promise((resolve, reject) => {
                axios
                    .delete(`/api/expenses/${id}`)
                    .then((response) => {
                        if (
                            this.expenses.length == 1 ||
                            (Array.isArray(id) &&
                                id.length == this.expenses.length)
                        ) {
                            this.current_page == 1
                                ? (this.current_page = 1)
                                : (this.current_page -= 1);
                        }

                        this.resetCurrentExpenseData();
                        const notifcationStore = useNotificationStore();
                        notifcationStore.pushNotification({
                            message: "expense deleted successfully",
                            type: "success",
                            time: 2000,
                        });

                        resolve(response);
                    })
                    .catch((error) => {
                        reject(error);
                    });
            });
        },
    },
});
