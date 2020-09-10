
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = require('vue');


import VueSocketio from 'vue-socket.io';
Vue.use(new VueSocketio({
    debug: true,
    connection: 'http://178.62.108.89:8080'
}));

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Raphael from 'raphael/raphael'
global.Raphael = Raphael;

import store from './stores/global-store';

import Vuetify from 'vuetify';

Vue.use(Vuetify);

import Toasted from 'vue-toasted';

import VueSnackbar from 'vue-snack' 

import Timer from './components/user/Timer.vue'

import VueSingleSelect from "vue-single-select";
Vue.component('vue-single-select', VueSingleSelect);

Vue.use(Toasted, {
    position: 'top-right',
    duration: 5000,
   //type: 'info',
});
Vue.use(VueSnackbar, {
    position: 'top',
    duration: 5000,
  // type: 'error',
});

store.commit('loadTokenAndUserFromSession');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

const comp= Vue.component('example-component', require('./components/ExampleComponent.vue').default);
//const accessHistory= Vue.component('accessHistory', require('./components/admin/accessHistory.vue').default);
const accessPermissions= Vue.component('accessPermissions', require('./components/admin/accessPermissions.vue').default);
const userList= Vue.component('userList', require('./components/admin/userList.vue').default);
const roomList= Vue.component('roomList', require('./components/admin/roomList.vue').default);
const profile = Vue.component('profile', require('./components/user/profile.vue').default);
const login = Vue.component('login', require('./components/user/login.vue').default);
const changepassword = Vue.component('changepassword', require('./components/user/changepassword.vue').default);
const my_sensors = Vue.component('my_sensors', require('./components/sensor/my_sensors.vue').default);

const my_actuators = Vue.component('my_actuators', require('./components/sensor/my_actuators.vue').default);

const roomHistory= Vue.component('roomHistory', require('./components/room/roomHistory.vue').default);
const myHistory= Vue.component('myHistory', require('./components/user/myHistory.vue').default);

const timeHistory= Vue.component('timeHistory', require('./components/user/timeHistory.vue').default);

const allTimeHistory= Vue.component('allTimeHistory', require('./components/user/allTimeHistory.vue').default);

const roomUsers= Vue.component('roomUsers', require('./components/room/roomUsers.vue').default);

Vue.component('time-working',Timer);

const blockchain= Vue.component('blockchain', require('./components/user/blockchain.vue').default);

const routes = [
    //{path:'/login',component:login},
    {path:'/accessPermissions',component:accessPermissions,name:'accessPermissions'},
    //{path:'/accessHistory',component:accessHistory,name:'accessHistory'},
    {path:'/userList',component:userList,name:'userList'},
    {path:'/roomList',component:roomList,name:'roomList'},
    {path:'/preferences',name:'preferences'},
    {path:'/comp',component:comp,name:'comp'},
    {path:'/login',component:login,name:'login'},
    {path:'/profile',component:profile,name:'profile'},
    {path:'/changepassword',component:changepassword,name:'changepassword'},
    {path:'/my_sensors',component:my_sensors,name:'my_sensors'},

    {path:'/my_actuators',component:my_actuators,name:'my_actuators'},
    {path:'/roomHistory/:room_id',component:roomHistory,name:'roomHistory'},
    {path:'/roomUsers/:room_id',component:roomUsers,name:'roomUsers'},
    {path:'/myHistory',component:myHistory,name:'myHistory'},

    {path:'/timeHistory',component:timeHistory,name:'timeHistory'},
    {path:'/allTimeHistory',component:allTimeHistory,name:'allTimeHistory'},
    {path:'/blockchain',component:blockchain,name:'blockchain'},


   // {path:'*',redirect:'/login' ,name:'root'},
];



const router = new VueRouter({
    routes: routes
});

 router.beforeEach((to, from, next) => {
    //if (!to.name || (to.name != 'login') || (to.name == 'logout')|| (to.name == 'changepassword')|| (to.name == 'history')|| (to.name == 'preferences')) {
        if (store.state.user==null) {
            console.log("N LOGADO")
            console.log(store.state.user)
            if (to.name!="login") {
                next("/login");
                return;
            }else {
                next();
                return;
            }
        }else {
            console.log("tasdasdsd))")
            if (to.name=="login") {
                next("/profile");
                return;
            }
            next();
            return;
        }
});
/*
 router.beforeEach((to, from, next) => {
    if (to.name != 'login') {
            next("/login");
    //if ((to.name != 'login') || (to.name == 'logout')|| (to.name == 'changepassword')|| (to.name == 'history')|| (to.name == 'preferences')) {
        store.commit('loadTokenAndUserFromSession');
        if (!store.state.user) {
            console.log(store.state.user);
            console.log("store.state.user");
            next("/login");
            return;
        }else{
            next();
            return;
        }
    }
    //next();
});*/

const app = new Vue({
    store,
    router,
    data:{
        drawer:false,
        msgGlobalText: "",
        msgGlobalTextArea: "",
        msgManagerText: "",
        msgManagerTextArea: "",
        selected: '',

      },
      methods: {
        logout: function(){
            this.$socket.emit('user_exit',this.$store.state.user);
            this.$store.commit('clearUserAndToken');
            this.$router.push('/login');
        }
    
      },
        sockets:{
            connect(){
                console.log('socket connected (socket ID = '+this.$socket.id+')');
            },  
            text_from_server_managers(dataFromServer){
                console.log('Receiving this message from Server: "' + dataFromServer + '"');            
                this.msgManagerTextArea = dataFromServer + '\n' + this.msgManagerTextArea ;
            },
            user_changed(dataFromServer){
                this.$toasted.show('User "' + dataFromServer.name + '" (ID= ' + dataFromServer.id + ') has changed');
            },
            access_granted(dataFromServer){
                this.$toasted.show(dataFromServer,{
                        type:'success'
                    });
            }, 
            access_denied(dataFromServer){
                this.$toasted.show(dataFromServer,{
                        type:'error'
                    });
            },
            access_denied_unknown(dataFromServer){
                this.$toasted.show('ACCESS UNKOWN',{
                        type:'error'
                    });
            }, 
            danger(dataFromServer){
                /*this.$snack.show({
                    text: dataFromServer,
                    button: 'Close',
                    action: this.clickAction
                  });*/

                this.$toasted.show(dataFromServer,{
                        type:'error',
                        position:'top-center',
                        fitToScreen:true,
                        fullWidth:true,
                        singleton:true,
                        duration:null,
                        dontClose : true,
                        action : [
                        {
                            text : 'Cancel',
                            onClick : (e, toastObject) => {
                                toastObject.goAway(0);
                            }
                        },]
                    });
            },  

        },
        /*components: {
            timeWorking,
        },*/
      created() {
          console.log('-----');
          console.log(this.$store.state.user);
          console.log(this.$store.state.user);
          console.log("this.$store.state.user");
      }
}).$mount("#app");