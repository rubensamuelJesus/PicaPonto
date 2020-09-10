<template>
  <div>
    <v-toolbar flat color="primary">
      <v-toolbar-title>BlockChain</v-toolbar-title>
      <v-divider
        class="mx-2"
        inset
        vertical
      ></v-divider>
      <v-btn fab color="white">
       <v-icon dark color="primary" @click="getBlockChain()">refresh</v-icon>
  </v-btn>
    </v-toolbar>
    <div>
    <h3>Choose a node!</h3>
    <v-select  v-model="roomEscolhido" @input="getBlockChain()" :items="roomsName" item-text='name' item-value='node'></v-select>
  </div>
    <vue-json-pretty :path="'res'" :data="blockchain"> </vue-json-pretty>

  </div>

</template>
<script>   
import VueJsonPretty from 'vue-json-pretty';
    export default {
      components: {
        VueJsonPretty
      },
      data:function(){
        return {
          dialog: false,
          blockchain: [],
          rooms:[],
          roomsName:[],
          roomEscolhido:'',
        }
      },
      methods:{
          getBlockChain(){
            this.blockchain= []
            console.log("roomEscolhido")
            console.log(this.roomEscolhido)
            axios.get('http://'+this.roomEscolhido+'/chain')
            .then(response => {
              console.log("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
              console.log(response);
              this.blockchain=response.data;
            
            }).catch((error) => {
              //console.log('error 3 ' + error);
            });
          },
          getRooms () {
          axios.get('api/rooms', this.$store.state.user.id)
            .then(response => {
              console.log("response   !!!!!!!!!!!")
              console.log(response)
              this.rooms=response.data;

              this.rooms.map(item=>{
                  this.roomsName.push({ 'id':item.id, 'name':item.name,'node':item.node})
                  console.log('itemitem')
                  console.log(item)

              })
            })
            .catch(error => { 
              console.log("error   acessssssssssss")
              console.log(error)
            })
          },

      },

    watch: {
      dialog (val) {
        val || this.close()
      }
    },
      mounted() {
       //this.getBlockChain();
        this. getRooms();
      }
  }
</script>