<template>
  <v-container
    fill-height
    fluid
    grid-list-xl>
    <v-layout
      justify-center
      wrap
    >
      <v-flex
        xs12
        md8
      >

          <v-form>
            <v-container py-0>
              <v-layout wrap>
                <v-flex
                  xs12
                  md4
                >
                  <v-text-field
                    class="purple-input"
                    label="User Name" v-model="profileUser.name"
                  />
                </v-flex>
                <v-flex
                  xs12
                  md4
                >
                  <v-text-field
                    label="Email Address" v-model="profileUser.email"
                    class="purple-input"/>
                </v-flex>
                <v-flex
                  xs12
                  text-xs-left
                >
                  <v-btn class="mx-0 font-weight-light" color="success" :loading=loadingLogin @click="saveUser()">Update Profile</v-btn>
                </v-flex>
              </v-layout>
            </v-container>
          </v-form>
      </v-flex>
      <v-flex
        xs12
        md4
      >
          <v-avatar
            slot="offset"
            class="mx-auto d-block"
            size="130"
          >
            <img
              src="https://d2ln1xbi067hum.cloudfront.net/assets/default_user-abdf6434cda029ecd32423baac4ec238.png"
            >
          </v-avatar>
          <v-card-text class="text-xs-center">
            <h6 class="display-1 category text-gray font-weight-large mb-3">{{$store.state.user.type}}</h6>
            <h4 class="card-title font-weight-light">{{$store.state.user.name}}</h4>
            <p class="card-description font-weight-light">{{$store.state.user.type}} na plataforma IoT.</p>
          </v-card-text>
      </v-flex>
    </v-layout>
  </v-container>
</template>


<script type="text/javascript">    

    export default {
        data: function(){
            return { 
                profileUser: {
                    email:"",
                    name:"",
                    type:"",
                    id:"",
                },
                loadingLogin: false,
            }
        },
        methods: {
            getInformationFromLoggedUser() {
                Object.assign(this.profileUser,this.$store.state.user);
            },
            saveUser: function(){
                this.loadingLogin=true;
                axios.put('api/users/'+this.profileUser.id, this.profileUser)
                .then(response=>{
                    console.log("response")
                    console.log(response)
                    Object.assign(this.profileUser, response.data);
                    this.$store.commit('setUser',response.data);
                    //Object.assign(this.$store.state.user, response.data);
                    this.loadingLogin=false;
                });
            },
            cancelEdit: function(){
                axios.get('api/users/'+this.profileUser.id)
                .then(response=>{
                    Object.assign(this.profileUser, response.data.data);
                });
        }            
        },
        mounted() {
            this.getInformationFromLoggedUser();
        }        
    }
</script>