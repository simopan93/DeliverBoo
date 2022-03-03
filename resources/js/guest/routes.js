import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

//import vari
import Home from '../components/pages/Home';
import Restaurants from '../components/pages/Restaurants.vue'


import Error404 from '../components/pages/Error404'

const router = new VueRouter({
  mode: 'history',
  linkExactActiveClass: 'active',
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/restaurants/:slug',
      name: 'restaurants',
      component: Restaurants,
      props: {
        provaProp: String
      }
    },
    {
      path: '*',
      component: Error404
    },
    
  ]
})

export default router;