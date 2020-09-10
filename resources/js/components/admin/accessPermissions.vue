<template>
  <div>
    <v-toolbar flat color="primary">
      <v-toolbar-title>Access Permissions</v-toolbar-title>
      <v-divider
        class="mx-2"
        inset
        vertical
      ></v-divider>
      <v-btn fab color="white">
	     <v-icon dark color="primary" @click="getAccessPermissions()">refresh</v-icon>
	</v-btn>
      <v-spacer></v-spacer>

      <v-dialog v-model="dialog" max-width="500px">

        <v-card v-if="editedItem.rfid_id !== null">
          <v-card-title>
            <span class="headline">Update RFID</span>
          </v-card-title>

          <v-card-text>
            <v-container grid-list-md>
              <v-layout wrap>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="editedItem.user_id" label="User Id" disabled></v-text-field>
                  <v-text-field v-model="editedItem.rfid_id" label="Card Id" disabled></v-text-field>
                  <div id="app">
                      <label>Choose a user!</label>
                      <vue-single-select v-model="userNameEscolhido":options="namesUsers">   
                    </vue-single-select>
                  </div>
                </v-flex>
              </v-layout>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click="close">Cancel</v-btn>
            <v-btn color="blue darken-1" :loading=loadingLogin @click="saveCardID()">Save</v-btn>
          </v-card-actions>
        </v-card>

        <v-card v-else>
          <v-card-title>
            <span class="headline">Update Finger print</span>
          </v-card-title>

          <v-card-text>
            <v-container grid-list-md>
              <v-layout wrap>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="editedItem.user_id" label="User Id" disabled></v-text-field>
                  <v-text-field v-model="editedItem.finger_id" label="Finger Id" disabled></v-text-field>
                  <div id="app">
                      <label>Choose a user!</label>
                      <vue-single-select v-model="userNameEscolhido":options="namesUsers">   
                    </vue-single-select>
                  </div>
                </v-flex>
              </v-layout>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click="close">Cancel</v-btn>
            <v-btn color="blue darken-1" :loading=loadingLogin @click="saveFingerPrint()">Save</v-btn>
          </v-card-actions>
        </v-card>


      </v-dialog>

    </v-toolbar>
    <v-data-table
      :headers="headers"
      :items="accessPermissions"
      class="elevation-1"
    >
      <template v-slot:items="props">
        <!-- <td>{{ props.item.name }}</td> -->
        <td class="text-xs">{{ props.item.id }}</td>
        <td class="text-xs">{{ props.item.rfid_id }}</td>
        <td class="text-xs">{{ props.item.finger_id }}</td>
        <td class="text-xs">{{ props.item.user_id }}</td>
        <td class="text-xs">{{ props.item.name }}</td>
        <td class="justify-center layout px-0">
          <v-icon small class="mr-2" @click="editItem(props.item)">
            edit
          </v-icon>
          <v-icon
            small
            @click="deleteItem(props.item)"
          >
            delete
          </v-icon>
        </td>
      </template>
    </v-data-table>
  </div>
</template>

