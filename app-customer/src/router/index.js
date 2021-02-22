import Vue from "vue";
import VueRouter from "vue-router";
import Home from "../views/Home.vue";

Vue.use(VueRouter);

const routes = [{
    path: "/:qr",
    name: "Home",
    component: Home,
    meta: {
        title: 'Home',
    }
},
{
    path: "/404",
    name: "Page404",
    component: () =>
        import("@/views/Page404.vue"),
    meta: {
        title: 'Not Found',
    }
}
];

const router = new VueRouter({
    mode: "history",
    base: process.env.BASE_URL,
    routes
});

router.beforeEach((to, from, next) => {
    let title = to.meta.title || 'CustomerApp';
    document.title = `${title} | CustomerApp`;

    let urValid = false;
    if (to.params && to.params.qr) {
        let path = to.params.qr.split('-');
        if (path[0] == 'product' || path[0] == 'package' || path[0] == 'transport') {
            urValid = true;
        }
    }

    if (urValid || to.name == "Page404") {
        next();
    }
    else {
        next({ name: "Page404" });
    }
});

export default router;