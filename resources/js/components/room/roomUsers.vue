<template>
  <div>
    <v-toolbar flat color="primary">
      <v-toolbar-title>Room [{{this.room_id}}] Users</v-toolbar-title>
      <v-divider
        class="mx-2"
        inset
        vertical
      ></v-divider>
      <v-btn fab color="white">
       <v-icon dark color="primary" @click="getRoomUsers()">refresh</v-icon>
  </v-btn>
    </v-toolbar>
    <v-data-table
      :headers="headers"
      :items="roomUsers"
      class="elevation-1"
    >
      <template v-slot:items="props">
        <td class="text-xs">{{ props.item.room_id }}</td>
        <td class="text-xs">{{ props.item.user_id }}</td>
        <td class="text-xs">{{ props.item.name }}</td>
        <td class="text-xs">{{ props.item.entered_at }}</td>
        <td class="text-xs">{{ props.item.type }}</td>
      </template>
    </v-data-table>
  </div>

</template>

<script>
import { BarChart } from 'vue-morris'
  export default {
    data: () => ({
      dialog: false,
      headers: [
        { text: 'Room Id ', value: 'room_id' },
        { text: 'User Id', value: 'user_id' },
        { text: 'User Name', value: 'name' },
        { text: 'Entered', value: 'entered_at' },
        { text: 'Type', value: 'type' },
      ],
      room_id:'',
      roomUsers: [],
      desserts: [],
      editedIndex: -1,
      editedItem: {
        name: '',
        rfid_id: 0,
        id: 0,
        created_at: 0,
        protein: 0
      },
      defaultItem: {
        name: '',
        rfid_id: 0,
        id: 0,
        created_at: 0,
        protein: 0
      },
      chartData: [
       // { label: 'Red', value: 300 },
       // { label: 'Blue', value: 50 },
       // { label: 'Yellow', value: 100 }
      ],
      Left:0,
      Entered:0,
      Room_occupied:0,
      Unknown_ID:0,
    }),
    components: {
        BarChart,
      },
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
      this.room_id=this.$route.params.room_id
      this.getRoomUsers();
    },

    methods: {
      getRoomUsers () {
        axios.get('api/roomUsers/'+this.room_id).then(response=>{
          this.roomUsers=response.data
        }).catch(error=>{
          this.roomUsers=[]
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