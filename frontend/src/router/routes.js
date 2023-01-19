import HomePage from "@/home/HomePage.vue";

export default [
    {
        path: '/',
        name: 'home',
        component: HomePage,
        meta: {unrestricted: true, onlyUnauthenticated: true},
    },
    {
        path: '*',
        component: () => import(/* webpackChunkName: "errorNotFound" */ '../common/ErrorNotFound'),
    },
];
