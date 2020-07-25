import Profile from './pages/Profile';
import VueRouter from "vue-router";
import Main from "./pages/Main";
import Users from './pages/Users';
import User from './pages/User';

const routes = [
    {path: '/', component: Main},
    {path: '/profile', component: Profile},
    {path: '/users', component: Users},
    {path: '/users/:id', component: User}
];

export default new VueRouter({routes});