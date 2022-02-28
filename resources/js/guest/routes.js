import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

//import vari
import Home from '../components/pages/Home';

const router = new VueRouter({
  mode: 'history',
  linkExactActiveClass: 'active',
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    
  ]
})

export default router;