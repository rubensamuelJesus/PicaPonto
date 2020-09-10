<template>
  <div>
    <v-toolbar flat color="primary">
      <v-toolbar-title>Access History</v-toolbar-title>
      <v-divider
        class="mx-2"
        inset
        vertical
      ></v-divider>
      <v-btn fab color="white">
	     <v-icon dark color="primary" @click="getAccessHistory()">refresh</v-icon>
	</v-btn>

    </v-toolbar>
    <v-data-table
      :headers="headers"
      :items="accessHistory"
      class="elevation-1"
    >
      <template v-slot:items="props">
        <!-- <td>{{ props.item.name }}</td> -->
        <td class="text-xs">{{ props.item.id }}</td>
        <td class="text-xs">{{ props.item.room_id }}</td>
        <td class="text-xs">{{ props.item.state }}</td>
        <td class="text-xs">{{ props.item.finger_id }}</td>
        <td class="text-xs">{{ props.item.rfid_id }}</td>
        <td class="text-xs">{{ props.item.created_at }}</td>
        <td class="justify-center layout px-0">
          <v-icon
            small
            class="mr-2"
            @click="editItem(props.item)"
          >
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
    <bar-chart 
      id="bar" 
      :data="chartData" 
      colors='[ "#FF6384", "#36A2EB", "#FFCE56" ]' 
      resize="true" xkey="label">
    </bar-chart>
  </div>
</template>

<script>
import { BarChart } from 'vue-morris'
  export default {
    data: () => ({
      dialog: false,
      headers: [
       // { text: 'Access Type', value: 'access_type' },
        { text: 'Register Id ', value: 'id' },
        { text: 'Room Id', value: 'room_id' },
        { text: 'Action', value: 'state' },
        { text: 'Finger Id', value: 'finger_id' },
        { text: 'Card Id', value: 'rfid_id' },
        { text: 'Time Registered', value: 'created_at' },
        //{ text: 'Protein (g)', value: 'protein' },
       // { text: 'Actions', value: 'name', sortable: false }
      ],
      accessHistory: [],
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
      this.getAccessHistory();
    },

    methods: {
      getAccessHistory () {
        axios.get('api/accessHistory', this.$store.state.user.id)
                    .then(response => {
                    	console.log("response   acessssssssssss")
                    	console.log(response)
                    	this.accessHistory=response.data;
                      this.accessHistory.map(item=>{
                        if(item.state=='Left')
                          this.Left++
                        if(item.state=='Entered')
                          this.Entered++
                        if(item.state=='Room occupied')
                          this.Room_occupied++
                        if(item.state=='Unknown ID')
                          this.Unknown_ID++
                      })
                      this.chartData.push({label: 'Left', value : this.Left},
                                          {label: 'Entered', value : this.Entered},
                                          {label: 'Room occupied', value : this.Room_occupied},
                                          {label: 'Unknown ID', value : this.Unknown_ID})
                      console.log("!!!!!!!!!!!!!!!!!!!!")
                      console.log(this.chartData)
                    })
                    .catch(error => { 
                    	console.log("error   acessssssssssss")
                    	console.log(error)
                    })
      },

      editItem (item) {
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.accessHistory.indexOf(item)
        confirm('Are you sure you want to delete this Register ?') && this.accessHistory.splice(index, 1)
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