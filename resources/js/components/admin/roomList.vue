<template>
  <div>
    <v-toolbar flat color="primary">
      <v-toolbar-title>Room list</v-toolbar-title>
      <v-divider
        class="mx-2"
        inset
        vertical
      ></v-divider>
      <v-btn fab color="white">
       <v-icon dark color="primary" @click="getRooms()">refresh</v-icon>
  </v-btn>
      <v-spacer></v-spacer>
      <v-dialog v-model="dialog" max-width="500px">
        <template v-slot:activator="{ on }">
          <v-btn color="success" dark class="mb-2" v-on="on">New Room</v-btn>
        </template>
        
        <v-card>
          <v-card-title>
            <span class="headline">{{ formTitle }}</span>
          </v-card-title>

          <v-card-text>
            <v-container grid-list-md>
              <v-layout wrap>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="defaultItem.name" label="Name"></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="defaultItem.controller_name" label="Controller"></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="defaultItem.node" label="Node"></v-text-field>
                </v-flex>
              </v-layout>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click="close">Cancel</v-btn>
            <v-btn color="blue darken-1" flat @click="save">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-dialog v-model="dialog1" max-width="500px">
        <v-card>
          <v-card-text>
            <v-container grid-list-md>
              <v-layout wrap>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="editedItem.name" label="Name"></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="editedItem.controller_name" label="Controller"></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="editedItem.node" label="Node"></v-text-field>
                </v-flex>
              </v-layout>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click="close">Cancel</v-btn>
            <v-btn color="blue darken-1" flat @click="update">Update</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-toolbar>
    <v-data-table
      :headers="headers"
      :items="rooms"
      class="elevation-1"
    >
      <template v-slot:items="props">
        <!-- <td>{{ props.item.name }}</td> -->
        <td class="text-xs">{{ props.item.id }}</td>
        <td class="text-xs">{{ props.item.name }}</td>
        <td class="text-xs">{{ props.item.controller_name }}</td>
        <td class="text-xs">{{ props.item.node }}</td>
        <td class="text-xs">{{ props.item.users_entered }}</td>
        <td class="text-xs">
          <v-btn color="blue darken-2" dark class="mb-2" v-on:click.native="getRoomUsers(props.item.id)" >Users</v-btn>
          <v-btn color="blue darken-2" dark class="mb-2" v-on:click.native="getRoomHistory(props.item.id)" >History</v-btn>
        </td>
        <td class="justify-center layout px-0">
          <v-icon
            small
            class="mr-2"
            @click="editItem(props.item)"
          >
            edit
          </v-icon>
        </td>
      </template>
    </v-data-table>
  </div>
</template>

<script>
  export default {
    data: () => ({
      dialog: false,
      dialog1: false,
      headers: [
        { text: 'Room Id', value: 'id' },
        { text: 'Room Name ', value: 'name' },
        { text: 'Room Controller', value: 'controller_name' },
        { text: 'Node', value: 'node' },
        { text: 'Occupied by', value: 'users_entered' },
        { text: 'Room History' },
      ],
      rooms: [],
      desserts: [],
      editedIndex: -1,
      editedItem: {
        name: '',
        controller_name: '',
        node: '',
      },
      defaultItem: {
        name: '',
        controller_name: '',
        node: '',
      }
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Room' : 'Edit Item'
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
      this.getRooms();
    },

    methods: {
      getRooms () {
        axios.get('api/rooms', this.$store.state.user.id)
                    .then(response => {
                      //console.log("response   !!!!!!!!!!!")
                      //console.log(response)
                      this.rooms=response.data;
                    })
                    .catch(error => { 
                      //console.log("error   acessssssssssss")
                      //console.log(error)
                    })
      },

      editItem (item) {
        this.editedIndex = this.rooms.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog1 = true
      },
      getRoomHistory (room_id) {
        this.$router.push({ name: 'roomHistory',params:{room_id:room_id} })
      },
      getRoomUsers (room_id) {
        this.$router.push({ name: 'roomUsers',params:{room_id:room_id} })
      },
      close () {
        this.dialog = false
        this.dialog1 = false
      },
      save () {
        axios.post('api/addRoom', this.defaultItem)
                    .then(response => {
                      //console.log("response   !!!!!!!!!!!")
                      //console.log(response)
                      this.getRooms()
                      this.rooms.map(item=>{
                        if (item.node!=null) {
                          axios.get('http://'+item.node+'/nodes/update')
                        }
                      })
                    })
                    .catch(error => { 
                      //console.log("error   acessssssssssss")
                      //console.log(error)
                    })
                    this.defaultItem={
                      name: '',
                      controller_name: '',
                      node: '',
                    }
        this.close()
      },
      update () {
        axios.patch('api/updateRoom/'+ this.editedItem.id,this.editedItem)
                    .then(response => {
                      this.getRooms()
                    })
                    .catch(error => { 
                      //console.log("error   acessssssssssss")
                      //console.log(error)
                    })
        this.close()
      }
    }
  }
</script>