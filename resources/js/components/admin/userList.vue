<template>
  <div>
    <v-toolbar flat color="primary">
      <v-toolbar-title>User List</v-toolbar-title>
      <v-divider
        class="mx-2"
        inset
        vertical
      ></v-divider>
      <v-btn fab color="white">
       <v-icon dark color="primary" @click="getUsers()">refresh</v-icon>
  </v-btn>
      <v-spacer></v-spacer>
      <v-dialog v-model="dialog22" max-width="500px">
        <template v-slot:activator="{ on }">
          	<v-btn color="success" dark class="mb-2" @click="createUser()">Create User</v-btn>
        </template>
      </v-dialog>


      <v-dialog v-model="dialog" max-width="500px">
        <v-card>
          <v-card-title>
            <span class="headline">Create User</span>
          </v-card-title>
          <v-card-text>
            <v-container grid-list-md>
              <v-layout wrap>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="userName" label="User Name" ></v-text-field>
                  <v-text-field v-model="userEmail" label="User Email"></v-text-field>
                  <v-select item-value="last" v-model="userType" :items="usersType"></v-select>
                </v-flex>
              </v-layout>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click="create">Create</v-btn>
            <v-btn color="blue darken-1" flat @click="close">Cancel</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      

    </v-toolbar>
    <v-data-table
      :headers="headers"
      :items="users"
      class="elevation-1"
    >
      <template v-slot:items="props">
        <!-- <td>{{ props.item.name }}</td> -->
        <td class="text-xs">{{ props.item.id }}</td>
        <td class="text-xs">{{ props.item.name }}</td>
        <td class="text-xs">{{ props.item.email }}</td>
        <td class="text-xs">{{ props.item.type }}</td>
      </template>
    </v-data-table>
  </div>
</template>

<script>
  export default {
    data: () => ({
      dialog22: false,
      dialog: false,
      userName: '',
      userType: '',
      userEmail: '',
      usersType: ['admin','client'],
      headers: [
       // { text: 'Access Type', value: 'access_type' },
        { text: 'User Id', value: 'user_id' },
        { text: 'User Name ', value: 'id' },
        { text: 'User Email', value: 'rfid_id' },
        { text: 'Type', value: 'user_id' },
        //{ text: 'Protein (g)', value: 'protein' },
       // { text: 'Actions', value: 'name', sortable: false }
      ],
      users: [],
      desserts: [],
      editedIndex: -1,
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      }
    },

    watch: {
      dialog (val) {
        val || this.close()
      }
    },

    mounted () {
      this.getUsers();
    },

    methods: {
		getUsers () {
			axios.get('api/users', this.$store.state.user.id)
			.then(response => {
			  console.log("response   !!!!!!!!!!!")
			  console.log(response)
			  this.users=response.data;
			})
			.catch(error => { 
			  console.log("error   acessssssssssss")
			  console.log(error)
			})
		},
		create () {
			axios.post('api/createUser/', {
				name: this.userName,
				email: this.userEmail,
				type: this.userType,
			})
		.then(response => {
		  this.dialog = false
		   this.getUsers();
		})
		.catch(error => { 
		  console.log("error   acessssssssssss")
		  console.log(error)
		})
		},
      createUser(){
      	console.log("666666666666666666666666666666666666666666")
      	this.dialog = true
      },

      editItem (item) {
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.users.indexOf(item)
        confirm('Are you sure you want to delete this Register ?') && this.users.splice(index, 1)
        axios.delete('api/deleteAccess/'+ item.id)
        .then(response => {
          //console.log("response   acessssssssssss")
          //console.log(response)
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

      save () {
        if (this.editedIndex > -1) {
          Object.assign(this.desserts[this.editedIndex], this.editedItem)
        } else {
          this.desserts.push(this.editedItem)
        }
        this.close()
      }
    }
  }
</script>