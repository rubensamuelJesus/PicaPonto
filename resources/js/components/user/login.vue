<template>
	<div >
	  <v-app id="inspire">
	        <v-layout  justify-space-around align-content-space-around>
	          <v-flex xs12 sm8 md4>
	            <v-card class="elevation-12">
	              <v-toolbar dark color="primary">
	                <v-toolbar-title>Login</v-toolbar-title>
	                <v-spacer></v-spacer>
	                <v-tooltip bottom>
	                    <v-icon large>code</v-icon>
	                  </v-btn>
	                  <span>Source</span>
	                </v-tooltip>
	              </v-toolbar>
	              <v-card-text>
	                <v-form>
	                  <v-text-field v-model="user.email" prepend-icon="person" name="login" label="Email" type="text"></v-text-field>
	                  <v-text-field v-model="user.password" prepend-icon="lock" name="password" label="Password" id="password" type="password"></v-text-field>
	                </v-form>
	              </v-card-text>
	              <v-card-actions>
	                <v-spacer></v-spacer>
	                <v-btn color="primary" :loading=loadingLogin v-on:click.prevent="login" >Login</v-btn>
	              </v-card-actions>
	            </v-card>
	          </v-flex>
	        </v-layout>

	  </v-app>
	</div>	
</template>


<script>
    export default {
    	data:function(){
    		return{
				user:{
    				email:"",
    				password:"",
				},
                typeofmsg: "alert-success",
                showMessage: false,
                message: "",
                loadingLogin: false,
    		}
    	},
    	methods:{
    		login(){
    			this.showMessage = false;
                this.loadingLogin=true;
                axios.post('api/login', this.user)
                    .then(response => {
                        this.$store.commit('setToken',response.data.access_token);
                        return axios.get('api/users/me');
                    })
                    .then(response => {
//                        this.$socket.emit('user_enter', response.data.data);
                        this.$store.commit('setUser',response.data.data);
                        this.$socket.emit('user_enter', response.data.data);
                        this.loadingLogin=false;
                    })
                    .catch(error => { 
                        this.$store.commit('clearUserAndToken');
                        this.typeofmsg = "alert-danger";
                        this.message = "Invalid credentials!";
                        this.showMessage = true;
                        this.loadingLogin=false;
                    })
    		}
    	},
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
