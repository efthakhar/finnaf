import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    mode: "hash",
    history: createWebHistory(),

    routes: [
        {
            path: "/admin",
            name: "admin",
            component: () => import("../views/Admin.vue"),
            children: [
                // dashboard route
                {
                    name: "dashboard",
                    path: "",
                    component: () =>
                        import("../modules/dashboard/Dashboard.vue"),
                },

                // Incomes Route
                {
                    name: "incomes",
                    path: "incomes",
                    component: () => import("../modules/income/Incomes.vue"),
                },
                // Incomes Category Route
                {
                    name: "income_categories",
                    path: "income-categories",
                    component: () =>
                        import(
                            "../modules/income-category/IncomeCategories.vue"
                        ),
                },

                // Expenses Route
                {
                    name: "expenses",
                    path: "expenses",
                    component: () => import("../modules/expense/Expenses.vue"),
                },
                // Expenses Category Route
                {
                    name: "expense_categories",
                    path: "expense-categories",
                    component: () =>
                        import(
                            "../modules/expense-category/ExpenseCategories.vue"
                        ),
                },

                // Demo Site Visitors Track 
                {
                    name: "visitors",
                    path: "visitors",
                    component: () =>
                        import(
                            "../modules/visitor/Visitors.vue"
                        ),
                },
            ],
        },
    ],
});

export default router;
