import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router);

export default new Router({
    mode: 'history',
    routes: [
        {
            path:'/login',
            name: 'login',
            component: () => import('./components/Login.vue')
        },
        {
            path:'/',
            name: 'home',
            component: () => import('./components/Home.vue'),
            beforeEnter: (to, from, next) => {
                if(!localStorage.getItem('token')) next('/login')
                else next();
            }
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('./components/Register.vue')
        },
        {
            path: '/profile',
            name: 'profile',
            component: () => import('./components/Profile.vue'),
            beforeEnter: (to, from, next) => {
                if(!localStorage.getItem('token')) next('/login')
                else next();
            }
        },
        {
            path: '/profile/:id',
            name: 'userProfile',
            component: () => import('./components/UserProfile.vue')
        },
        {
            path: '/admin',
            name: 'admin',
            component: () => import('./components/AdminPanel.vue'),
            beforeEnter: (to, from, next) => {
                if(!localStorage.getItem('token')) next('/login')
                else next();
            }
                
        },
        {
            path: '/admin/reports',
            name: 'reports',
            component: () => import('./components/Reports.vue'),
            beforeEnter: (to, from, next) => {
                if(!localStorage.getItem('token')) next('/login')
                else next();
            }

        },
        {
            path: '/post/:id',
            name: 'post',
            component: () => import('./components/Posting.vue'),
            beforeEnter: (to, from, next) => {
                if(!localStorage.getItem('token')) next('/login')
                else next();
            }

        },
        {
            path: '/verifica/:email/:code',
            name: 'verifica',
            component: () => import('./components/Verifica.vue'),

        },
        {
            path: '/admin/applications',
            name: 'applications',
            component: () => import('./components/Applications.vue'),
            beforeEnter: (to, from, next) => {
                if(!localStorage.getItem('token')) next('/login')
                else next();
            }

        },
        {
            path: '/aplica',
            name: 'aplica',
            component: () => import('./components/Aplica.vue'),
            beforeEnter: (to, from, next) => {
                if(!localStorage.getItem('token')) next('/login')
                else next();
            }
                
        },
        {
            path: '/notifications',
            name: 'notifications',
            component: () => import('./components/Notifications.vue'),
            beforeEnter: (to, from, next) => {
                if(!localStorage.getItem('token')) next('/login')
                else next();
            }

        },
        {
            path: '/admin/applications/:id',
            name: 'aplicationID',
            component: () => import('./components/Application.vue'),
            beforeEnter: (to, from, next) => {
                if(!localStorage.getItem('token')) next('/login')
                else next();
            }
        },

        {
            path :'*',
            component: () => import('./components/404.vue')
        }

    ],
})