<script src="https://unpkg.com/vue@latest"></script>
<script src="https://unpkg.com/vue-single-select@latest"></script>
<script>
  export default {
    data: () => ({
      selected: null,
      dialog: false,
      dialog1: false,
      loadingLogin: false,
      userNameEscolhido: null,
      headers: [
       // { text: 'Access Type', value: 'access_type' },
        { text: 'Register Id ', value: 'id' },
        { text: 'Card Id', value: 'rfid_id' },
        { text: 'Finger Id', value: 'finger_id' },
        { text: 'User Id', value: 'user_id' },
        { text: 'User Name ', value: 'name' },
        //{ text: 'Protein (g)', value: 'protein' },
       // { text: 'Actions', value: 'name', sortable: false }
      ],
      accessPermissions: [],
      allUsers: [],
      namesUsers: [],
      usersID: [],
      idUserEscolhido: null,
      desserts: [],
      editedIndex: -1,
      editedItem: {
        //card_id: '',
        /*rfid_id: 0,
        id: 0,
        created_at: 0,
        protein: 0*/
      },
      defaultItem: {
       /* name: '',
        rfid_id: 0,
        id: 0,
        created_at: 0,
        protein: 0*/
      }
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      }
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
      dialog1 (val) {
        val || this.close()
      }
    },
    mounted () {
      this.getAccessPermissions();
    },

    methods: {
      getAccessPermissions () {
        axios.get('api/accessPermissions', this.$store.state.user.id)
                    .then(response => {
                    	console.log("response   acessssssssssss")
                    	console.log(response)
                    	this.accessPermissions=response.data;
                    })
                    .catch(error => { 
                    	console.log("error   acessssssssssss")
                    	console.log(error)
                    })
      },

      saveFingerPrint(){
        console.log(this.userNameEscolhido+"???????????????????????????????");
        console.log(this.editedItem.id+"xxxxxxxxxxxxxxxxxxxxxxx");
        for (let i = 0; i < 8/*this.allUsers.lenght*/; i++) {
            //console.log("asdsdsadassdaasd"+this.allUsers[i].name);
            if(this.namesUsers[i] ==  this.userNameEscolhido){
              

              console.log(this.usersID[i]+" ____________________");
              this.idUserEscolhido = this.usersID[i];
              //console.log(this.namesUsers[i]+"ºººººººººººººººººººººººººººººººº");
            //console.log(this.userNameEscolhido+"´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´");
            }
        }
        axios.post('api/updateFingerPrintAndRFID/'+this.editedItem.id, {
          idUserEscolhido: this.idUserEscolhido,
        })
        .then(response => {
          this.dialog = false
          this.getAccessPermissions();
        })
        .catch(error => { 
          console.log("error   acessssssssssss")
          console.log(error)
        })
      },

      saveCardID(){
        console.log(this.userNameEscolhido+"???????????????????????????????");
        for (let i = 0; i < 8/*this.allUsers.lenght*/; i++) {
            //console.log("asdsdsadassdaasd"+this.allUsers[i].name);
            if(this.namesUsers[i] ==  this.userNameEscolhido){
              

              console.log(this.usersID[i]+" ____________________");
              this.idUserEscolhido = this.usersID[i];
              //console.log(this.namesUsers[i]+"ºººººººººººººººººººººººººººººººº");
            //console.log(this.userNameEscolhido+"´´´´´´´´´´´´´´´´´´´´´´´´´´´´´´");
            }
        }
        axios.post('api/updateFingerPrintAndRFID/'+this.editedItem.id, {
          idUserEscolhido: this.idUserEscolhido,
        })
        .then(response => {
          this.dialog = false
          this.getAccessPermissions();
        })
        .catch(error => { 
          console.log("error   acessssssssssss")
          console.log(error)
        })
      },

      editItem (item) {
        this.editedIndex = this.accessPermissions.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true

        axios.get('api/allUsers')
        .then(response => {
          console.log("response   acessssssssssss")
          //console.log(response)
          this.allUsers=response.data;
          //console.log(this.allUsers);

          //this.namesUsers=response.data;
          //console.log(this.namesUsers);
          for (let i = 0; i < 8/*this.allUsers.lenght*/; i++) {
              //console.log("asdsdsadassdaasd"+this.allUsers[i].name);
              this.namesUsers[i] =  this.allUsers[i].name;
              this.usersID[i] = this.allUsers[i].id;
          }
          console.log(this.namesUsers+"*******************************");
          

          /*this.allUsers.map(function(value, key) {
           this.namesUsers.push(value);
         });*/

          //this.allUsers.forEach(function(element) {
            //console.log(element.name);
            //this.namesUsers[0] = element.name;
            //this.namesUsers.push("5");
            //console.log(this.namesUsers);
            //this.$parent.namesUsers.push(element.name); // what to push unto the rows array?
          //console.log(this.namesUsers);
          //this.namesUsers.push({namesUsers}); // what to push unto the rows array?

        })
        .catch(error => { 
          console.log("error   acessssssssssss")
          console.log(error)
        })
      },

      deleteItem (item) {
        const index = this.accessPermissions.indexOf(item)
        confirm('Are you sure you want to delete this Register ?') && this.accessPermissions.splice(index, 1)
        axios.delete('api/deletePermission/'+ item.id)
        .then(response => {
        	this.dialog = false
          this.getAccessPermissions();
        })
        .catch(error => { 
        	//console.log("error   acessssssssssss")
        	//console.log(error)
        })
      },

      close () {
        this.dialog = false
        setTimeout(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        }, 300)
      },     
       close1 () {
        this.dialog1 = false
        setTimeout(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        }, 300)
      },

      save () {
        let rfid_id=this.editedItem.rfid_id;
        let finger_id=this.editedItem.finger_id;
        this.loadingLogin=true;
        axios.patch('api/updateAccessId/'+ this.editedItem.id,{rfid_id,finger_id})
        .then(response => {
          if (this.editedIndex > -1) {
            Object.assign(this.accessPermissions[this.editedIndex], this.editedItem)
          } else {
            this.accessPermissions.push(this.editedItem)
          }
          this.loadingLogin=false;
          this.close()
        })
        .catch(error => { 
          console.log("error   acessssssssssss")
          this.loadingLogin=false;
          console.log(error)
        })
      },
      save1 () {
        let permission=this.editedItem;
        this.loadingLogin=true;
        axios.post('api/createPermission',permission)
        .then(response => {
          this.getAccessPermissions;
          
          this.loadingLogin=false;
          this.close();
        })
        .catch(error => { 
          console.log("error   acessssssssssss")
          this.loadingLogin=false;
          console.log(error)
        })
      }
    }
  }
</script>